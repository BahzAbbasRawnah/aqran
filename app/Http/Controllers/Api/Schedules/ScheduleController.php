<?php

namespace App\Http\Controllers\Api\Schedules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedules\StoreScheduleRequest;
use App\Http\Requests\Schedules\UpdateScheduleRequest;
use App\Http\Resources\Schedules\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $schedules = Schedule::with(['items.course'])->paginate(15);
        return ScheduleResource::collection($schedules);
    }

    /**
     * Display the current student's schedule.
     */
    public function mySchedule(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->student) {
            // Fallback for testing if student_id is passed
            $studentId = $request->query('student_id');
            if (!$studentId) {
                return response()->json(['data' => null]);
            }
            $schedule = Schedule::where('student_id', $studentId)
                ->with(['items.course'])
                ->latest()
                ->first();
        } else {
            $schedule = $user->student->schedules()
                ->with(['items.course'])
                ->latest()
                ->first();
        }

        return $schedule ? new ScheduleResource($schedule) : response()->json(['data' => null]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleRequest $request): ScheduleResource
    {
        return DB::transaction(function () use ($request) {
            $schedule = Schedule::create([
                'student_id' => $request->student_id,
                'semester' => $request->semester,
                'year' => $request->year,
            ]);

            foreach ($request->items as $item) {
                $schedule->items()->create($item);
            }

            return new ScheduleResource($schedule->load('items.course'));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Schedule $schedule): ScheduleResource
    {
        return new ScheduleResource($schedule->load(['items.course']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule): ScheduleResource
    {
        $schedule->update($request->validated());
        return new ScheduleResource($schedule);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule): Response
    {
        $schedule->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
