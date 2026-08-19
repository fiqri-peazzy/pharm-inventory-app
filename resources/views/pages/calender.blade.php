@extends('layouts.app')

@section('title', 'Kalender')

@section('content')
    <x-common.page-breadcrumb pageTitle="Calender" />
    <x-calender-area />
@endsection
