<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressCollection;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class AddressController extends Controller
{
    public function addresses()
    {
        $user_id = auth()->user()->id;
        $addresses = Address::where('user_id', $user_id)
            ->orderBy('default', 'desc')
            ->orderBy('id', 'desc')
            ->where('is_active', true)
            ->get();

        return new AddressCollection($addresses);
    }

    public function store(AddressRequest $request)
    {
        DB::beginTransaction();
        try {
            $default = false;
            $user_id = auth()->user()->id;
            $addresses = Address::where('user_id', $user_id)->where('is_active', true)->get();
            if ($addresses->count() == 0) {
                $default = true;
            }

            $address = new Address();
            $address->user_id = $user_id;
            $address->default = $default;
            $address->address = $request->address;
            $address->detail = $request->detail;
            $address->recipient_name = $request->recipient_name;
            $address->phone = $request->phone;
            $address->references = $request->references;
            $address->country = $request->country;
            $address->locality = $request->locality;
            $address->plus_code = $request->plus_code;
            $address->postal_code = $request->postal_code;
            $address->latitude = $request->latitude;
            $address->longitude = $request->longitude;
            $address->save();

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

    public function update(AddressRequest $request, Address $address)
    {
        DB::beginTransaction();
        try {
            $user_id = auth()->user()->id;

            if ($address->user_id != $user_id) {
                return response()->json([
                    'success' => false,
                    'message' => "You don't have permission to update this address"
                ], 401);
            }

            if ($request->default == true) {
                Address::where('user_id', $user_id)
                    ->update(['default' => false]);
                $address->default = true;
            }

            $address->address = $request->address;
            $address->detail = $request->detail;
            $address->recipient_name = $request->recipient_name;
            $address->phone = $request->phone;
            $address->references = $request->references;
            $address->country = $request->country;
            $address->locality = $request->locality;
            $address->plus_code = $request->plus_code;
            $address->postal_code = $request->postal_code;
            $address->latitude = $request->latitude;
            $address->longitude = $request->longitude;
            $address->save();

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
            ], 401);
        }

        return new AddressResource($address);
    }

    public function destroy(Address $address)
    {
        $user_id = auth()->user()->id;
        if ($address->user_id != $user_id) {
            return response()->json([
                'success' => false,
                'message' => "You don't have permission to delete this address"
            ], 401);
        }

        DB::beginTransaction();
        try {
            if ($address->default) {
                $defaultAddress = Address::where('user_id', $user_id)
                    ->where('is_active', true)
                    ->where('id', '!=',  $address->id)
                    ->orderBy('id', 'desc')
                    ->first();


                Address::where('user_id', $user_id)
                    ->update(['default' => false]);

                $defaultAddress->default = true;
                $defaultAddress->save();
            }

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

    public function autocomplete(Request $request)
    {
        $input = $request->input('input');
        $apiKey = env('GOOGLE_MAPS_API_KEY');


        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/autocomplete/json', [
                'input' => $input,
                'key' => $apiKey,
            ]);

            $data = $response->json();

            $predictions = collect($data['predictions'])->map(function ($prediction) {
                return [
                    'place_id' => $prediction['place_id'],
                    'structured_formatting' => [
                        'main_text' => $prediction['structured_formatting']['main_text'],
                        'secondary_text' => $prediction['structured_formatting']['secondary_text'],
                    ],
                ];
            });

            return $predictions;
        } catch (Throwable $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function placeDetails(Request $request)
    {
        $placeId = $request->input('place_id');
        $apiKey = env('GOOGLE_MAPS_API_KEY');

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/details/json', [
                'place_id' => $placeId,
                'key' => $apiKey,
                'fields' => 'geometry'
            ]);

            $data = $response->json();

            return $data['result']['geometry'];
        } catch (Throwable $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function geocode(Request $request)
    {
        $latitude = $request->input('lat');
        $longitude = $request->input('lng');

        $apiKey = env('GOOGLE_MAPS_API_KEY');

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $apiKey,
            ]);

            $data = $response->json();

            // Verificar si existe plus_code
            $plus_code = isset($data['plus_code']['global_code']) ? $data['plus_code']['global_code'] : '';


            $results = $data['results'];

            // Buscar el primer resultado con un componente de tipo 'route'
            foreach ($results as $result) {
                $routeComponent = collect($result['address_components'])->first(function ($component) {
                    return in_array('route', $component['types']);
                });

                if ($routeComponent) {
                    // Encontrar country, locality y postal_code
                    $countryComponent = collect($result['address_components'])->first(function ($component) {
                        return in_array('country', $component['types']);
                    });

                    $localityComponent = collect($result['address_components'])->first(function ($component) {
                        return in_array('locality', $component['types']);
                    });

                    $postalCodeComponent = collect($result['address_components'])->first(function ($component) {
                        return in_array('postal_code', $component['types']);
                    });

                    return response()->json([
                        'address' => $routeComponent['long_name'],
                        'country' => $countryComponent ? $countryComponent['long_name'] : '',
                        'locality' => $localityComponent ? $localityComponent['long_name'] : '',
                        'postal_code' => $postalCodeComponent ? $postalCodeComponent['long_name'] : '',
                        'plus_code' => $plus_code,
                    ]);
                }
            }
        } catch (Throwable $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function geocodeFull(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $apiKey = env('GOOGLE_MAPS_API_KEY');

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $apiKey,
            ]);

            return $response->json();
        } catch (Throwable $e) {

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
