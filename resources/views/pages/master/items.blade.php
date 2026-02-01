@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Daftar Obat & BMHP" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        @livewire('master.item-index')
    </div>
@endsection
