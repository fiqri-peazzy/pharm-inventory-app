@extends('layouts.app')

@section('title', 'Detail Jurnal')

@section('content')
    <div class="p-6">
        @livewire('accounting.journal-form', ['journalId' => $id, 'isViewOnly' => true])
    </div>
@endsection
