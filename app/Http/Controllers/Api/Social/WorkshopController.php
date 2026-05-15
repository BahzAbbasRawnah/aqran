<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\Social\StoreWorkshopRequest;
use App\Http\Requests\Social\UpdateWorkshopRequest;
use App\Http\Resources\Social\WorkshopResource;
use App\Models\Workshop;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use App\Enums\ContentStatus;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $workshops = Workshop::where('status', ContentStatus::APPROVED)
            ->with(['user', 'targetMajors'])
            ->paginate(15);
        return WorkshopResource::collection($workshops);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkshopRequest $request): WorkshopResource
    {
        $data = $request->validated();
        
        if ($request->hasFile('video')) {
            $data['video_url'] = $request->file('video')->store('workshops/videos', 'public');
        }
        
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_url'] = $request->file('thumbnail')->store('workshops/thumbnails', 'public');
        }

        $data['user_id'] = $request->user()->id;
        $data['status'] = ContentStatus::PENDING;

        $workshop = Workshop::create($data);

        if ($request->has('target_major_ids')) {
            $workshop->targetMajors()->sync($request->target_major_ids);
        }

        return new WorkshopResource($workshop->load(['user', 'targetMajors']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Workshop $workshop): WorkshopResource
    {
        return new WorkshopResource($workshop->load(['user', 'targetMajors']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWorkshopRequest $request, Workshop $workshop): WorkshopResource
    {
        $workshop->update($request->validated());
        
        if ($request->has('target_major_ids')) {
            $workshop->targetMajors()->sync($request->target_major_ids);
        }
        
        return new WorkshopResource($workshop->load(['user', 'targetMajors']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workshop $workshop): Response
    {
        $workshop->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
