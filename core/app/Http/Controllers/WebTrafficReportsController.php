<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\GeneralSetting;
use App\Models\WebTrafficReports;
use App\Models\Clicks;
use App\Models\Order;
use App\Models\User;
use App\Lib\CurlRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;


class WebTrafficReportsController extends Controller
{

	public function hit(Request $request, $id)
	{
		$order = Order::findOrFail($id);

		$ip = getRealIP();
		if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
		    // IPv4 handling
		    $parts = explode('.', $ip);
		    $parts[1] = 'xxx';
		    $parts[2] = 'xxx';
		    $maskedIp = implode('.', $parts);

		} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
		    // IPv6 handling
		    $parts = explode(':', $ip);
		    $parts[1] = 'xxxx';
		    $parts[2] = 'xxxx';
		    $maskedIp = implode(':', $parts);
		}
		$data = new WebTrafficReports();
		$data->user_id = $order->user_id;
		$data->order_id = $order->id;
		$data->traffic_plan = $order->traffic_plan;
		$data->category_id = $order->category_id;
		$data->ip = $ip;
		$data->save();

		if ($order->category_id == 11 || $order->category_id == 12) {
			$info = json_decode(json_encode(getIpInfo()), true);
			$click = new Clicks();
			$click->user_id = $order->user_id;
			$click->order_id = $order->id;
			$click->link = $order->link;
			$click->keyword = $order->keyword;
			$click->clicker_region = @implode(',', $info['country']);
			$click->clicker_country = @implode(',', $info['code']);
			$click->clicker_ip = $maskedIp;
			$click->save();
		}

	}

	public function countCheck()
	{
		$today = Carbon::now()->startOfDay();
		$order_ids = WebTrafficReports::where('created_at', '>=', $today)->pluck('order_id')->unique();
		$orders = Order::whereIn('id', $order_ids)->get();
		foreach ($orders as $order) {
			if ($order->status == 1 || $order->status == 0) {
			$hits = WebTrafficReports::where('order_id', $order->id)->where('created_at', '>=', $order->created_at)->count();
			$order->start_counter = $hits;
			$order->remain = $order->quantity - $order->start_counter;
			$order->save();
			}
		}
		$general = GeneralSetting::first();
		$general->last_report_update = now();
		$general->save();

	}

	public function delete()
	{
		$date = Carbon::now()->subDays(45);
		WebTrafficReports::where('created_at', '<', $date)->delete();
	}

	public function completed()
	{
    		$orders = Order::all();
    		foreach ($orders as $order) {
        		if ($order->start_counter > $order->quantity && $order->status == 1) {
		            $order->status = 2;
		            $order->save();
			$user = $order->user;
	//Send email to user
            if($order->category_id == 17){
            notify($user, 'WEB_COMPLETED', [
                'service_name' => $order->service->name,
                'order_id' => $order->id,
                'name' => $order->name,
                'link' => urldecode($order->link),
                'clicks' => $order->start_counter,
            ]);
			$response = CurlRequest::curlPostContent($order->provider->api_url, [
					'key' => $order->provider->api_key,
					'action' => "completed",
					'id' => $order->api_order_id,
					]);    
			} elseif($order->category_id == 20){
            notify($user, 'REALISTIC_COMPLETED', [
                'service_name' => $order->service->name,
                'order_id' => $order->id,
                'name' => $order->name,
                'link' => urldecode($order->link),
                'clicks' => $order->start_counter,
            ]);
			$response = CurlRequest::curlPostContent($order->provider->api_url, [
					'key' => $order->provider->api_key,
					'action' => "completed",
					'id' => $order->api_order_id,
					]);
            } else {
            notify($user, 'COMPLETED_ORDER', [
                    'service_name' => $order->service->name,
                    'order_id' => $order->id,
                    'link' => urldecode($order->link),
                    'keyword' => $order->keyword,
                    'clicks' => $order->start_counter,
                    'clicks/day' => $order->clicks,
                    'attempt' => $order->attempt
                ]);
            }

		        }
		    }
	}

	public function expired()
	{
		$date = Carbon::now();
    		$orders = Order::all();
    		foreach ($orders as $order) {
        		if ($order->category_id == 17 && (($order->status == 0 || $order->status == 6 || $order->status == 1) && $order->traffic_exp < $date && $order->traffic_plan == 101)){
		            $order->status = 5;
		            $order->save();
					$response = CurlRequest::curlPostContent($order->provider->api_url, [
						'key' => $order->provider->api_key,
						'action' => "completed",
						'id' => $order->api_order_id,
						]);
				}
			}	
	}

