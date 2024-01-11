<?php

namespace App\Http\Controllers;

use App\Http\Requests\SnappyTokenRequest;
use App\Models\SnappyToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SnappyTokenController extends Controller
{
    public function store(SnappyTokenRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;
            SnappyToken::updateOrCreate(
                [
                    'token' => $request->token
                ],
                ['user_id' => $user_id,]
            );

            DB::commit();
            return [
                'success' => true,
                'message' => 'Snappy token registered successfully'
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
