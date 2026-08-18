@extends('layouts.supervisor')

@section('page-title', 'Profil Saya')
@section('page-desc', 'Kelola dan perbarui informasi data diri, foto profil, serta keamanan akun Anda.')

@section('content')
    @include('shared._profile_content', [
        'userData' => $userData,
        'updateProfileUrl' => route('supervisor.profile.update'),
        'updatePasswordUrl' => route('supervisor.profile.password'),
    ])
@endsection
