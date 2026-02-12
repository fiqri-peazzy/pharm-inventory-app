@extends('errors.illustrated')

@section('title', __('Akses Ditolak'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.'))
