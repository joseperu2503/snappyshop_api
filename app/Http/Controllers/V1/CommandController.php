<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CommandController extends Controller
{
    public function migration()
    {
        Artisan::call('migrate');
        return [
            'success' => true,
            'message' => 'ok'
        ];
    }
}
