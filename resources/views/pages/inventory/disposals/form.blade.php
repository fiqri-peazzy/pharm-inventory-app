@extends('layouts.app')

@section('title', 'Form Pemusnahan Barang')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <livewire:inventory.disposal-form :disposalId="$disposalId ?? null" />
    </div>
@endsection
