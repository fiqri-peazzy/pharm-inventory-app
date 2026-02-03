@extends('layouts.app')

@section('title', 'Pesanan Barang (PO) - POS Pharm')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white uppercase">Purchase Order (PO)</h2>
        <p class="text-sm text-gray-500">Kelola pesanan barang ke supplier dan pantau status pengiriman.</p>
    </div>

    @livewire('procurement.purchase-order-index')
@endsection
