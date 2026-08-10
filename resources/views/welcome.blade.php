@extends('layouts.app')

@section('content')
    @include('pages.dashboard')
    @include('pages.absensi')
    @include('pages.riwayat')
    @include('pages.laporan')
    @include('pages.guru')
    @include('pages.siswa')
    @include('pages.kelas')
    @include('pages.mapel')
    @include('pages.pengaturan')
@endsection
