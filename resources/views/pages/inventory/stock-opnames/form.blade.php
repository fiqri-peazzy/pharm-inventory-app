@extends('layouts.app')

@section('content')
    <livewire:inventory.stock-opname-form :opnameId="$opnameId ?? null" />
@endsection
