<?php

namespace App\Http\Controllers\Api\Schedules;

use App\Http\Controllers\Controller;
use App\Http\Requests\Schedules\StoreScheduleItemRequest;
use App\Http\Requests\Schedules\UpdateScheduleItemRequest;
use App\Http\Resources\Schedules\ScheduleItemResource;
use App\Models\ScheduleItem;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ScheduleItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $items = ScheduleItem::with(['schedule', 'course'])->paginate(15);
        return ScheduleItemResource::collection($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreScheduleItemRequest $request): ScheduleItemResource
    {
        $item = ScheduleItem::create($request->validated());
        return new ScheduleItemResource($item);
    }

    /**
     * Display the specified resource.
     */
    public function show(ScheduleItem $scheduleItem): ScheduleItemResource
    {
        return new ScheduleItemResource($scheduleItem->load(['schedule', 'course']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateScheduleItemRequest $request, ScheduleItem $scheduleItem): ScheduleItemResource
    {
        $scheduleItem->update($request->validated());
        return new ScheduleItemResource($scheduleItem);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ScheduleItem $scheduleItem): Response
    {
        $scheduleItem->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
