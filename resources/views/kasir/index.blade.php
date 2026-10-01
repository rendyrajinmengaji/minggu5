<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kasir POS | {{ config('app.name', 'Minimarket') }}</title>
    <link rel="stylesheet" href="{{ asset('css/chasier.css') }}">
    <script src="{{ asset('js/chasier.js') }}" defer></script>
</head>
<body class="pos-page">
    <header class="topbar">
        <a class="store-brand" href="{{ route('kasir.index') }}" aria-label="Kasir minimarket">
            <span class="brand-mark">M</span>
            <span><strong>MINIMARKET</strong><small>POINT OF SALE</small></span>
        </a>
        <div class="topbar-meta">
            <div class="transaction-meta"><span>NO. TRANSAKSI</span><strong>{{ $transactionNumber }}</strong></div>
            <div class="transaction-meta"><span>TANGGAL & WAKTU</span><strong id="currentDateTime"></strong></div>
            <div class="operator-meta"><span class="online-dot"></span><span>{{ auth()->user()->name }}<small>Kasir aktif</small></span></div>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-button" type="submit">Keluar</button></form>
        </div>
    </header>

    <main class="pos-main">
        <div class="page-heading">
            <div><p class="eyebrow">MEJA KASIR <span> / </span> TRANSAKSI BARU</p><h1>Penjualan</h1></div>
            <button class="restore-button" id="restoreTransaction" type="button" hidden>Ambil transaksi tertahan</button>
        </div>

        @if ($errors->any())
            <div class="alert alert-error" role="alert">
                <strong>Transaksi belum dapat diproses.</strong>
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        @if (session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif

        @if ($completedTransaction = session('completedTransaction'))
            <section class="receipt" id="receipt" aria-label="Struk transaksi">
                <div class="receipt-head"><span class="receipt-mark">M</span><strong>MINIMARKET</strong><small>STRUK PEMBELIAN</small></div>
                <div class="receipt-meta"><span>{{ $completedTransaction->transaction_number }}</span><span>{{ $completedTransaction->created_at->format('d/m/Y H:i') }}</span><span>Kasir: {{ auth()->user()->name }}</span></div>
                <div class="receipt-lines">
                    @foreach ($completedTransaction->details as $detail)
                        <div><span>{{ $detail->product_name }}<small>{{ $detail->qty }} x Rp {{ number_format((float) $detail->price, 0, ',', '.') }}</small></span><strong>Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</strong></div>
                    @endforeach
                </div>
                <div class="receipt-totals">
                    <span>Subtotal</span><strong>Rp {{ number_format((float) $completedTransaction->subtotal, 2, ',', '.') }}</strong>
                    <span>Diskon</span><strong>- Rp {{ number_format((float) $completedTransaction->discount, 2, ',', '.') }}</strong>
                    <span>Pajak / PPN</span><strong>Rp {{ number_format((float) $completedTransaction->tax, 2, ',', '.') }}</strong>
                    <span>Biaya lain</span><strong>Rp {{ number_format((float) $completedTransaction->other_fee, 2, ',', '.') }}</strong>
                    <span>Grand total</span><strong>Rp {{ number_format((float) $completedTransaction->grand_total, 2, ',', '.') }}</strong>
                    <span>Metode bayar</span><strong>{{ strtoupper(str_replace('_', ' ', $completedTransaction->payment_method)) }}</strong>
                    <span>Dibayar</span><strong>Rp {{ number_format((float) $completedTransaction->paid_amount, 2, ',', '.') }}</strong>
                    <span>Kembalian</span><strong>Rp {{ number_format((float) $completedTransaction->change_amount, 2, ',', '.') }}</strong>
                </div>
                <p>Terima kasih telah berbelanja.</p>
                <button class="print-again" type="button" onclick="window.print()">Cetak ulang</button>
            </section>
        @endif

        <form id="cashierForm" class="pos-grid" method="POST" action="{{ route('kasir.transactions.store') }}" novalidate>
            @csrf
            <section class="workspace" aria-label="Pencarian dan keranjang belanja">
                <div class="search-panel">
                    <label class="search-label" for="productSearch"><span class="search-glyph" aria-hidden="true">⌕</span><span class="sr-only">Cari produk</span></label>
                    <input id="productSearch" type="search" placeholder="Cari nama, kode, atau scan barcode..." autocomplete="off" aria-controls="productResults" aria-expanded="false">
                    <kbd>F2</kbd>
                    <div id="productResults" class="product-results" role="listbox" hidden></div>
                </div>

                <div class="cart-heading"><div><p class="eyebrow">DAFTAR BARANG</p><h2>Keranjang <span id="cartCount">0 item</span></h2></div><span class="cart-hint">Pilih produk untuk mulai</span></div>
                <div class="cart-table-wrap">
                    <table class="cart-table">
                        <thead><tr><th>PRODUK</th><th>HARGA</th><th>JUMLAH</th><th>TOTAL</th><th><span class="sr-only">Aksi</span></th></tr></thead>
                        <tbody id="cartRows"></tbody>
                    </table>
                    <div class="empty-cart" id="emptyCart"><span class="empty-icon" aria-hidden="true">＋</span><strong>Keranjang masih kosong</strong><span>Cari atau scan barang untuk menambahkannya.</span></div>
                </div>
                <div class="workspace-footer"><span><i class="keyboard-key">F2</i> Cari barang</span><span><i class="keyboard-key">F4</i> Bayar</span><span><i class="keyboard-key">ESC</i> Batalkan</span></div>
            </section>

            <aside class="payment-panel" aria-label="Ringkasan pembayaran">
                <div class="payment-heading"><div><p class="eyebrow">RINGKASAN</p><h2>Pembayaran</h2></div><span class="secure-label"><i></i> SIAP</span></div>
                <div class="summary-lines">
                    <div class="summary-row"><span>Subtotal</span><strong id="subtotalDisplay">Rp 0</strong></div>
                    <div class="summary-control"><label for="discountPercent">Diskon (%)</label><div class="input-suffix"><input id="discountPercent" name="discount_percent" type="number" min="0" max="100" step="0.01" value="{{ old('discount_percent', 0) }}"><span>%</span></div></div>
                    <div class="summary-control"><label for="discountAmount">Diskon (Rp)</label><div class="input-prefix"><span>Rp</span><input id="discountAmount" name="discount_amount" type="number" min="0" step="1" value="{{ old('discount_amount', 0) }}"></div></div>
                    <div class="summary-control"><label for="taxPercent">Pajak / PPN (%)</label><div class="input-suffix"><input id="taxPercent" name="tax_percent" type="number" min="0" max="100" step="0.01" value="{{ old('tax_percent', 11) }}"><span>%</span></div></div>
                    <div class="summary-control"><label for="otherFee">Biaya lain (Rp)</label><div class="input-prefix"><span>Rp</span><input id="otherFee" name="other_fee" type="number" min="0" step="1" value="{{ old('other_fee', 0) }}"></div></div>
                </div>
                <div class="grand-total"><span>GRAND TOTAL</span><strong id="grandTotalDisplay">Rp 0</strong></div>
                <div class="paid-block">
                    <label for="paidAmount">Uang dibayar</label>
                    <div class="paid-input"><span>Rp</span><input id="paidAmount" name="paid_amount" type="number" min="0" step="1" value="{{ old('paid_amount', '') }}" placeholder="0" required></div>
                    <div class="change-line"><span id="changeLabel">Kembalian</span><strong id="changeDisplay">Rp 0</strong></div>
                    <p id="paymentMessage" class="payment-message" role="status"></p>
                </div>
                <div class="method-block"><label for="paymentMethod">Metode pembayaran</label><select id="paymentMethod" name="payment_method" required>
                    <option value="cash" @selected(old('payment_method', 'cash') === 'cash')>Tunai</option>
                    <option value="qris" @selected(old('payment_method') === 'qris')>QRIS</option>
                    <option value="debit" @selected(old('payment_method') === 'debit')>Debit</option>
                    <option value="credit" @selected(old('payment_method') === 'credit')>Kredit</option>
                    <option value="e_wallet" @selected(old('payment_method') === 'e_wallet')>E-Wallet</option>
                    <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer</option>
                </select></div>
                <div class="action-stack">
                    <button class="pay-button" id="payButton" type="submit"><span>BAYAR & CETAK</span><kbd>F4</kbd></button>
                    <div class="secondary-actions"><button class="hold-button" id="holdTransaction" type="button"><span aria-hidden="true">Ⅱ</span> Tahan Transaksi</button><button class="cancel-button" id="cancelTransaction" type="button" title="Batalkan transaksi (Esc)"><span aria-hidden="true">×</span></button></div>
                </div>
                <p class="operator-note">Dilayani oleh <strong>{{ auth()->user()->name }}</strong><span>·</span> Transaksi tersimpan otomatis saat pembayaran</p>
            </aside>
        </form>
    </main>

    <script>
        window.cashierConfig = {
            products: @json($products),
            initialItems: @json(old('items', [])),
            transactionNumber: @json($transactionNumber),
            completed: @json((bool) session('completedTransaction')),
        };
    </script>
</body>
</html>