@extends('layouts.app')

@section('title', 'Form Stock Opname')

@section('content')
    <livewire:inventory.stock-opname-form :opnameId="$opnameId ?? null" />
@endsection
