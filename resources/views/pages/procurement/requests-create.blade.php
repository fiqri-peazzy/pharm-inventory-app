@extends('layouts.app')

@section('title', 'Buat Permintaan Barang (PR) - POS Pharm')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white uppercase">Buat Purchase Request (PR)</h2>
        <p class="text-sm text-gray-500">Ajukan kebutuhan perbekalan farmasi untuk periode tertentu.</p>
    </div>

    @livewire('procurement.purchase-request-form')
@endsection
