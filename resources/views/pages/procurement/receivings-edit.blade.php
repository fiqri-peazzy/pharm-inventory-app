@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $id ?? null ? 'Edit' : 'Tambah' }} Penerimaan Barang</h1>
            <p class="text-sm text-gray-500">Input rincian item, nomor batch, dan tanggal kadaluarsa dari supplier.</p>
        </div>
        <a href="{{ route('procurement.receivings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Kembali
        </a>
    </div>

    @livewire('procurement.receiving-form', ['receivingId' => $id ?? null])
</div>
@endsection
