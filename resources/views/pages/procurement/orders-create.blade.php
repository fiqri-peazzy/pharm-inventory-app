@extends('layouts.app')

@section('title', 'Buat Pesanan Barang (PO) - POS Pharm')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white uppercase">Buat Purchase Order (PO)</h2>
        <p class="text-sm text-gray-500">Buat pesanan barang ke supplier baik melalui PR Approved maupun Manual.</p>
    </div>

    @livewire('procurement.purchase-order-form')
@endsection
