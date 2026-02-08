@extends('layouts.app')

@section('content')
<div class="p-6">
    <livewire:inventory.distribution-receive :distributionId="$distributionId" />
</div>
@endsection
