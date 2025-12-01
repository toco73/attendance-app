@extends('admin.layouts.app')

@section('title','勤怠詳細（管理者）')

@section('css')
<link rel="stylesheet" href="{{ asset('/css/attendance/detail.css')  }}">
@endsection

@section('content')
<style>
input[disabled]{
    border: none;
    background-color: #FFFFFF;
    color: #000000;
    pointer-events: none;
}
textarea[disabled]{
    border: none;
    background-color: #FFFFFF;
    color: #000000;
    pointer-events: none;
    resize: none;
    padding-left: 20px;
    font-weight: bold;
}
</style>
<div class="container">
    <h1 class="page__title">勤怠詳細</h1>
    <form action="{{ route('admin.attendance.update',$attendance->id) }}" method="post">
        @csrf
        @method('PUT')
            <table class="table">
                <tr class="table__tr">
                    <th class="table__th">名前</th>
                    <td class="table__td-name">{{ $attendance->user->name }}</td>
                </tr>
                <tr class="table__tr">
                    <th class="table__th">日付</th>
                    <td class="table__td">
                        <span class="date__year">
                            {{ \Carbon\Carbon::parse($attendance->work_date)->format('Y年') }}
                        </span>
                        <span class="date__monthday">
                            {{ \Carbon\Carbon::parse($attendance->work_date)->format('n月j日') }}
                        </span>
                    </td>
                </tr>

                @php
                $isDisabled = $pendingRequest ? 'disabled' : '';
                $clockInValue = old('clock_in_time')
                    ?? ($pendingRequest ? optional($pendingRequest->new_clock_in_time)->format('H:i')
                    : optional($attendance->clock_in_time)->format('H:i')
                    );
                $clockOutValue = old('clock_out_time')
                    ?? ($pendingRequest ? optional($pendingRequest->new_out_in_time)->format('H:i')
                    : optional($attendance->clock_out_time)->format('H:i')
                    );
                $breaks = $attendance->breaks->map(function($b){
                    return [
                        'start' => optional($b->break_start_time)->format('H:i'),
                        'end' => optional($b->break_end_time)->format('H:i')
                    ];
                })->toArray();
                $newIndex = count($breaks);
                @endphp

                <tr  class="table__tr">
                    <th class="table__th">出勤・退勤</th>
                    <td class="table__td">
                        <input type="time" name="clock_in_time" value="{{ $clockInValue }}" {{$isDisabled}}>
                        <span class="tilde">~</span>
                        <input type="time" name="clock_out_time" value="{{ $clockOutValue }}" {{$isDisabled}}>
                        <div class="form__error">
                            @error('clock_in_time')
                            {{$message}}
                            @enderror
                        </div>
                        <div class="form__error">
                            @error('clock_out_time')
                            {{$message}}
                            @enderror
                        </div>
                    </td>
                </tr>
                
                @foreach($breaks as $i => $break)
                <tr class="table__tr">
                    <th class="table__th">休憩{{ $i + 1 }}</th>
                    <td class="table__td">
                        <input type="time" name="breaks[{{ $i }}][start]" value="{{ old("breaks.$i.start", $break['start']) }}" {{$isDisabled}}>
                        <span class="tilde">~</span>
                        <input type="time" name="breaks[{{ $i }}][end]" value="{{ old("breaks.$i.end", $break['end']) }}" {{$isDisabled}}>
                        <div class="form__error">
                            @error("breaks.$i.start")
                            {{$message}}
                            @enderror
                        </div>
                        <div class="form__error">
                            @error("breaks.$i.end")
                            {{$message}}
                            @enderror
                        </div>
                    </td>
                </tr class="table__tr">
                @endforeach
                <tr class="table__tr">
                    <th class="table__th">休憩{{ $newIndex + 1 }}</th>
                    <td class="table__td">
                        <input type="time" name="breaks[{{ $newIndex }}][start]" value="{{ old('breaks.' . $newIndex . '.start', $breaks[$newIndex]['start'] ?? '') }}" {{$isDisabled}}>
                        <span class="tilde">~</span>
                        <input type="time" name="breaks[{{ $newIndex }}][end]" value="{{ old('breaks.' . $newIndex . '.end', $breaks[$newIndex]['end'] ?? '') }}" {{$isDisabled}}
                        @error('breaks.' . $newIndex . '.start')
                        <div class="form__error">
                            {{ $message }}
                        </div>
                        @enderror

                        @error('breaks.' . $newIndex . '.end')
                        <div class="form__error">
                            {{ $message }}
                        </div>
                        @enderror
                    </td>
                </tr>
                <tr class="table__tr">
                    <th class="table__th">備考</th>
                    <td class="table__td">
                        <textarea name="remark" {{$isDisabled}} required>{{ old('remark',$attendance->remark) }}</textarea>
                        @error('remark')
                            <div class="form__error">
                            {{$message}}
                            </div>
                        @enderror
                    </td>
                </tr>
            </table>
        @if($pendingRequest)
        <p class="pending">*承認待ちのため修正できません。</p>
        @endif
        @if(!$pendingRequest)
        <div class="submit-button">
            <button type="submit">修正</button>
        </div>
        @endif
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.querySelectorAll('input[type="time"]').forEach(input => { function toggleColor() {
        if (input.value === '') {
            input.style.color = 'transparent';
            input.style.webkitTextFillColor = 'transparent';
        } else {
            input.style.color = '';
            input.style.webkitTextFillColor = '';
        }
    }
    toggleColor();
    input.addEventListener('input', toggleColor);
    });
</script>
@endsection