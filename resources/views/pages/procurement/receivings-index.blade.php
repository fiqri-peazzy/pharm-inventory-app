@extends('layouts.app')

@section('title', 'Daftar Penerimaan Barang')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Dropping Outgoing</h1>
            <p class="text-sm text-gray-500">Manajemen rincian penerimaan barang masuk dari supplier.</p>
        </div>

        @livewire('procurement.receiving-index')
    </div>
@endsection
