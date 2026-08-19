@extends('layouts.app')

@section('title', 'Form Permintaan Ruangan')

@section('content')
<div class="p-6">
    <livewire:clinical.ward-request-form :requestId="$id ?? null" />
</div>
@endsection
