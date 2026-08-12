@extends('layouts.app')

@section('content')
    @include('admin.pages.dashboard')
    @include('admin.pages.absensi')
    @include('admin.pages.riwayat')
    @include('admin.pages.laporan')
    @include('admin.pages.guru')
    @include('admin.pages.siswa')
    @include('admin.pages.kelas')
    @include('admin.pages.mapel')
    @include('admin.pages.pengaturan')
@endsection
