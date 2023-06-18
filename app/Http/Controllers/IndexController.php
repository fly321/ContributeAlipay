<?php

namespace App\Http\Controllers;

use App\Annotation\RequestMapping;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    # hello world
    public function index(Request $request)
    {
        return response()->json([
            'message' => 'No new orders!'
        ]);
    }
}
