@extends('layouts.admin')

@section('title', 'Tambah Produk | Minimarket')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Produk</a></li>
                <li class="breadcrumb-item" aria-current="page">Tambah</li>
            </ul>
            <div class="page-header-title"><h2 class="mb-0">Tambah Produk</h2></div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                @include('products._form')
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('products.index') }}" class="btn btn-light-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
@endsection