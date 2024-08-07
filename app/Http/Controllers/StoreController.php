<?php

namespace App\Http\Controllers;

use App\Http\Resources\StoreCollection;
use App\Http\Resources\StoreResource;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::where('is_active', true)->paginate(10);
        return  $this->paginateMapper(new StoreCollection($stores));
    }
}
