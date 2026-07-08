<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class UserDataController extends Controller
{
    public function athlete(): JsonResponse
    {
        return response()->json(request()->user()->athleteProfile);
    }

    public function fan(): JsonResponse
    {
        return response()->json(request()->user()->fanProfile);
    }

    public function employee(): JsonResponse
    {
        return response()->json(request()->user()->employeeProfile);
    }
}
