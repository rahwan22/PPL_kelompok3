<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController; // Perhatikan alias 'as BaseController'

class Controller extends BaseController // Dan gunakan 'BaseController' di sini
{
    use AuthorizesRequests, ValidatesRequests;
}