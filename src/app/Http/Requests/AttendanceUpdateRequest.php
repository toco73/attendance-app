<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceUpdateRequest extends FormRequest
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
            'clock_in_time' => ['required', 'date_format:H:i'],
            'clock_out_time' => ['required', 'date_format:H:i'],
            'break.*.start' => ['nullable', 'date_format:H:i'],
            'break.*.end' => ['nullable', 'date_format:H:i'],
            'remark' => ['required', 'string'],
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
            $in = $this->clock_in_time;
            $out = $this->clock_out_time;

            if($in && $out && $in >= $out){
                $validator->errors()->add('clock_in_time','出勤時間もしくは退勤時間が不適切な値です');
            }

            if($this->breaks){
                foreach ($this->breaks as $i => $break){
                    $start = $break['start'] ?? null;
                    $end = $break['end'] ?? null;

                    if($start){
                        if($start < $in){
                            $validator->errors()->add("breaks.$i.start",'休憩時間が不適切な値です');
                        }
                        if($start > $out){
                            $validator->errors()->add("breaks.$i.start",'休憩時間が不適切な値です');
                        }
                    }

                    if($end){
                        if($end > $out){
                            $validator->errors()->add("breaks.$i.end",'休憩時間もしくは退勤時間が不適切な値です');
                        }
                    }
                }
            }
        });
    }
}
