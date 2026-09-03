@extends('layouts.app')

@section('content')
    @if(session('auth_role') === 'guru_piket')
        @include('struktural.pages.guru-piket')
        @include('struktural.pages.izin-guru')
        @include('struktural.pages.dispen-siswa')
        @include('struktural.pages.absensi-siswa')
    @endif
    @if(session('auth_role') === 'satpam')
        @include('struktural.pages.satpam')
        @include('struktural.pages.satpam_harian')
    @endif
    @if(session('auth_role') === 'waka_sdm')
        @include('struktural.pages.waka_sdm')
    @endif
    @if($isWaliKelas)
        @include('struktural.pages.walikelas')
        @include('struktural.pages.wali_absensi_harian')
        @include('struktural.pages.wali_jurnal_harian')
        @include('struktural.pages.wali_rekap_absensi')
        @include('struktural.pages.wali_rekap_jurnal')
    @endif
    @include('struktural.pages.profil')
    @include('struktural.pages.buat-laporan')
@endsection
