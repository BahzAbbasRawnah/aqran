<?php

namespace App\Http\Controllers\Api\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskReminderRequest;
use App\Http\Requests\Tasks\UpdateTaskReminderRequest;
use App\Http\Resources\Tasks\TaskReminderResource;
use App\Models\TaskReminder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class TaskReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $reminders = TaskReminder::with(['task'])->paginate(15);
        return TaskReminderResource::collection($reminders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskReminderRequest $request): TaskReminderResource
    {
        $reminder = TaskReminder::create($request->validated());
        return new TaskReminderResource($reminder);
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskReminder $taskReminder): TaskReminderResource
    {
        return new TaskReminderResource($taskReminder->load(['task']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskReminderRequest $request, TaskReminder $taskReminder): TaskReminderResource
    {
        $taskReminder->update($request->validated());
        return new TaskReminderResource($taskReminder);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskReminder $taskReminder): Response
    {
        $taskReminder->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
