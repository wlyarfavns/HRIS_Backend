@extends('layouts.supervisor')

@section('page-title', 'Profil Saya')
@section('page-desc', 'Kelola dan perbarui informasi data diri, foto profil, serta keamanan akun Anda.')

@section('content')
    @include('shared._profile_content', [
        'userData' => [
            'name' => 'Andy Bernard',
            'email' => 'andy.bernard@talentahr.co.id',
            'phone' => '0815-4433-2211',
            'role' => 'Supervisor',
            'department' => 'Operations & Field Team',
            'position' => 'Operational Supervisor',
            'nip' => 'SPV-2024-005',
            'avatar' => 'https://i.pravatar.cc/150?img=51',
            'join_date' => '05 Mei 2021',
            'gender' => 'Laki-laki',
            'birth_place' => 'Semarang',
            'birth_date' => '1989-02-14',
            'address' => 'Jl. Pandanaran No. 12, Semarang',
            'bank_name' => 'Bank Mandiri',
            'bank_account' => '987-00-1122334-5',
            'npwp' => '67.890.123.4-056.000',
        ]
    ])
@endsection
