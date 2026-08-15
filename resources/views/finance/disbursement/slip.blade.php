@extends('layouts.finance')

@section('title', 'Slip Gaji')
@section('page-title', 'Slip Gaji')
@section('page-desc', 'Preview slip gaji digital yang telah didistribusikan ke karyawan.')

@section('content')
    @include('shared._slip-gaji-content', ['slip' => $slip, 'backRoute' => 'finance.disbursement.index'])
    
@endsection