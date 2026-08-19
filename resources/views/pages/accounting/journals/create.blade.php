@extends('layouts.app')

@section('title', 'Tambah Jurnal')

@section('content')
    <div class="p-6">
        @livewire('accounting.journal-form')
    </div>
@endsection
