@extends('layouts.admin')

@section('title', 'Dashboard Admin | Minimarket')

@section('content')
    <div class="page-header">
        <div class="page-block">
            <div class="row align-items-center">
                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item" aria-current="page">Beranda</li>
                    </ul>
                </div>
                <div class="col-md-12">
                    <div class="page-header-title"><h2 class="mb-0">Dashboard Admin</h2></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            Selamat datang, {{ auth()->user()->name }}.
        </div>
    </div>
@endsection