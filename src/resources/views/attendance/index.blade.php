@extends('layouts.app')

@section('title','勤怠登録')

@section('css')
<link rel="stylesheet" href="{{asset('/css/attendance/index.css')}}">
@endsection

@section('content')
<div class="container">
    <div class="status">
        <span>{{ $attendance->status ?? '勤務外' }}</span>
    </div>
    <p class="date">{{ \Carbon\Carbon::now()->isoformat('YYYY年M月D日(ddd)') }}</p>
    <h1 class="clock" id="clock">{{ now()->format('H:i') }}</h1>
    <form action="{{route('attendance.updateStatus')}}" method="post">
        @csrf
        @php
            $status = $attendance->status ?? '勤務外';
        @endphp

        @if($status === '勤務外')
            <button type="submit" name="action" value="clock_in" class="button-brack">出勤</button>
        @elseif($status === '出勤中')
            <button type="submit" name="action" value="clock_out" class="button-brack">退勤</button>
            <button type="submit" name="action" value="break_in" class="button-white">休憩入</button>
        @elseif($status === '休憩中')
            <button type="submit" name="action" value="break_out" class="button-white">休憩戻</button>
        @elseif($status === '退勤済')
            <p class="breakout-comment">お疲れ様でした。</p>
        @endif
    </form>
</div>
@endsection

@section('scripts')
<script>
    function updateClock(){
        const now = new Date();
        const hours = String(now.getHours()).padStart(2,'0');
        const minutes = String(now.getMinutes()).padStart(2,'0');
        document.getElementById('clock').textContent = `${hours}:${minutes}`;
    }

    setInterval(updateClock,1000);
    updateClock();
</script>
@endsection