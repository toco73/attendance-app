<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\CorrectionRequest;
use Illuminate\Support\Facades\Auth;

class StampCorrectionRequestController extends Controller
{
    public function list(Request $request)
    {
        $tab = $request->get('tab', 'pending');
        $user = Auth::user();

        if ($tab === 'approved') {
            $requests = CorrectionRequest::where('user_id', $user->id)
                ->where('status', 'approved')
                ->with('user', 'attendance')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $requests = CorrectionRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->with('user', 'attendance')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('correction_requests.list', compact('requests', 'tab'));
    }
}
