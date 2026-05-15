<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\Social\StoreAnnouncementRequest;
use App\Http\Requests\Social\UpdateAnnouncementRequest;
use App\Http\Resources\Social\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use App\Enums\ContentStatus;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $announcements = Announcement::where('status', ContentStatus::APPROVED)
            ->with(['majors'])
            ->paginate(15);
        return AnnouncementResource::collection($announcements);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAnnouncementRequest $request): AnnouncementResource
    {
        $data = $request->validated();
        
        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('announcements/images', 'public');
        }

        $data['user_id'] = auth()->id();
        $data['publish_date'] = now();
        $data['status'] = ContentStatus::PENDING;

        $announcement = Announcement::create($data);
        
        if ($request->has('major_ids')) {
            $announcement->majors()->sync($request->major_ids);
        }

        return new AnnouncementResource($announcement->load(['majors']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Announcement $announcement): AnnouncementResource
    {
        return new AnnouncementResource($announcement->load(['majors']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): AnnouncementResource
    {
        $announcement->update($request->validated());
        
        if ($request->has('major_ids')) {
            $announcement->majors()->sync($request->major_ids);
        }
        
        return new AnnouncementResource($announcement->load(['majors']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): Response
    {
        $announcement->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
