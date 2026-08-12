@extends('layouts.finance')

@section('page-title', 'Profil Saya')
@section('page-desc', 'Kelola dan perbarui informasi data diri, foto profil, serta keamanan akun Anda.')

@section('content')
    @include('shared._profile_content', [
        'userData' => [
            'name' => 'Rina Kartika',
            'email' => 'rina.kartika@talentahr.co.id',
            'phone' => '0813-9876-5432',
            'role' => 'Finance Staff',
            'department' => 'Finance & Accounting',
            'position' => 'Senior Payroll Specialist',
            'nip' => 'FIN-2024-002',
            'avatar' => 'https://i.pravatar.cc/150?img=32',
            'join_date' => '10 Maret 2022',
            'gender' => 'Perempuan',
            'birth_place' => 'Surabaya',
            'birth_date' => '1994-11-20',
            'address' => 'Jl. Pemuda No. 88, Surabaya',
            'bank_name' => 'Bank Central Asia (BCA)',
            'bank_account' => '5432-1098-76',
            'npwp' => '45.678.901.2-034.000',
        ]
    ])
@endsection