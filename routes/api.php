<?php

use App\Http\Controllers\Api\Academics\CourseController;
use App\Http\Controllers\Api\Academics\MajorController;
use App\Http\Controllers\Api\Academics\StudyPlanController;
use App\Http\Controllers\Api\Academics\StudyPlanCourseController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\OtpController;
use App\Http\Controllers\Api\Auth\StudentController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\Projects\ProjectController;
use App\Http\Controllers\Api\Projects\ProjectFileController;
use App\Http\Controllers\Api\Schedules\ScheduleController;
use App\Http\Controllers\Api\Schedules\ScheduleItemController;
use App\Http\Controllers\Api\Social\AnnouncementController;
use App\Http\Controllers\Api\Social\CommunityController;
use App\Http\Controllers\Api\Social\SocialFeedController;
use App\Http\Controllers\Api\Social\WorkshopController;
use App\Http\Controllers\Api\Academics\TutoringRequestController;
use App\Http\Controllers\Api\Academics\ExplanationController;
use App\Http\Controllers\Api\Tasks\TaskController;
use App\Http\Controllers\Api\Tasks\TaskReminderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user()->load('student');
})->middleware('auth:sanctum');

// =====================================================================
// Authentication (Public)
// =====================================================================
Route::post('/register', [AuthController::class, 'register']);
    Route::get('/registration/metadata', [AuthController::class, 'metadata']);
    Route::get('/study-plans/levels', [StudyPlanController::class, 'availableLevels']);
    Route::get('/majors/{major}/courses', [\App\Http\Controllers\Api\Academics\MajorController::class, 'courses']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::post('/validate-otp', [AuthController::class, 'validateOtp']);

// Academics (Public for Registration)
Route::apiResource('majors', MajorController::class);
Route::apiResource('study-plans', StudyPlanController::class);
Route::get('study-plans/{study_plan}/courses', [StudyPlanController::class, 'courses']);
Route::get('courses/search', [CourseController::class, 'search']);
Route::apiResource('courses', CourseController::class);
Route::apiResource('study-plan-courses', StudyPlanCourseController::class);

Route::middleware('auth:sanctum')->group(function () {
    // Tutoring Requests
    Route::get('tutoring-requests/me', [TutoringRequestController::class, 'myRequests']);
    Route::post('tutoring-requests', [TutoringRequestController::class, 'store']);
    
    // Course Explanations
    Route::get('explanations/courses', [ExplanationController::class, 'courses']);
    Route::get('explanations/course/{courseId}', [ExplanationController::class, 'index']);
    Route::post('explanations', [ExplanationController::class, 'store']);
    Route::get('my-approved-courses', [ExplanationController::class, 'myApprovedCourses']);
    Route::post('explanations/{id}/view', [ExplanationController::class, 'incrementViews']);

    // Authentication (Protected)
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // =====================================================================
    // 2. Tasks & Reminders
    // =====================================================================
    Route::apiResource('tasks', TaskController::class);
    Route::apiResource('task-reminders', TaskReminderController::class);

    // =====================================================================
    // 3. Users & Authentication
    // =====================================================================
    Route::apiResource('users', UserController::class);
    Route::apiResource('students', StudentController::class);
    Route::apiResource('otps', OtpController::class);

    // =====================================================================
    // 4. Project Boards
    // =====================================================================
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('project-files', ProjectFileController::class);

    // =====================================================================
    // 5. Communities & Workshops
    // =====================================================================
    Route::apiResource('communities', CommunityController::class);
    Route::post('communities/{community}/join', [CommunityController::class, 'join']);
    Route::delete('communities/{community}/leave', [CommunityController::class, 'leave']);
    
    // Social Feed
    Route::get('social-feed', [SocialFeedController::class, 'index']);
    Route::get('my-requests', [SocialFeedController::class, 'myRequests']);
    Route::apiResource('workshops', WorkshopController::class);
    Route::apiResource('announcements', AnnouncementController::class);

    // =====================================================================
    // 6. Schedule Builder
    // =====================================================================
    Route::apiResource('schedules', ScheduleController::class);
    Route::get('/my-schedule', [ScheduleController::class, 'mySchedule']);
    Route::delete('/my-schedule', [ScheduleController::class, 'resetMySchedule']);
    Route::apiResource('schedule-items', ScheduleItemController::class);
});
