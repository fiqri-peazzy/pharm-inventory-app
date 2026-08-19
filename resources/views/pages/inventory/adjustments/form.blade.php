@extends('layouts.app')

@section('title', 'Form Adjustment Stok')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <livewire:inventory.stock-adjustment-form :adjustmentId="$adjustmentId ?? null" />
    </div>
@endsection
