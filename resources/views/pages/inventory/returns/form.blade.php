@extends('layouts.app')

@section('content')
    <livewire:inventory.return-form :returnId="$returnId ?? null" />
@endsection
