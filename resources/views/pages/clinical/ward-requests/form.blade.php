@extends('layouts.app')

@section('content')
<div class="p-6">
    <livewire:clinical.ward-request-form :requestId="$id ?? null" />
</div>
@endsection
