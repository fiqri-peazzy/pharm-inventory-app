@extends('layouts.app')

@section('content')
<div class="p-6">
    <livewire:clinical.prescription-form :prescriptionId="$id ?? null" />
</div>
@endsection
