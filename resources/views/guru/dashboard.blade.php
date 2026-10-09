@extends('layouts.app')

@section('content')
    @include('guru.pages.dashboard')
    @include('guru.pages.jadwal-mengajar')
    @include('guru.pages.izin-guru')
    @include('guru.pages.jurnal_absensi')
    @include('guru.pages.riwayat')

    @if($isWaliKelas ?? false)
        @include('struktural.pages.walikelas')
        @include('struktural.pages.wali_absensi_harian')
        @include('struktural.pages.wali_jurnal_harian')
        @include('struktural.pages.wali_rekap_absensi')
        @include('struktural.pages.wali_rekap_jurnal')
    @endif

    @if($isGuruPiket ?? false)
        @include('struktural.pages.guru-piket')
        @include('struktural.pages.dispen-siswa')
        @include('struktural.pages.absensi-siswa')
        @include('struktural.pages.siswa-terlambat')
    @endif

    @include('guru.pages.profil')
    @include('guru.pages.buat-laporan')

    @include('partials.bottom_nav_guru')
@endsection
