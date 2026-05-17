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
    public function destroy(\Illuminate\Http\Request $request, ProjectFile $projectFile)
    {
        $user = $request->user();

        // 1. Authorize the user (must be uploader or project member)
        $isUploader = $projectFile->user_id === $user->id;
        $isMember = $projectFile->project && $projectFile->project->members()->where('users.id', $user->id)->exists();

        if (!$isUploader && !$isMember) {
            return response()->json([
                'message' => 'You are not authorized to delete this file.'
            ], 403);
        }

        // 2. Securely delete the physical file from storage
        if ($projectFile->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($projectFile->file_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($projectFile->file_path);
        }

        // 3. Delete the ProjectFile record from the database
        $projectFile->delete();

        return response()->json([
            'message' => 'File deleted successfully.'
        ], 200);
    }
}