public function reportAPI(Request $request)
{
    $api_key = 'a776dhsja76dgw0';
    $id = $request->order_id;
    
    if (!$request->token) {
        return response()->json(["error" => "Api token required"]);
    }
    if ($request->token !== $api_key) {
        return response()->json(['error' => 'Invalid api key']);
    }
    
    try {
        $order = Order::findOrFail($id);
    } catch (ModelNotFoundException $e) {
        return response()->json(['visits' => '0']);
    }
    
    $start = Carbon::now()->startOfDay();
    
    // Get actual visit count for today
    $visits = WebTrafficReports::where('order_id', $id)
        ->whereDate('created_at', '=', $start->toDateString())
        ->count();
    
    // Return the actual visit count for the current date
    return response()->json(['visits' => $visits]);
}

		public function reportAPIOld(Request $request)
	{
		$api_key = 'a776dhsja76dgw0';
		$id = $request->order_id;
		if (!$request->token) {
			return response()->json(["error" => "Api token required"]);
		}
		if ($request->token !== $api_key) {
			return response()->json(['error' => 'Invalid api key']);
		}
		try {
			$order = Order::findOrFail($id);
		} catch (ModelNotFoundException $e) {
			return response()->json(['visits' => '0']);
		}
		$start = Carbon::now()->startOfDay(); // Start of the current day

		if ($order->service_id != 101) {
		    $remaining = ($order->quantity - $order->start_counter);
		    $rem = ($order->speed - $remaining);
    
		    if ($remaining < $order->speed) {
		        return response()->json([
		            'visits' => $rem
		        ]);
		    }
		}
		$data = WebTrafficReports::selectRaw('DATE(created_at) as created_date, COUNT(*) as count')
			->where('order_id', $id)
			->whereDate('created_at', '=', $start->toDateString()) // Only for the current date
			->first();
		
		// Extract visit count for the current date
		$visits = $data ? $data->count : 0;
		
		// Return the visit count for the current date
		return response()->json(['visits' => $visits]);
	}

	public function reportAPI2(Request $request)
	{
    		$api_key = 'a776dhsja76dgw0';
    		$id = $request->order_id;

    		if (!$request->token) {
    		    return response()->json(["error" => "Api token required"]);
   		 }

   		 if ($request->token !== $api_key) {
  		      return response()->json(['error' => 'Invalid api key']);
  		  }

  		  try {
  		      $order = Order::findOrFail($id);
  		  } catch (ModelNotFoundException $e) {
  		      return response()->json(['visits' => '0']);
  		  }

   		 // Get the start date for the last 30 days
   		 $startDate = Carbon::now()->subDays(10)->startOfDay();
  		  $visits = WebTrafficReports::where('order_id', $id)
                                ->where('created_at', '>=', $order->created_at)
                                ->count();
    
   		 // Return the visit count for the last 30 days
   		 return response()->json(['visits' => $visits]);
	}
	public function statusApi(Request $request)
	{
		$api_key = 'a776dhsja76dgw0'; 
		$id = $request->order_id;

		// Check if token is provided
		if (!$request->has('token')) {
			return response()->json(["error" => "API token is required"], 400);
		}

		// Validate token
		if ($request->token !== $api_key) {
			return response()->json(['error' => 'Invalid API token'], 401);
		}

		// Validate order_id
		if (!$id) {
			return response()->json(['error' => 'ID is required'], 400);
		}

		// Fetch the order
		$order = Order::find($id);

		if (!$order) {
			return response()->json(['error' => 'Not found'], 404);
		}

		// Return order status
		return response()->json([
			'apiId' => $order->api_order_id,
			'id' => $order->id,
			'status' => $order->status
		], 200);
	}

}
