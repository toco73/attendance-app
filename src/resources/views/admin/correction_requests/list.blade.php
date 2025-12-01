@extends('admin.layouts.app')

@section('title','申請一覧（管理者）')

@section('css')
<link rel="stylesheet" href="{{asset('/css/correction/list.css')}}">
@endsection

@section('content')
<div class="container">
    <h1 class="page__title">申請一覧</h1>
    <ul class="nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}" href="{{ route('admin.stamp_correction_request.list',['tab' => 'pending']) }}">承認待ち</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'approved' ? 'active' : '' }}"
               href="{{ route('admin.stamp_correction_request.list', ['tab' => 'approved']) }}">承認済み</a>
        </li>
    </ul>
    <div class="tab-content">
        <table class="table">
            <tr class="table__th">
                <th>状態</th>
                <th>名前</th>
                <th>対象日時</th>
                <th>申請理由</th>
                <th>申請日時</th>
                <th>詳細</th>
            </tr>
            @forelse($requests as $req)
            <tr class="table__td">
                <td class="table__td-item">{{ $req->status === 'pending' ? '承認待ち' : '承認済み' }}</td>
                <td class="table__td-item">{{ $req->user->name }}</td>
                <td class="table__td-item">{{ optional($req->attendance)->work_date ? \Carbon\Carbon::parse($req->attendance->date)->format('Y/m/d') : '-' }}</td>
                <td class="table__td-item">{{ $req->remark }}</td>
                <td class="table__td-item">{{ \Carbon\Carbon::parse($req->created_at)->format('Y/m/d') }}</td>
                <td class="table__td-item">
                    <a href="{{route('admin.attendance.detail',$req->id)}}">詳細</a>
                </td>
            </tr>
            @empty
            <tr class="table__td"><td class="table__td-item" colspan="6"></td></tr>
            @endforelse
        </table>
    </div>
</div>
@endsection