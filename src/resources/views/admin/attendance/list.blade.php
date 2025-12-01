@extends('admin.layouts.app')

@section('title','勤怠一覧（管理者）')

@section('css')
<link rel="stylesheet" href="{{asset('/css/attendance/list.css')}}">
@endsection

@section('content')
<div class="container">
    <h1 class="page__title">{{ $date->format('Y年n月j日') }}の勤怠</h1>
    
    <div class="flex">
        <a href="{{ route('admin.attendance.list',['date' => $date->copy()->subDay()->toDateString()]) }}">←前日</a>
        <span>{{ $date->format('Y/n/j') }}</span>
        <a href="{{ route('admin.attendance.list',['date' => $date->copy()->addDay()->toDateString()]) }}">翌日→</a>
    </div>
    <table class="table">
        <tr class="table__th">
            <th>名前</th>
            <th>出勤</th>
            <th>退勤</th>
            <th>休憩</th>
            <th>合計</th>
            <th>詳細</th>
        </tr>

        @forelse($attendances as $attendance)
        <tr class="table__td">
            <td class="table__td-item">{{ $attendance->user->name }}</td>
            <td class="table__td-item">{{ optional($attendance->clock_in_time)->format('H:i') ?? '' }}</td>
            <td class="table__td-item">{{ optional($attendance->clock_out_time)->format('H:i') ?? '' }}</td>
            @php
            $breakSeconds = 0;
            if ($attendance->breaks && $attendance->breaks->isNotEmpty()){
                foreach ($attendance->breaks as $break){
                    if ($break->break_start_time && $break->break_end_time){
                        $breakSeconds += \Carbon\Carbon::parse($break->break_start_time)
                            ->diffInSeconds(\Carbon\Carbon::parse($break->break_end_time));
                    }
                }
            }
            $totalSeconds = 0;
            if ($attendance->clock_in_time && $attendance->clock_out_time){
                $totalSeconds = \Carbon\Carbon::parse($attendance->clock_in_time)
                    ->diffInSeconds(\Carbon\Carbon::parse($attendance->clock_out_time)) - $breakSeconds;
            }
            $breakTime = gmdate('H:i',$breakSeconds);
            $totalTime = gmdate('H:i',$totalSeconds);
            @endphp
            <td class="table__td-item">{{ $breakTime }}</td>
            <td class="table__td-item">{{ $totalTime }}</td>                
            <td class="table__td-item">
                <a href="{{ route('admin.attendance.detail',['id' => $attendance->id]) }}">詳細</a>
            </td>
        </tr>
        @empty
        <tr><td class="table__td-item" colspan="6"></td></tr>
        @endforelse
    </table>
</div>
@endsection