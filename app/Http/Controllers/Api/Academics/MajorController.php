<?php

namespace App\Http\Controllers\Api\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreMajorRequest;
use App\Http\Requests\Academics\UpdateMajorRequest;
use App\Http\Resources\Academics\MajorResource;
use App\Models\Major;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Models\StudyPlan;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $majors = Major::with(['studyPlans', 'courses'])->paginate(15);
        return MajorResource::collection($majors);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMajorRequest $request): MajorResource
    {
        $major = Major::create($request->validated());
        return new MajorResource($major);
    }

    /**
     * Display the specified resource.
     */
    public function show(Major $major): MajorResource
    {
        return new MajorResource($major->load(['studyPlans', 'courses']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMajorRequest $request, Major $major): MajorResource
    {
        $major->update($request->validated());
        return new MajorResource($major);
    }

    /**
     * Get courses for the major filtered by year and level.
     */
    public function courses(Request $request, Major $major)
    {
        $year = $request->query('year');
        $level = $request->query('level');

        $studyPlan = StudyPlan::where('major_id', $major->id)
            ->where('effective_year', '<=', $year)
            ->orderBy('effective_year', 'desc')
            ->first();

        if (!$studyPlan) {
            return response()->json(['data' => []]);
        }

        $courses = $studyPlan->courses()
            ->when($level, function ($query, $level) {
                return $query->where('study_plan_courses.semester_level', $level);
            })
            ->get();

        return \App\Http\Resources\Academics\CourseResource::collection($courses);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Major $major): Response
    {
        $major->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
