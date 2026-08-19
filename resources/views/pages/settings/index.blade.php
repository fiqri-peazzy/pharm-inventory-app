@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
    <div class="p-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
            <p class="text-sm text-gray-500">Konfigurasi identitas aplikasi dan lingkungan.</p>
        </div>

        @livewire('settings.settings-page')
    </div>
@endsection
