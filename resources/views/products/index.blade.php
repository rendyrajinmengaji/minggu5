@extends('layouts.admin')

@section('title', 'Produk | Minimarket')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item" aria-current="page">Produk</li>
                    </ul>
                </div>
                <div class="col-md-12 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="page-header-title"><h2 class="mb-0">Produk</h2></div>
                    <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="ti ti-plus me-1"></i>Tambah produk</a>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Product</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Stock</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td>{{ $products->firstItem() + $loop->index }}</td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if ($product->description)
                                    <small class="d-block text-muted">{{ \Illuminate\Support\Str::limit($product->description, 70) }}</small>
                                @endif
                            </td>
                            <td>{{ $product->category ?: '-' }}</td>
                            <td>Rp {{ number_format((float) $product->price, 2, ',', '.') }}</td>
                            <td>{{ number_format($product->stock) }}</td>
                            <td>
                                <span class="badge {{ $product->is_active ? 'bg-light-success text-success' : 'bg-light-secondary text-secondary' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('products.show', $product) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                <a href="{{ route('products.edit', $product) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-4 text-center text-muted">Belum ada produk.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $products->links() }}</div>
        </div>
    </div>
@endsection