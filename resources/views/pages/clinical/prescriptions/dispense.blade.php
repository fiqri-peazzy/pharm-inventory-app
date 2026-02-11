@extends('layouts.app')

@section('content')
<div class="p-6">
    <livewire:clinical.prescription-dispense :prescriptionId="$prescriptionId" />
</div>
@endsection
