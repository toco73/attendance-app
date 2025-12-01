<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class AdminAttendanceUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clock_in_time' => ['required','date_format:H:i'],
            'clock_out_time' => ['required','date_format:H:i'],
            'breaks.*.start' => ['nullable','date_format:H:i'],
            'breaks.*.end' => ['nullable','date_format:H:i'],
            'remark' => ['required','string'],
        ];
    }

    public function messages()
    {
        return [
            'remark.required' => '備考欄を記入してください',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator){
            $date = $this->all();

            if (!empty($date['clock_in_time']) && !empty($date['clock_out_time'])){
                $in = Carbon::createFromFormat('H:i',$date['clock_in_time']);
                $out = Carbon::createFromFormat('H:i',$date['clock_out_time']);

                if ($in->greaterThan($out)){
                    $validator->errors()->add('clock_in_time','出勤時間もしくは退勤時間が不適切な値です');
                    $validator->errors()->add('clock_out_time','出勤時間もしくは退勤時間が不適切な値です');
                }

                if (!empty($date['breaks']) && is_array($date['breaks'])){
                    foreach($date['breaks'] as $i => $break){
                        if (empty($break['start']) && empty($break['end'])){
                            continue;
                        }

                        if (!empty($break['start'])){
                            $start = Carbon::createFromFormat('H:i',$break['start']);
                            if ($start->lessThan($in) || $start->greateThan($out)){
                                 $validator->errors()->add("breaks.$i.start",'休憩時間が不適切な値です');
                            }
                        }

                        if (!empty($break['end'])){
                            $end = Carbon::createFromFormat('H:i',$break['end']);
                            if ($end->greaterThan($out)){
                                $validator->errors()->add("breaks.$i.end",'休憩時間もしくは退勤時間が不適切な値です');
                            }
                        }
                    }
                }
            }
        });
    }
}
