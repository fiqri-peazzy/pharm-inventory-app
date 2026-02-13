@extends('layouts.app')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Chart of Accounts</h1>
            <p class="text-sm text-gray-500">Manajemen daftar akun akuntansi (Bagan Akun).</p>
        </div>

        @livewire('accounting.coa-index')
    </div>
@endsection
