@extends('layouts.app')

@section('title', 'Dispensing Resep')

@section('content')
<div class="p-6">
    <livewire:clinical.prescription-dispense :prescriptionId="$prescriptionId" />
</div>
@endsection
