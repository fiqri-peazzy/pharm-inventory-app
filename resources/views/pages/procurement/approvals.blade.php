@extends('layouts.app')

@section('title', 'Persetujuan Pengadaan (PR) - POS Pharm')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white uppercase">Approval Purchase Request</h2>
        <p class="text-sm text-gray-500">Tinjau dan berikan persetujuan untuk pengajuan kebutuhan farmasi.</p>
    </div>

    @livewire('procurement.purchase-request-approval')
@endsection
