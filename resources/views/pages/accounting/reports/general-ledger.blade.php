@extends('layouts.app')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Buku Besar</h1>
            <p class="text-sm text-gray-500">Rincian mutasi transaksi per akun.</p>
        </div>

        @livewire('accounting.reports.general-ledger')
    </div>
@endsection
