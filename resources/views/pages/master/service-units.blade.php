@extends('layouts.app')

@section('title', 'Unit Layanan')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-black tracking-tight text-gray-900 dark:text-white uppercase">Master Unit Layanan</h2>
        <p class="text-sm text-gray-500">Kelola poli, ruangan, dan instalasi yang menggunakan layanan farmasi.</p>
    </div>

    @livewire('master.service-unit-index')
@endsection
