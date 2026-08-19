@extends('layouts.app')

@section('title', 'Jurnal Akuntansi')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Jurnal Akuntansi</h1>
            <p class="text-sm text-gray-500">Manajemen pencatatan jurnal umum, penyesuaian, dan penutup.</p>
        </div>

        @livewire('accounting.journal-index')
    </div>
@endsection
