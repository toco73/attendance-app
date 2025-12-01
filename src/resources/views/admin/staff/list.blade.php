@extends('admin.layouts.app')

@section('title','スタッフ一覧（管理者）')

@section('css')
<link rel="stylesheet" href="{{asset('/css/attendance/list.css')}}">
@endsection

@section('content')
<div class="container">
    <h1 class="page__title">スタッフ一覧</h1>
    
    <table class="table">
        <tr class="table__th">
            <th>名前</th>
            <th>メールアドレス</th>
            <th>月次勤怠</th>
        </tr>
        @foreach ($staffs as $staff)
        <tr class="table__td">
            <td class="table__td-item">{{ $staff->name }}</td>
            <td class="table__td-item">{{ $staff->email }}</td>  
            <td class="table__td-item">
                <a href="{{ route('admin.attendance.staff', $staff->id) }}">詳細</a>
            </td>
        </tr>
        @endforeach   
    </table>
</div>
@endsection