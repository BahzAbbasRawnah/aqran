<?php

namespace App\Http\Controllers\Api\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\Projects\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $projects = Project::with(['members', 'files.uploader'])->paginate(15);
        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): ProjectResource
    {
        return \DB::transaction(function () use ($request) {
            $data = $request->validated();
            unset($data['members'], $data['auto_delete_agreement']);
            
            $project = Project::create($data);

            foreach ($request->members as $member) {
                $project->members()->attach($member['user_id'], [
                    'role' => $member['role'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return new ProjectResource($project->load(['members']));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): ProjectResource
    {
        return new ProjectResource($project->load(['members', 'files.uploader']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project)
    {
        $user = $request->user();

        // Ensure the authenticated user is the leader/owner of the project
        $isLeader = $project->members()
            ->where('users.id', $user->id)
            ->wherePivot('role', 'leader')
            ->exists();

        if (!$isLeader) {
            return response()->json([
                'message' => 'You are not authorized to update this project.'
            ], 403);
        }

        $project->update($request->validated());

        return new ProjectResource($project->load(['members', 'files.uploader']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): Response
    {
        $project->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
