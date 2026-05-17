<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Workshop;
use App\Models\TutoringRequest;
use App\Models\Community;
use Illuminate\Http\Request;
use App\Http\Resources\Social\WorkshopResource;
use App\Http\Resources\Social\AnnouncementResource;
use App\Http\Resources\Social\CommunityResource;

class SocialFeedController extends Controller
{
    /**
     * Display a unified feed of workshops and announcements.
     */
    public function index(Request $request)
    {
        $limit = $request->get('limit', 15);
        $userMajorId = $request->user()?->student?->major_id;

        $workshops = Workshop::where('status', 'approved')
            ->where(function ($query) use ($userMajorId) {
                $query->doesntHave('targetMajors')
                    ->when($userMajorId, function ($q) use ($userMajorId) {
                        $q->orWhereHas('targetMajors', function ($q) use ($userMajorId) {
                            $q->where('majors.id', $userMajorId);
                        });
                    });
            })
            ->with(['user', 'targetMajors'])
            ->latest()
            ->get();

        $announcements = Announcement::where('status', 'approved')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->where(function ($query) use ($userMajorId) {
                $query->doesntHave('majors')
                    ->when($userMajorId, function ($q) use ($userMajorId) {
                        $q->orWhereHas('majors', function ($q) use ($userMajorId) {
                            $q->where('majors.id', $userMajorId);
                        });
                    });
            })
            ->with(['majors'])
            ->latest()
            ->get();

        // Merge and sort
        $merged = $workshops->map(function ($item) {
            $item->feed_type = 'workshop';
            return $item;
        })->concat($announcements->map(function ($item) {
            $item->feed_type = 'announcement';
            return $item;
        }))->sortByDesc('created_at')->values();

        return response()->json([
            'data' => $merged->map(function ($item) {
                if ($item->feed_type === 'workshop') {
                    return [
                        'type' => 'workshop',
                        'data' => new WorkshopResource($item),
                        'created_at' => $item->created_at,
                    ];
                } else {
                    return [
                        'type' => 'announcement',
                        'data' => new AnnouncementResource($item),
                        'created_at' => $item->created_at,
                    ];
                }
            })
        ]);
    }

    /**
     * Display a unified list of the user's own requests/submissions.
     */
    public function myRequests(Request $request)
    {
        $user = $request->user();

        // 1. Tutoring Requests
        $tutoringRequests = collect();
        if ($user->student) {
            $tutoringRequests = TutoringRequest::where('student_id', $user->student->id)
                ->with('course:id,name,code')
                ->latest()
                ->get();
        }

        // 2. Workshops
        $workshops = Workshop::where('user_id', $user->id)
            ->with(['targetMajors'])
            ->latest()
            ->get();

        // 3. Announcements
        $announcements = Announcement::where('user_id', $user->id)
            ->with(['majors'])
            ->latest()
            ->get();

        // 4. Communities
        $communities = Community::where('user_id', $user->id)
            ->with(['major'])
            ->latest()
            ->get();

        // Merge and sort
        $merged = $tutoringRequests->map(function ($item) {
            $item->feed_type = 'tutoring_request';
            return $item;
        })->concat($workshops->map(function ($item) {
            $item->feed_type = 'workshop';
            return $item;
        }))->concat($announcements->map(function ($item) {
            $item->feed_type = 'announcement';
            return $item;
        }))->concat($communities->map(function ($item) {
            $item->feed_type = 'community';
            return $item;
        }))->sortByDesc('created_at')->values();

        return response()->json([
            'data' => $merged->map(function ($item) {
                $type = $item->feed_type;
                
                $status = null;
                if ($item->status !== null) {
                    $status = $item->status instanceof \BackedEnum ? $item->status->value : $item->status;
                }

                if ($type === 'workshop') {
                    return [
                        'type' => 'workshop',
                        'status' => $status,
                        'data' => new WorkshopResource($item),
                        'created_at' => $item->created_at,
                    ];
                } elseif ($type === 'announcement') {
                    return [
                        'type' => 'announcement',
                        'status' => $status,
                        'data' => new AnnouncementResource($item),
                        'created_at' => $item->created_at,
                    ];
                } elseif ($type === 'community') {
                    return [
                        'type' => 'community',
                        'status' => $status,
                        'data' => new CommunityResource($item),
                        'created_at' => $item->created_at,
                    ];
                } else {
                    return [
                        'type' => 'tutoring_request',
                        'status' => $status,
                        'data' => $item, // Raw tutoring request for now
                        'created_at' => $item->created_at,
                    ];
                }
            })
        ]);
    }
}
