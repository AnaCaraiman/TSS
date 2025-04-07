<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class CatalogController
{
    private string $catalogServiceUrl;
    public function __construct(){
        $this->catalogServiceUrl = config('services.ms_catalog.url');
    }

    public function getCatalog(Request $request):JsonResponse
    {
        $response = Http::get($this->catalogServiceUrl . '/api/ms-catalog',http_build_query($request->query()));
        return response()->json(json_decode($response->getBody()->getContents(),true));
    }

}
