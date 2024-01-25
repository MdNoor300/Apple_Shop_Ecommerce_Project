<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use App\Helper\ResponseHelper;

use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function BrandList(): JsonResponse
    {
        $data = Brand::all();
        return ResponseHelper::Out('success', $data, 200);
    }
}
