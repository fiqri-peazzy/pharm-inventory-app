@extends('layouts.app')

@section('title', 'Terima Distribusi')

@section('content')
<div class="p-6">
    <livewire:inventory.distribution-receive :distributionId="$distributionId" />
</div>
@endsection
