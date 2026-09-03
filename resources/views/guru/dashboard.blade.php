@extends('layouts.app')

@section('content')
    @include('guru.pages.dashboard')
    @include('guru.pages.jadwal-mengajar')
    @include('guru.pages.izin-guru')
    @include('guru.pages.jurnal_absensi')
    @include('guru.pages.riwayat')
    @include('guru.pages.profil')
    @include('guru.pages.buat-laporan')
@endsection
