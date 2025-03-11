<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthServices;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public $authservices;
    public function __construct(AuthServices $authservices)
    {
        $this->authservices = $authservices;
    }
}
