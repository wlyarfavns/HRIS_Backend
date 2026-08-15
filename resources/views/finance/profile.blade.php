@extends('layouts.finance')

@section('page-title', 'Profil Saya')
@section('page-desc', 'Kelola dan perbarui informasi data diri, foto profil, serta keamanan akun Anda.')

@section('content')
    @include('shared._profile_content', [
        'userData' => $userData,
        'updateProfileUrl' => route('finance.profile.update'),
        'updatePasswordUrl' => route('finance.profile.password')
    ])
@endsection