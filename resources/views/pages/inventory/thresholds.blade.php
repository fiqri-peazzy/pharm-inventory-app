@extends('layouts.app')

@section('title', 'Optimasi Batas Stok')

@section('content')
<div class="p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Optimasi Batas Stok</h1>
            <p class="text-sm text-gray-500">Analisis pola pemakaian dan saran batas stok minimum/maksimum otomatis per depo.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="Livewire.dispatch('refreshSuggestions')" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all flex items-center gap-2">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 4v6h-6"></path><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                Refresh Data
            </button>
        </div>
    </div>

    @livewire('inventory.stock-threshold-optimization')
</div>
@endsection
