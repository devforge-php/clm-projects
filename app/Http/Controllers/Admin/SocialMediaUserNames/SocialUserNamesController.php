<?php

namespace App\Http\Controllers\Admin\SocialMediaUserNames;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocilaMediaResource;
use App\Models\SocialUserName;
use Illuminate\Http\Request;

class SocialUserNamesController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 5);
        $socialMedia = SocialUserName::cursorPaginate($perPage);
    
        return response()->json([
            'data' => SocilaMediaResource::collection($socialMedia),
            'next_page_url' => $socialMedia->nextPageUrl(),
        ]);
    }
     
    
}
