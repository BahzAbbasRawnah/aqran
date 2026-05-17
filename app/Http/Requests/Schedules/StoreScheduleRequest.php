<?php

namespace App\Http\Requests\Schedules;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|integer',
            'year' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.course_id' => 'required|exists:courses,id',
            'items.*.type' => 'required|in:theory,practical',
            'items.*.day_of_week' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'items.*.start_time' => 'required|date_format:H:i',
            'items.*.end_time' => 'required|date_format:H:i|after:items.*.start_time',
            'items.*.notes' => 'nullable|string',
        ];
    }

    /**
     * Custom overlap detection validation.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items');
            if (is_array($items)) {
                $grouped = [];
                foreach ($items as $index => $item) {
                    if (empty($item['day_of_week']) || empty($item['start_time']) || empty($item['end_time'])) {
                        continue;
                    }
                    $grouped[$item['day_of_week']][] = [
                        'index' => $index,
                        'start' => $item['start_time'],
                        'end' => $item['end_time']
                    ];
                }

                $arabicDays = [
                    'sunday' => 'الأحد',
                    'monday' => 'الاثنين',
                    'tuesday' => 'الثلاثاء',
                    'wednesday' => 'الأربعاء',
                    'thursday' => 'الخميس',
                    'friday' => 'الجمعة',
                    'saturday' => 'السبت',
                ];

                foreach ($grouped as $day => $slots) {
                    // Sort slots by start_time
                    usort($slots, function ($a, $b) {
                        return strcmp($a['start'], $b['start']);
                    });

                    // Check for overlap
                    for ($i = 1; $i < count($slots); $i++) {
                        $prev = $slots[$i - 1];
                        $curr = $slots[$i];

                        // If current start is less than previous end, they overlap
                        if (strcmp($curr['start'], $prev['end']) < 0) {
                            $dayName = $arabicDays[$day] ?? $day;
                            $validator->errors()->add(
                                "items.{$curr['index']}.start_time",
                                "توجد تعارضات في الأوقات ليوم {$dayName}. يتقاطع الوقت مع الحصة الأخرى."
                            );
                        }
                    }
                }
            }
        });
    }
}
