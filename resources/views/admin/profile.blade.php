@extends('layouts.admin')

@section('page-title', 'Profil Saya')
@section('page-desc', 'Kelola dan perbarui informasi data diri, foto profil, serta keamanan akun Anda.')

@section('content')
    @include('shared._profile_content', [
        'userData' => [
            'name' => 'Andi Wijaya',
            'email' => 'andi.wijaya@talentahr.co.id',
            'phone' => '0811-2233-4455',
            'role' => 'Super Admin',
            'department' => 'Executive Office & Management',
            'position' => 'System Administrator',
            'nip' => 'ADM-2024-001',
            'avatar' => 'https://i.pravatar.cc/150?img=15',
            'join_date' => '01 Januari 2021',
            'gender' => 'Laki-laki',
            'birth_place' => 'Bandung',
            'birth_date' => '1990-08-17',
            'address' => 'Jl. Asia Afrika No. 102, Bandung',
            'bank_name' => 'Bank Central Asia (BCA)',
            'bank_account' => '8800-1234-5678',
            'npwp' => '12.345.678.9-012.000',
        ]
    ])
@endsection
