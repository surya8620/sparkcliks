<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Models\Service;
use App\Lib\CurlRequest;
use App\Models\ApiProvider;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiProviderController extends Controller
{
    public function index()
    {
        $pageTitle    = "Api Providers";
        $apiProviders = ApiProvider::orderBy('name')->paginate(getPaginate());
        return view('admin.api_provider.index', compact('pageTitle', 'apiProviders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required',
            'api_url'    => 'required|url',
            'api_key'    => 'required',
            'short_name' => 'required|max:4',

        ]);
        if ($request->id) {
            $apiProvider = ApiProvider::findOrFail($request->id);
            $message     = "API provider updated successfully";
        } else {
            $apiProvider = new ApiProvider();
            $message     = "API provider added successfully";
        }

        $apiProvider->name       = $request->name;
        $apiProvider->short_name = $request->short_name;
        $apiProvider->api_url    = $request->api_url;
        $apiProvider->api_key    = $request->api_key;
        $apiProvider->save();

        $notify[] = ['success', $message];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return ApiProvider::changeStatus($id);
    }

    public function balanceUpdate($id)
    {
            $apiProvider = ApiProvider::active()->findOrFail($id);
            $arr = [
                'key'    => $apiProvider->api_key,
                'action' => 'balance',
            ];
            $response = CurlRequest::curlPostContent($apiProvider->api_url, $arr);
            $response = json_decode($response);

            if (@$response->error) {
                $notify[] = ['error', $response->error];
                return back()->withNotify($notify);
            }

            $apiProvider->balance = $response->balance;
            $apiProvider->currency = @$response->currency ?? gs('cur_text');
            $apiProvider->save();

            $notify[] = ['success', 'API provider balance updated successfully'];
            return back()->withNotify($notify);
    }

    public function serviceSync($id)
    {
        $apiProvider = ApiProvider::active()->findOrFail($id);
        $arr = [
            'key'    => $apiProvider->api_key,
            'action' => 'services',
        ];
        $response = CurlRequest::curlPostContent($apiProvider->api_url, $arr);
        $response = json_decode($response);
        if (@$response->error) {
            $notify[] = ['error', $response->error];
            return back()->withNotify($notify);
        }
        $serviceIds = Service::where('api_provider_id', $id)->pluck('api_service_id')->toArray();
        $apiServiceIds = array_map(function ($service) {
            return $service->service;
        }, $response);

        $relevantServiceIds = array_intersect($apiServiceIds, $serviceIds);

        $filteredServices = array_filter($response, function ($service) use ($relevantServiceIds) {
            return in_array($service->service, $relevantServiceIds);
        });

        foreach ($filteredServices as $filterService) {
            $service = Service::where('api_service_id', $filterService->service)->firstOrFail();
            $service->original_price = $filterService->rate;
            $service->min            = $filterService->min;
            $service->max            = $filterService->max;
            $service->dripfeed       = $filterService->dripfeed ? Status::YES : Status::NO;
            $service->refill         = $filterService->refill ? Status::YES : Status::NO;
            $service->save();
        }

        $notify[] = ['success', 'API provider service sync completed successfully'];
        return back()->withNotify($notify);
    }
}
