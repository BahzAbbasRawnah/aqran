<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Resources\Tasks\TaskResource;
use App\Models\Task;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $tasks = Task::with(['reminders'])->paginate(15);
        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request): TaskResource
    {
        $validated = $request->validated();

        $task = \DB::transaction(function () use ($validated) {
            // Extract reminders to prevent database column matching issues
            $taskData = array_diff_key($validated, ['reminders' => 1]);
            $task = Task::create($taskData);

            if (isset($validated['reminders']) && is_array($validated['reminders'])) {
                $task->reminders()->createMany($validated['reminders']);
            }

            return $task;
        });

        return new TaskResource($task->load(['reminders']));
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): TaskResource
    {
        return new TaskResource($task->load(['reminders']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): TaskResource
    {
        $validated = $request->validated();

        $task = \DB::transaction(function () use ($task, $validated) {
            // Extract reminders to prevent database column matching issues
            $taskData = array_diff_key($validated, ['reminders' => 1]);
            $task->update($taskData);

            if (isset($validated['reminders']) && is_array($validated['reminders'])) {
                // Delete existing reminders and sync with the new payload
                $task->reminders()->delete();
                $task->reminders()->createMany($validated['reminders']);
            }

            return $task;
        });

        return new TaskResource($task->load(['reminders']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): Response
    {
        $task->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
