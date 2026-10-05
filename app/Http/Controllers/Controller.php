<?php

namespace App\Http\Controllers;

use App\Models\Wedding;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function getWedding(): Wedding
    {
        return Wedding::firstOrFail();
    }
}
