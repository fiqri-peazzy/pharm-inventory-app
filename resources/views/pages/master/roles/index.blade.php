@extends('layouts.app')

@section('title', 'Manajemen Role & Hak Akses')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Role & Permission</h1>
            <p class="text-sm text-gray-500">Daftar hak akses sistem per role.</p>
        </div>

        @livewire('master.role-index')
    </div>
@endsection
