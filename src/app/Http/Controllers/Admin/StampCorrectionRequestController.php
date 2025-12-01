<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\CorrectionRequest;
use Illuminate\Support\Facades\Auth;

class StampCorrectionRequestController extends Controller
{
    //申請一覧画面
    public function requestList(Request $request){
        $tab = $request->get('tab', 'pending');
        $query = CorrectionRequest::with('user','attendance')->orderBy('created_at','desc');

        if ($tab === 'approved') {
            $query->where('status', 'approved');
        } else {
            $query->where('status', 'pending');
        }

        $requests = $query->get();

        return view('admin.correction_requests.list', compact('requests', 'tab'));
    }
}
