<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\CorrectionRequest;
use App\Http\Requests\AttendanceUpdateRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    //勤怠登録画面
    public function index()
    {
        $today = Carbon::today();
        $attendance = Attendance::where('user_id',Auth::id())
            ->whereDate('work_date', $today)->first();

        return view('attendance.index',compact('attendance'));
    }

    public function updateStatus(Request $request)
    {
        $action = $request->input('action');
        $today = Carbon::today();
        $attendance = Attendance::firstOrCreate(
            ['user_id' => Auth::id(),'work_date' => $today],
            ['status' => '勤務外']
        );

        switch ($action) {
            case  'clock_in':
                $attendance->update([
                    'clock_in_time' => Carbon::now(),
                    'status' => '出勤中'
                ]);
                break;

            case 'break_in':
                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start_time' => Carbon::now(),
                ]);
                $attendance->update(['status' => '休憩中']);
                break;
            
            case 'break_out':
                $break = $attendance->breaks()->whereNull('break_end_time')->latest()->first();
                if($break){
                    $break->update(['break_end_time' => Carbon::now()]);
                }
                $attendance->update(['status' => '出勤中']);
                break;

            case 'clock_out':
                $attendance->update([
                    'clock_out_time' => Carbon::now(),
                    'status' => '退勤済'
                ]);
                break;
        }
        
        return redirect()->route('attendance.index');
    }

    //勤怠一覧画面
    public function list(Request $request){
        $monthParam = $request->query('month');
        $currentMonth = $monthParam ? Carbon::parse($monthParam) : Carbon::now();

        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        $attendances = Attendance::with('breaks')
            ->where('user_id',Auth::id())
            ->whereBetween('work_date',[$startOfMonth,$endOfMonth])
            ->get()
            ->keyBy(function($attendance){
                return Carbon::parse($attendance->work_date)->format('Y-m-d');
            });

        $days = [];
        $period = \Carbon\CarbonPeriod::create($startOfMonth,$endOfMonth);

        foreach ($period as $date){
            $formattedDate = $date->format('Y-m-d');
            $attendance = Attendance::with('breaks')
                ->where('user_id',Auth::id())
                ->whereDate('work_date',$formattedDate)
                ->first();

            $totalBreakSeconds = 0;

            if($attendance && $attendance->breaks->isNotEmpty()){
                foreach($attendance->breaks as $break){
                    if($break->break_start_time && $break->break_out_time){
                        $totalBreakSeconds += Carbon::parse($break->break_start_time)->diffInSeconds(Carbon::parse($break->break_end_time));
                    }
                }
            }

            $breakTime = gmdate('H:i',$totalBreakSeconds);

            $totalTime = '';
            if($attendance && $attendance->clock_in_time && $attendance->clock_out_time){
                $totalSeconds = Carbon::parse($attendance->clock_in_time)
                    ->diffInSeconds(Carbon::parse($attendance->clock_out_time)) - ($totalBreakSeconds ?? 0);
                $totalTime = gmdate('H:i',$totalSeconds);
            }

            $days[] = [
                'date' => $date,
                'attendance' => $attendance,
                'break_time' => $breakTime,
                'total_time' => $totalTime,
            ];
        }

        return view('attendance.list',[
            'days' => $days,
            'currentMonth' => $currentMonth,
        ]);
    }

    //勤怠詳細画面
    public function detail($id)
    {
        $attendance = Attendance::with('breaks','user')->findOrFail($id);

        $pendingRequest = $attendance->correctionRequests()
            ->where('status','pending')
            ->first();

        return view('attendance.detail',compact('attendance','pendingRequest'));
    }

    public function updateRequest(AttendanceUpdateRequest $request,$id)
    {
        $attendance = Attendance::findOrFail($id);

        CorrectionRequest::create([
            'attendance_id' => $attendance->id,
            'user_id' => Auth::id(),
            'new_clock_in_time' => $request->clock_in_time,
            'new_clock_out_time' => $request->clock_out_time,
            'new_breaks' => json_encode($request->breaks),
            'remark' => $request->remark,
            'status' => 'pending',
        ]);

        return redirect()->route('attendance.detail',$id);
    }
}
