<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordExternalRequest;
use App\Http\Requests\ChangePasswordInternalRequest;
use App\Http\Requests\ChangePersonalDataRequest;
use App\Http\Requests\SendVerifyCodeRequest;
use App\Http\Requests\ValidateVerifyCodeRequest;
use App\Mail\VerifyCodeMail;
use App\Models\User;
use App\Models\VerifyCode;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function changePasswordInternal(ChangePasswordInternalRequest $request)
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

    public function changePasswordExternal(ChangePasswordExternalRequest $request)
    {

        DB::beginTransaction();
        try {

            $user = User::where('email', $request->email)->first();

            $verify_code = VerifyCode::where('user_id', $user->id)
                ->where('uuid', $request->uuid)
                ->where('code', $request->code)
                ->where('is_verified', true)
                ->first();
            if (!$verify_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect data'
                ], 422);
            }

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

    public function changePersonalData(ChangePersonalDataRequest $request)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;
            $user = User::find($user_id);
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'profile_photo' => $request->profile_photo,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Personal data changed successfully.',
                'data' => $user,
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function sendVerifyCode(SendVerifyCodeRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'The email entered is not registered.'
                ], 422);
            }

            $code = mt_rand(1000, 9999);
            $uuid = Str::uuid();
            $expiration_date = (Carbon::now())->addMinute();
            VerifyCode::create([
                'user_id' => $user->id,
                'code' => $code,
                'uuid' => $uuid,
                'is_verified' => false,
                'expiration_date' => $expiration_date,
            ]);

            Mail::to($user->email)->send(new VerifyCodeMail($code));

            DB::commit();

            return [
                'success' => true,
                'message' => 'Verify code sended successfully.',
                'data' => [
                    'uuid' => $uuid,
                    'expiration_date' => $expiration_date,
                ],
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function validateVerifyCode(ValidateVerifyCodeRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::where('email', $request->email)->first();

            $verify_code = VerifyCode::where('user_id', $user->id)
                ->where('uuid', $request->uuid)->first();
            if (!$verify_code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect data'
                ], 422);
            }

            if ($verify_code->code != $request->code) {
                return response()->json([
                    'success' => false,
                    'message' => 'Incorrect code'
                ], 422);
            }

            if (!Carbon::now()->isBefore($verify_code->expiration_date)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Expired code'
                ], 422);
            }

            $verify_code->update([
                'is_verified' => true,
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Code verified correctly',
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
