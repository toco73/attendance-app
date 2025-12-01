@extends('admin.layouts.app')

@section('title','スタッフ別勤怠一覧（管理者）')

@section('css')
<link rel="stylesheet" href="{{asset('/css/attendance/list.css')}}">
@endsection

@section('content')
<div class="container">
    <h1 class="page__title">{{ $staff->name }}の勤怠</h1>
    
    <div class="flex">
        <a href="{{ route('attendance.list',['month' => $currentMonth->copy()->subMonth()->format('Y-m')]) }}">←前月</a>
        <span>{{ $currentMonth->format('Y/m') }}</span>
        <a href="{{ route('attendance.list',['month' => $currentMonth->copy()->addMonth()->format('Y-m')]) }}">翌月→</a>
    </div>
    <table class="table">
        <tr class="table__th">
            <th>日付</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>
        @foreach($days as $day)
        <tr class="table__td">
            <td class="table__td-item">{{ $day['date']->translatedFormat('m/d(D)') }}</td>
            @if($day['attendance'])
                <td class="table__td-item">{{ optional($day['attendance']->clock_in_time)->format('H:i') ?? '' }}</td>
                <td class="table__td-item">{{ optional($day['attendance']->clock_out_time)->format('H:i') ?? '' }}</td>
                <td class="table__td-item">{{ $day['break_time'] }}</td>
                <td class="table__td-item">{{ $day['total_time'] }}</td>
                <td class="table__td-item"><a href="{{ route('attendance.detail',['id' => $day['attendance']->id]) }}">詳細</a></td>
            @else
                <td class="table__td-item" colspan="5"></td>
            @endif
        </tr>
        @endforeach
    </table>
</div>
@endsection