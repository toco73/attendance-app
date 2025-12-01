<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\CorrectionRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminAttendanceUpdateRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    //勤怠一覧画面
    public function list(Request $request)
    {
        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))
            : Carbon::today();

        $attendances = Attendance::with('user')
            ->whereDate('work_date',$date->toDateString())
            ->get();

        
        return view('admin.attendance.list',compact('attendances','date'));
    }

    //勤怠詳細画面
    public function detail($id)
    {
        $attendance = Attendance::with('user','breaks')->findOrFail($id);

        $pendingRequest = CorrectionRequest::where('attendance_id',$id)
            ->where('status','pending')
            ->first();

        return view('admin.attendance.detail',compact('attendance','pendingRequest'));
    }

    public function update(AdminAttendanceUpdateRequest $request,$id)
    {
        $attendance = Attendance::findOrFail($id);

        $attendance->update([
            'clock_in_time' => $request->clock_in_time,
            'clock_out_time' => $request->clock_out_time,
            'remark' => $request->remark,
        ]);

        $attendance->breaks()->delete();
        foreach($request->breaks ?? [] as $break){
            if (!empty($break['start']) && !empty($break['end'])){
                $attendance->breaks()->create([
                    'break_start_time' => $break['start'],
                    'break_end_time' => $break['end'],
                ]);
            }
        }

        $validated = $request->validated();

        return redirect()->route('admin.attendance.detail',$id);
    }

    //スタッフ一覧画面
    public function staffList(Request $request){
        $staffs = User::all();
        return view('admin.staff.list',compact('staffs'));
    }

    public function attendanceStaff(Request $request,$id){
        $staff = User::findOrFail($id);

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
        return view('admin.attendance.staff',compact('staff','days','currentMonth'));
    }
}
