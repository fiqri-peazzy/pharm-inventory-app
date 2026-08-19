@extends('layouts.app')

@section('title', 'Form Retur Barang')

@section('content')
    <livewire:inventory.return-form :returnId="$returnId ?? null" />
@endsection
