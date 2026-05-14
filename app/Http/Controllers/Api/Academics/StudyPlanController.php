<?php

namespace App\Http\Controllers\Api\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreStudyPlanRequest;
use App\Http\Requests\Academics\UpdateStudyPlanRequest;
use App\Http\Resources\Academics\StudyPlanResource;
use App\Models\StudyPlan;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudyPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $query = StudyPlan::with(['major', 'courses']);
        
        if (request()->has('major_id')) {
            $query->where('major_id', request('major_id'));
        }

        $studyPlans = $query->paginate(15);
        return StudyPlanResource::collection($studyPlans);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudyPlanRequest $request): StudyPlanResource
    {
        $studyPlan = StudyPlan::create($request->validated());
        return new StudyPlanResource($studyPlan);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudyPlan $studyPlan): StudyPlanResource
    {
        return new StudyPlanResource($studyPlan->load(['major', 'courses']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudyPlanRequest $request, StudyPlan $studyPlan): StudyPlanResource
    {
        $studyPlan->update($request->validated());
        return new StudyPlanResource($studyPlan);
    }

    /**
     * Get courses for the study plan filtered by level.
     */
    public function courses(StudyPlan $studyPlan): AnonymousResourceCollection
    {
        $level = request('level');
        $courses = $studyPlan->courses()
            ->when($level, function ($query, $level) {
                return $query->where('study_plan_courses.semester_level', $level);
            })
            ->get();

        return \App\Http\Resources\Academics\CourseResource::collection($courses);
    }

    /**
     * Get available levels for a major and year.
     */
    public function availableLevels(Request $request)
    {
        $request->validate([
            'major_id' => 'required|exists:majors,id',
            'year' => 'required|integer',
        ]);

        $studyPlan = StudyPlan::where('major_id', $request->major_id)
            ->where('effective_year', '<=', $request->year)
            ->orderBy('effective_year', 'desc')
            ->first();

        if (!$studyPlan) {
            return response()->json(['data' => []]);
        }

        $levels = DB::table('study_plan_courses')
            ->where('study_plan_id', $studyPlan->id)
            ->distinct()
            ->pluck('semester_level')
            ->sort()
            ->values();

        return response()->json(['data' => $levels]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyPlan $studyPlan): Response
    {
        $studyPlan->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
