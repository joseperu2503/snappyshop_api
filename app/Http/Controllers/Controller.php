<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function paginateMapper($collection)
    {
        return [
            "results" => $collection->collection,
            'info' => [
                "per_page" => ($collection->perPage()),
                "current_page" => ($collection->currentPage()),
                "last_page" => ($collection->lastPage()),
                "total" => ($collection->total()),
            ],
        ];
    }
}
