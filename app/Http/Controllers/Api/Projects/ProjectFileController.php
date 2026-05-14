<?php

namespace App\Http\Controllers\Api\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreProjectFileRequest;
use App\Http\Requests\Projects\UpdateProjectFileRequest;
use App\Http\Resources\Projects\ProjectFileResource;
use App\Models\ProjectFile;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProjectFileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $files = ProjectFile::with(['project', 'user'])->paginate(15);
        return ProjectFileResource::collection($files);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectFileRequest $request): ProjectFileResource
    {
        $uploadedFile = $request->file('file');
        $path = $uploadedFile->store('projects/files', 'public');

        $file = ProjectFile::create([
            'project_id' => $request->project_id,
            'user_id' => $request->user()->id,
            'is_pinned' => $request->boolean('is_pinned', false),
            'file_path' => $path,
            'file_name' => $uploadedFile->getClientOriginalName(),
            'file_size_mb' => round($uploadedFile->getSize() / (1024 * 1024), 2),
            'description' => $request->description ?? '',
        ]);

        return new ProjectFileResource($file->load(['uploader']));
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectFile $projectFile): ProjectFileResource
    {
        return new ProjectFileResource($projectFile->load(['project', 'user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectFileRequest $request, ProjectFile $projectFile): ProjectFileResource
    {
        $projectFile->update($request->validated());
        return new ProjectFileResource($projectFile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectFile $projectFile): Response
    {
        $projectFile->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
