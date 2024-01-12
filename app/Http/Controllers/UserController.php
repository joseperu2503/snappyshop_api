<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ChangePersonalDataRequest;
use App\Http\Requests\ChangeProfilePhotoRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function changePassword(ChangePasswordRequest $request)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);

        $user->update([
            'password' => $request->password
        ]);

        return [
            'success' => true,
            'message' => 'Password changed successfully.'
        ];
    }

    public function changePersonalData(ChangePersonalDataRequest $request)
    {
        $user_id = auth()->user()->id;
        $user = User::find($user_id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'profile_photo' => $request->profile_photo,
        ]);

        return [
            'success' => true,
            'message' => 'Personal data changed successfully.',
            'data' => $user,
        ];
    }
}
