<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $categories = Store::select(
            'id',
            'name',
            'description',
            'website',
            'email',
            'phone',
            'facebook',
            'instagram',
            'logotype',
            'isotype',
            'backdrop',
        )->get();
        return $categories;
    }
}
