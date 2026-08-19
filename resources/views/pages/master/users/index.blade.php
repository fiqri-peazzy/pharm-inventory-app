@extends('layouts.app')

@section('title', 'Manajemen Pengguna')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
            <p class="text-sm text-gray-500">Kelola data pengguna, role, dan akses gudang.</p>
        </div>

        @livewire('master.user-index')
    </div>
@endsection
