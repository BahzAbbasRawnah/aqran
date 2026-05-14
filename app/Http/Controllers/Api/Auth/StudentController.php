<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreStudentRequest;
use App\Http\Requests\Auth\UpdateStudentRequest;
use App\Http\Resources\Auth\StudentResource;
use App\Models\Student;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $students = Student::with(['user', 'major', 'studyPlan'])->paginate(15);
        return StudentResource::collection($students);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStudentRequest $request): StudentResource
    {
        $student = Student::create($request->validated());
        return new StudentResource($student);
    }

    /**
     * Display the specified resource.
     */
    public function show(Student $student): StudentResource
    {
        return new StudentResource($student->load(['user', 'major', 'studyPlan']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStudentRequest $request, Student $student): StudentResource
    {
        $student->update($request->validated());
        return new StudentResource($student);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student): Response
    {
        $student->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
