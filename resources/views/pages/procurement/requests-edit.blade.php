@extends('layouts.app')

@section('title', 'Ubah Permintaan Barang (PR) - POS Pharm')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white uppercase">Ubah Purchase Request (PR)</h2>
        <p class="text-sm text-gray-500">Sesuaikan item atau jumlah permintaan sebelum diajukan (submit).</p>
    </div>

    @livewire('procurement.purchase-request-form', ['requestId' => $id])
@endsection
