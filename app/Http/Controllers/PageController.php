<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function GetUdmurtia(): JsonResponse {
        $data = Page::where('name', 'about-udmurtia')->first();

        return response()->success($data, "Content fetched");
    }
}
