@extends('layouts.app')

@section('title', 'Master Kategori Item')

@section('content')
    <x-common.page-breadcrumb pageTitle="Kategori Item" />

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        @livewire('master.item-category-index')
    </div>
@endsection
