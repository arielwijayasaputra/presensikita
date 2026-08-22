@extends('layouts.app')

@section('content')
    @if(session('auth_role') === 'guru_piket')
        @include('struktural.pages.guru-piket')
        @include('struktural.pages.izin-guru')
    @endif
    @include('struktural.pages.profil')
    @include('struktural.pages.buat-laporan')
@endsection
