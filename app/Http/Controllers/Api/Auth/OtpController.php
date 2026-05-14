<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StoreOtpRequest;
use App\Http\Requests\Auth\UpdateOtpRequest;
use App\Http\Resources\Auth\OtpResource;
use App\Models\Otp;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class OtpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $otps = Otp::paginate(15);
        return OtpResource::collection($otps);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOtpRequest $request): OtpResource
    {
        $otp = Otp::create($request->validated());
        return new OtpResource($otp);
    }

    /**
     * Display the specified resource.
     */
    public function show(Otp $otp): OtpResource
    {
        return new OtpResource($otp);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOtpRequest $request, Otp $otp): OtpResource
    {
        $otp->update($request->validated());
        return new OtpResource($otp);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Otp $otp): Response
    {
        $otp->delete();
        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
