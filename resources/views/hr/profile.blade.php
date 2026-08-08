@extends('layouts.hr')

@section('page-title', 'Profil Saya')
@section('page-desc', 'Kelola dan perbarui informasi data diri, foto profil, serta keamanan akun Anda.')

@section('content')
    @include('shared._profile_content', [
        'userData' => [
            'name' => 'Sarah Johnson',
            'email' => 'sarah.johnson@talentahr.co.id',
            'phone' => '0812-3456-7890',
            'role' => 'HR Admin',
            'department' => 'Human Resources',
            'position' => 'Senior HR Specialist',
            'nip' => 'EMP-2024-001',
            'avatar' => 'https://i.pravatar.cc/150?img=47',
            'join_date' => '15 Januari 2022',
            'gender' => 'Perempuan',
            'birth_place' => 'Jakarta',
            'birth_date' => '1995-04-12',
            'address' => 'Jl. Sudirman No. 45, Jakarta Selatan',
            'bank_name' => 'Bank Mandiri',
            'bank_account' => '123-00-9876543-2',
            'npwp' => '98.765.432.1-012.000',
        ]
    ])
@endsection
