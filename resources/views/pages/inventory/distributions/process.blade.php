@extends('layouts.app')

@section('title', 'Proses Distribusi')

@section('content')
<div class="p-6">
    <livewire:inventory.distribution-process :distributionId="$distributionId" />
</div>
@endsection
