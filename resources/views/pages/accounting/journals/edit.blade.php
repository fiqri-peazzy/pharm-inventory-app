@extends('layouts.app')

@section('content')
    <div class="p-6">
        @livewire('accounting.journal-form', ['journalId' => $id])
    </div>
@endsection
