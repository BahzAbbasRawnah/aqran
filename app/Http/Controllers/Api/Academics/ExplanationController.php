<?php

namespace App\Http\Controllers\Api\Academics;

use App\Http\Controllers\Controller;
use App\Models\Explanation;
use App\Models\Course;
use App\Models\TutoringRequest;
use App\Enums\ContentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExplanationController extends Controller
{
    /**
     * Get list of courses that have explanations.
     */
    public function courses()
    {
        $courses = Course::whereHas('explanations')
            ->withCount('explanations')
            ->get();
            
        return response()->json([
            'data' => $courses
        ]);
    }

    /**
     * Get explanations for a specific course.
     */
    public function index(Request $request, $courseId)
    {
        $explanations = Explanation::with('user:id,first_name,last_name')
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return response()->json([
            'data' => $explanations
        ]);
    }

    /**
     * Increment view count.
     */
    public function incrementViews($id)
    {
        $explanation = Explanation::findOrFail($id);
        $explanation->increment('views');
        
        return response()->json(['message' => 'Views incremented']);
    }

    /**
     * Get courses the user is approved to explain.
     */
    public function myApprovedCourses(Request $request)
    {
        $user = $request->user();
        if (!$user->student) {
            return response()->json(['data' => []]);
        }

        $approvedCourseIds = TutoringRequest::where('student_id', $user->student->id)
            ->where('status', ContentStatus::APPROVED->value)
            ->pluck('course_id');

        $courses = Course::whereIn('id', $approvedCourseIds)->get();

        return response()->json([
            'data' => $courses
        ]);
    }

    /**
     * Store a new explanation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv|max:102400', // 100MB
            'thumbnail' => 'nullable|image|max:5120', // 5MB
        ]);

        $user = $request->user();
        if (!$user->student) {
            return response()->json(['message' => 'Only students can upload explanations.'], 403);
        }

        // Strictly verify approval
        $isApproved = TutoringRequest::where('student_id', $user->student->id)
            ->where('course_id', $request->course_id)
            ->where('status', ContentStatus::APPROVED)
            ->exists();

        if (!$isApproved) {
            return response()->json([
                'message' => 'You are not approved to upload explanations for this course.'
            ], 403);
        }

        $data = [
            'course_id' => $request->course_id,
            'user_id' => $user->id,
            'title' => $request->title,
            'views' => 0,
        ];

        if ($request->hasFile('video')) {
            $data['video_path'] = $request->file('video')->store('explanations/videos', 'public');
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('explanations/thumbnails', 'public');
        }

        $explanation = Explanation::create($data);

        return response()->json([
            'message' => 'Explanation uploaded successfully.',
            'data' => $explanation
        ], 201);
    }
}
