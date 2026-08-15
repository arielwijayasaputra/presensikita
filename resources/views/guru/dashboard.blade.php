@extends('layouts.app')

@section('content')
    @include('guru.pages.dashboard')
    @include('guru.pages.jurnal_absensi')
    @include('guru.pages.riwayat')
    @include('guru.pages.profil')
    @include('admin.pages.laporan')
@endsection
