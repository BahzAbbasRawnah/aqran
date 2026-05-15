<?php

namespace App\Http\Controllers\Api\Academics;

use App\Http\Controllers\Controller;
use App\Models\TutoringRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Enums\ContentStatus;

class TutoringRequestController extends Controller
{
    /**
     * Store a newly created tutoring request in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'is_completed' => 'required|boolean',
            'grade' => 'required_if:is_completed,1|nullable|in:A+,A,B+,B,C+,C',
            'learning_details' => 'required_if:is_completed,0|string|nullable',
            'curriculum_parts' => 'required|string',
            'demo_video' => 'nullable|file|mimes:mp4,mov,avi,wmv|max:51200', // max 50MB
        ]);

        $user = $request->user();
        if (!$user->student) {
            return response()->json(['message' => 'User is not a student'], 403);
        }

        $data = $validated;
        $data['student_id'] = $user->student->id;

        if ($request->hasFile('demo_video')) {
            $path = $request->file('demo_video')->store('tutoring_videos', 'public');
            $data['video_url'] = $path;
        }

        $data['status'] = ContentStatus::PENDING;

        $tutoringRequest = TutoringRequest::create($data);

        return response()->json([
            'message' => 'Tutoring request submitted successfully',
            'data' => $tutoringRequest
        ], 201);
    }

    /**
     * Get the authenticated user's tutoring requests.
     */
    public function myRequests(Request $request)
    {
        $user = $request->user();
        if (!$user->student) {
            return response()->json(['message' => 'User is not a student'], 403);
        }

        $requests = TutoringRequest::with('course:id,name,code')
            ->where('student_id', $user->student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $requests
        ]);
    }
}
