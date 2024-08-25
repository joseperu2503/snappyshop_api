<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordInternalRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AccountController extends Controller
{
    public function profile()
    {
        return new UserResource(auth()->user());
    }

    public function updatePassword(ChangePasswordInternalRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;
            $user = User::find($user_id);

            $user->update([
                'password' => $request->password
            ]);
            DB::commit();

            return [
                'success' => true,
                'message' => 'Password changed successfully.'
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;
            $user = User::find($user_id);


            $user2 = User::where('email', $request->email)->whereNot('id', $user_id)->first();
            if ($user2) {
                return response()->json([
                    'message' => 'The email address is already registered.'
                ], 422);
            }

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'profile_photo' => $request->profile_photo,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => new UserResource($user),
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
