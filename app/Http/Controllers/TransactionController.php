<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'product_code', 'barcode', 'price', 'stock']);

        $nextTransactionId = (int) Transaction::max('id') + 1;
        $transactionNumber = 'TRX-'.now()->format('Ymd').'-'.str_pad((string) $nextTransactionId, 3, '0', STR_PAD_LEFT);

return view('kasir.index', compact('products', 'transactionNumber'));    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'other_fee' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris,debit,credit,e_wallet,transfer'],
        ], [
            'items.required' => 'Keranjang masih kosong.',
            'items.min' => 'Tambahkan minimal satu produk.',
            'paid_amount.required' => 'Masukkan jumlah uang dibayar.',
        ]);

        $transaction = DB::transaction(function () use ($validated) {
            $lines = [];
            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $product = Product::query()->whereKey($item['product_id'])->lockForUpdate()->first();

                if (! $product || ! $product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => 'Salah satu produk sudah tidak aktif. Muat ulang halaman dan coba lagi.',
                    ]);
                }

                if ($product->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi. Tersedia {$product->stock}.",
                    ]);
                }

                $lineSubtotal = (float) $product->price * (int) $item['qty'];
                $subtotal += $lineSubtotal;
                $lines[] = [
                    'product' => $product,
                    'qty' => (int) $item['qty'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discountPercent = (float) ($validated['discount_percent'] ?? 0);
            $fixedDiscount = (float) ($validated['discount_amount'] ?? 0);
            $discount = min($subtotal, ($subtotal * $discountPercent / 100) + $fixedDiscount);
            $tax = max(0, $subtotal - $discount) * (float) ($validated['tax_percent'] ?? 0) / 100;
            $otherFee = (float) ($validated['other_fee'] ?? 0);
            $grandTotal = $subtotal - $discount + $tax + $otherFee;
            $paidAmount = (float) $validated['paid_amount'];

            if ($paidAmount < $grandTotal) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Uang dibayar masih kurang dari total transaksi.',
                ]);
            }

            $transaction = Transaction::create([
                'transaction_number' => 'TRX-'.now()->format('Ymd').'-'.Str::uuid(),
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => $tax,
                'other_fee' => $otherFee,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'change_amount' => $paidAmount - $grandTotal,
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
            ]);

            $transaction->update([
                'transaction_number' => 'TRX-'.$transaction->created_at->format('Ymd').'-'.str_pad((string) $transaction->id, 3, '0', STR_PAD_LEFT),
            ]);

            foreach ($lines as $line) {
                $product = $line['product'];
                $transaction->details()->create([
                    'product_id' => $product->id,
                    'product_code' => $product->product_code,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'qty' => $line['qty'],
                    'discount' => 0,
                    'subtotal' => $line['subtotal'],
                ]);
                $product->decrement('stock', $line['qty']);
            }

            return $transaction;
        }, 3);

        return redirect()
            ->route('kasir.index')
            ->with('completedTransaction', $transaction->load('details'));
    }
}