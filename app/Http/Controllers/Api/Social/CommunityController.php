<?php

namespace App\Http\Controllers\Api\Social;

use App\Http\Controllers\Controller;
use App\Http\Requests\Social\StoreCommunityRequest;
use App\Http\Requests\Social\UpdateCommunityRequest;
use App\Http\Resources\Social\CommunityResource;
use App\Models\Community;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use App\Enums\ContentStatus;

class CommunityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $communities = Community::where('status', ContentStatus::APPROVED)
            ->with(['major'])
            ->paginate(15);
        return CommunityResource::collection($communities);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommunityRequest $request): CommunityResource
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['status'] = ContentStatus::PENDING;
        
        $community = Community::create($data);
        return new CommunityResource($community->load('major'));
    }

    public function show(Community $community): CommunityResource
    {
        return new CommunityResource($community->load(['major', 'members']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunityRequest $request, Community $community): CommunityResource
    {
        $community->update($request->validated());
        return new CommunityResource($community->load('major'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Community $community): Response
    {
        $community->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Join the specified community.
     */
    public function join(Community $community)
    {
        $user = auth()->user();

        if ($community->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already joined'], 422);
        }

        \DB::transaction(function () use ($community, $user) {
            $community->members()->attach($user->id, ['joined_at' => now()]);
        });

        return new CommunityResource($community->load(['major']));
    }

    /**
     * Leave the specified community.
     */
    public function leave(Community $community)
    {
        $user = auth()->user();

        if (!$community->members()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Not a member'], 422);
        }

        \DB::transaction(function () use ($community, $user) {
            $community->members()->detach($user->id);
        });

        return new CommunityResource($community->load(['major']));
    }
}
