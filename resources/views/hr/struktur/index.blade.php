@extends('layouts.hr')

@section('title', 'Struktur Organisasi')
@section('page-title', 'Struktur Organisasi')
@section('page-desc', 'Org chart interaktif seluruh departemen.')

@php
    $tree = [
        'name' => 'David Wallace', 'role' => 'CEO', 'avatar' => 5,
        'children' => [
            ['name' => 'Michael Scott', 'role' => 'Regional Manager', 'avatar' => 14, 'children' => [
                ['name' => 'Dwight Schrute', 'role' => 'Asst. Manager', 'avatar' => 51],
                ['name' => 'Jim Halpert', 'role' => 'Sales Executive', 'avatar' => 12],
            ]],
            ['name' => 'Andi Wijaya', 'role' => 'Finance Head', 'avatar' => 15, 'children' => [
                ['name' => 'Angela Martin', 'role' => 'Accounting Staff', 'avatar' => 33],
            ]],
        ],
    ];
@endphp

@section('content')

    <div class="card-flat rounded-2xl p-8 overflow-x-auto">
        <div class="flex flex-col items-center min-w-[700px]">
            {{-- ROOT --}}
            <div class="card-flat rounded-xl px-5 py-3 flex items-center gap-3 border-t-[3px]" style="border-color:#0B3D2E;">
                <img src="https://i.pravatar.cc/36?img={{ $tree['avatar'] }}" class="w-9 h-9 rounded-full" alt="">
                <div>
                    <p class="font-bold text-on-surface text-sm">{{ $tree['name'] }}</p>
                    <p class="text-[11px] text-on-surface-variant/50">{{ $tree['role'] }}</p>
                </div>
            </div>

            <div class="w-px h-8 bg-black/10"></div>

            <div class="flex gap-16">
                @foreach ($tree['children'] as $c1)
                    <div class="flex flex-col items-center">
                        <div class="card-flat rounded-xl px-5 py-3 flex items-center gap-3 border-t-[3px]" style="border-color:#FFD700;">
                            <img src="https://i.pravatar.cc/32?img={{ $c1['avatar'] }}" class="w-8 h-8 rounded-full" alt="">
                            <div>
                                <p class="font-bold text-on-surface text-xs">{{ $c1['name'] }}</p>
                                <p class="text-[10px] text-on-surface-variant/50">{{ $c1['role'] }}</p>
                            </div>
                        </div>
                        <div class="w-px h-6 bg-black/10"></div>
                        <div class="flex gap-4">
                            @foreach ($c1['children'] as $c2)
                                <div class="card-flat rounded-lg px-4 py-2.5 flex items-center gap-2">
                                    <img src="https://i.pravatar.cc/26?img={{ $c2['avatar'] }}" class="w-6.5 h-6.5 rounded-full" alt="">
                                    <div>
                                        <p class="font-bold text-on-surface text-[11px]">{{ $c2['name'] }}</p>
                                        <p class="text-[10px] text-on-surface-variant/50">{{ $c2['role'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection