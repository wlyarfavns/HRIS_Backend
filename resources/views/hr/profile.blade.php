@extends('layouts.hr')

@section('page-title', 'Profil Saya')
@section('page-desc', 'Kelola dan perbarui informasi data diri, foto profil, serta keamanan akun Anda.')

@section('content')
    @include('shared._profile_content', [
        'userData' => $userData,
        'updateProfileUrl' => route('hr.profile.update'),
        'updatePasswordUrl' => route('hr.profile.password')
    ])
@endsection
