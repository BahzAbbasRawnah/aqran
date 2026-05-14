<?php

namespace App\Http\Controllers\Api\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreStudyPlanCourseRequest;
use App\Http\Requests\Academics\UpdateStudyPlanCourseRequest;
use App\Http\Resources\Academics\StudyPlanCourseResource;
use App\Models\StudyPlanCourse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class StudyPlanCourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $studyPlanCourses = StudyPlanCourse::with(['studyPlan', 'course'])->paginate(15);
        return StudyPlanCourseResource::collection($studyPlanCourses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudyPlanCourseRequest $request): StudyPlanCourseResource
    {
        $studyPlanCourse = StudyPlanCourse::create($request->validated());
        return new StudyPlanCourseResource($studyPlanCourse);
    }

    /**
     * Display the specified resource.
     */
    public function show(StudyPlanCourse $studyPlanCourse): StudyPlanCourseResource
    {
        return new StudyPlanCourseResource($studyPlanCourse->load(['studyPlan', 'course']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudyPlanCourseRequest $request, StudyPlanCourse $studyPlanCourse): StudyPlanCourseResource
    {
        $studyPlanCourse->update($request->validated());
        return new StudyPlanCourseResource($studyPlanCourse);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyPlanCourse $studyPlanCourse): Response
    {
        $studyPlanCourse->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
