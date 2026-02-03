@extends('layouts.app')

@section('title', 'Ubah Pesanan Barang (PO) - POS Pharm')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white uppercase">Ubah Purchase Order (PO)</h2>
        <p class="text-sm text-gray-500">Sesuaikan rincian pesanan, harga, atau diskon sebelum dikirim ke supplier.</p>
    </div>

    @livewire('procurement.purchase-order-form', ['orderId' => $id])
@endsection
