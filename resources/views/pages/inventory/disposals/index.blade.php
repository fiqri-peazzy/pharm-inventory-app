@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Retur & Pemusnahan (Disposal)</h1>
            <p class="text-sm text-gray-500">Manajemen stok rusak, expired, atau pengembalian barang ke supplier.</p>
        </div>
    </div>

    @livewire('inventory.disposal-index')
</div>
@endsection
