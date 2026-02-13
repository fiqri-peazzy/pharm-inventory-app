@extends('layouts.app')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Neraca Saldo</h1>
            <p class="text-sm text-gray-500">Ringkasan saldo akhir semua akun.</p>
        </div>

        @livewire('accounting.reports.trial-balance')
    </div>
@endsection
