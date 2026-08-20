@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
    <x-common.page-breadcrumb pageTitle="Profil Saya" />
    @livewire('settings.profile-page')
@endsection
