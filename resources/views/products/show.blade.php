@extends('layouts.admin')

@section('title', 'Detail Produk | Minimarket')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
                <li class="breadcrumb-item" aria-current="page">Detail</li>
            </ul>
            <div class="page-header-title"><h2 class="mb-0">Detail Produk</h2></div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0">{{ $product->name }}</h5>
            <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-primary">Edit</a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-borderless align-middle mb-0">
                <tbody>
                    <tr><th scope="row" class="w-25">Nama</th><td>{{ $product->name }}</td></tr>
                    <tr><th scope="row">Kategori</th><td>{{ $product->category ?: '-' }}</td></tr>
                    <tr><th scope="row">Deskripsi</th><td>{{ $product->description ?: '-' }}</td></tr>
                    <tr><th scope="row">Harga</th><td>Rp {{ number_format((float) $product->price, 2, ',', '.') }}</td></tr>
                    <tr><th scope="row">Stock</th><td>{{ number_format($product->stock) }}</td></tr>
                    <tr><th scope="row">Image</th><td>{{ $product->image ?: '-' }}</td></tr>
                    <tr>
                        <th scope="row">Status</th>
                        <td><span class="badge {{ $product->is_active ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary' }}">{{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></td>
                    </tr>
                    <tr><th scope="row">Dibuat</th><td>{{ $product->created_at?->format('d M Y H:i') ?? '-' }}</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><a href="{{ route('products.index') }}" class="btn btn-light-secondary">Kembali</a></div>
    </div>
@endsection