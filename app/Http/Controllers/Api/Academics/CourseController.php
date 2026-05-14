<?php

namespace App\Http\Controllers\Api\Academics;

use App\Http\Controllers\Controller;
use App\Http\Requests\Academics\StoreCourseRequest;
use App\Http\Requests\Academics\UpdateCourseRequest;
use App\Http\Resources\Academics\CourseResource;
use App\Models\Course;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $courses = Course::with(['major', 'studyPlans'])->paginate(15);
        return CourseResource::collection($courses);
    }

    /**
     * Search for courses by name or code.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        $query = $request->query('query');
        $courses = Course::where('name', 'LIKE', "%{$query}%")
            ->orWhere('code', 'LIKE', "%{$query}%")
            ->take(10)
            ->get();
            
        return CourseResource::collection($courses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCourseRequest $request): CourseResource
    {
        $course = Course::create($request->validated());
        return new CourseResource($course);
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course): CourseResource
    {
        return new CourseResource($course->load(['major', 'studyPlans']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course): CourseResource
    {
        $course->update($request->validated());
        return new CourseResource($course);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course): Response
    {
        $course->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
