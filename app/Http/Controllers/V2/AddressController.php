<?php

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\V2\AddressCollection;
use App\Http\Resources\V2\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class AddressController extends Controller
{
    public function myAddresses()
    {
        $user_id = auth()->user()->id;
        $addresses = Address::where('user_id', $user_id)->orderBy('primary', 'desc')->orderBy('id', 'desc')
            ->where('is_active', true)->paginate(10);

        return new AddressCollection($addresses);
    }

    public function store(AddressRequest $request)
    {
        DB::beginTransaction();
        try {
            $primary = false;
            $user_id = auth()->user()->id;
            $addresses = Address::where('user_id', $user_id)->where('is_active', true)->get();
            if ($addresses->count() == 0) {
                $primary = true;
            }

            $address =  Address::create($request->all() + [
                'user_id' => $user_id,
                'primary' =>  $primary,
            ]);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Address registered successfully',
                'data' => new AddressResource($address),
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function markAsPrimary(Address $address)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;

            if ($address->user_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => "You don't have permission to update this address"
                ], 200);
            }

            Address::where('user_id', $user_id)
                ->update(['primary' => false]);
            $address = Address::find($address->id);
            $address->update(['primary' => true]);

            DB::commit();
            return [
                'success' => true,
                'message' => 'Address updated successfully'
            ];
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Address $address)
    {
        $user_id = auth()->user()->id;
        if ($address->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to see this address"
            ], 200);
        }

        return new AddressResource($address);
    }

    public function destroy(Address $address)
    {
        $user_id = auth()->user()->id;
        if ($address->primary) {
            $newPrimaryAddress = Address::where('user_id', $user_id)
                ->where('is_active', true)
                ->where('id', '!=',  $address->id)
                ->orderBy('id', 'desc')
                ->first();

            $this->markAsPrimary($newPrimaryAddress);
        }
        if ($address->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to delete this address"
            ], 401);
        }

        DB::beginTransaction();
        try {

            $address->update([
                'is_active' => false,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ], 200);
        } catch (Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
