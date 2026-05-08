<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Lib\SMM;
use App\Models\CronJob;
use App\Models\CronJobLog;
use Carbon\Carbon;
use App\Models\GeneralSetting;
use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\User;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CronController extends Controller
{
    public function cron()
    {
            $general            = gs();
            $general->last_cron = now();
            $general->save();
    }

	    public function cron2()
    {
        if (gs('cron_status')) {
            $general            = gs();
            $general->last_cron = now();
            $general->save();
            $crons = CronJob::with('schedule');

            if (request()->alias) {
                $crons->where('alias', request()->alias);
            } else {
                $crons->where('next_run', '<', now())->where('is_running', Status::YES);
            }
            $crons = $crons->get();
            foreach ($crons as $cron) {
                $cronLog              = new CronJobLog();
                $cronLog->cron_job_id = $cron->id;
                $cronLog->start_at    = now();
                if ($cron->is_default) {
                    $controller = new $cron->action[0];
                    try {
                        $method = $cron->action[1];
                        $controller->$method();
                    } catch (\Exception $e) {
                        $cronLog->error = $e->getMessage();
                    }
                } else {
                    try {
                        CurlRequest::curlContent($cron->url);
                    } catch (\Exception $e) {
                        $cronLog->error = $e->getMessage();
                    }
                }
                $cron->last_run = now();
                $cron->next_run = now()->addSeconds($cron->schedule->interval);
                $cron->save();

                $cronLog->end_at = $cron->last_run;

                $startTime         = Carbon::parse($cronLog->start_at);
                $endTime           = Carbon::parse($cronLog->end_at);
                $diffInSeconds     = $startTime->diffInSeconds($endTime);
                $cronLog->duration = $diffInSeconds;
                $cronLog->save();
            }
            if (request()->target == 'all') {
                $notify[] = ['success', 'Cron executed successfully'];
                return back()->withNotify($notify);
            }
            if (request()->alias) {
                $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
                return back()->withNotify($notify);
            }
        }
    }

public function invNumber()
{
    $cutoffDate   = Carbon::create(2025, 7, 1);   // Start from July 2025
    $threeDaysAgo = now()->subDays(3);            // Created date must be older than 3 days

    // Fetch deposits matching conditions (no inv_num filter now)
    $deposits = Deposit::where('status', 1)
        ->whereDate('created_at', '>=', $cutoffDate)
        ->whereDate('created_at', '<=', $threeDaysAgo)
	->whereNull('inv_num')
        ->get();

    foreach ($deposits as $deposit) {
        // Current year-month (e.g., 202507)
        $yearMonth = $deposit->created_at->format('Ym');

        // Find last deposit with inv_num in same month
        $lastDeposit = Deposit::whereYear('created_at', $deposit->created_at->year)
            ->whereMonth('created_at', $deposit->created_at->month)
            ->whereNotNull('inv_num')
            ->orderBy('id', 'desc')
            ->first();

        // Get next sequence number
        $nextNumber = 1;
        if ($lastDeposit && preg_match('/(\d{5})$/', $lastDeposit->inv_num, $matches)) {
            $nextNumber = intval($matches[1]) + 1;
        }

        // Generate invoice number like INV-20250700001
        $invoiceNo = "INV-{$yearMonth}" . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // Store invoice number in deposit (override mode)
        $deposit->inv_num = $invoiceNo;
        $deposit->save();
    }
            $general            = gs();
            $general->inv_cron = now();
            $general->save();

}

	public function placeOrderToApi()
	{
		$apiOrders = Order::where('status', 1)
	        ->with('provider')
	        ->where('api_provider_id', '!=', 0)
	        ->where('order_placed_to_api', 0)
	        ->whereIn('api_order_id', [0, 2])
	        ->whereIn('category_id', [17, 20])
	    ->get();
		$general = GeneralSetting::first();
		$general->last_cron = now();
		$general->save();

		foreach ($apiOrders as $order) {
			Log::info('Order ID: ' . $order->id);
			$response = CurlRequest::curlPostContent($order->provider->api_url, [
				'key' => $order->provider->api_key,
				'action' => "add",
				'id' => $order->id,
				'link' => $order->link,
				'link2' => $order->link2,
				'link3' => $order->link3,
				'quantity' => $order->quantity,
				'td' => $order->td,
				'tp' => ($order->tp == 1) ? 30 : (($order->tp == 2) ? 1 : (($order->tp == 3) ? 2 : (($order->tp == 4) ? 3 : (($order->tp == 5) ? 4 : (($order->tp == 6) ? 5 : (($order->tp == 0) ? 30 : $order->tp)))))),
				'tt' => $order->tt,
				'category_id' => $order->category_id,
				'service_id' => $order->service_id,
				'country' => $order->country,
				'speed' => $order->speed,
				'keyword' => $order->keyword,
				'analytics' => $order->analytics,
				'ref' => $order->ref,
				'social' => $order->social,
				'traffic_exp' => $order->traffic_exp,
				'traffic_plan' => $order->traffic_plan,
				'click_type' => $order->click_type,
				'lang' => $order->lang,
				'random_time_page' => $order->random_time_page,
			]);
			if ($response === null) {
				Log::error('API call failed or returned null for order ID: ' . $order->id);
				continue; // Skip to the next iteration of the loop
			}

			$response = json_decode($response);

			if ($response === null || property_exists($response, 'error') && $response->error) {
				Log::error('Error placing order: ' . ($response === null ? 'API response is null' : $response->error));
				continue;
			}
			//Order placed
			if (!empty($response->order)) {
			$order->order_placed_to_api = 1;
			$order->api_order_id = $response->order;
			$order->save();
			Log::info('Order placed successfully. Order ID: ' . $order->id . ', API Order ID: ' . $response->order);
			}
		}
	}

	public function updateOrderToApi()
	{
		$apiOrders = Order::where('status', 1)
		->with('provider')
		->where('api_provider_id', '!=', 0)
		->where('order_placed_to_api', 2)
		->where('api_order_id', '!=', 0)
		->whereIn('category_id', [17, 20])
		->get();
		foreach ($apiOrders as $order) {
			Log::info('Order ID: ' . $order->id);
			$response = CurlRequest::curlPostContent($order->provider->api_url, [
				'key' => $order->provider->api_key,
				'action' => "update",
				'id' => $order->api_order_id,
				'link' => $order->link,
				'link2' => $order->link2,
				'link3' => $order->link3,
				'td' => $order->td,
				'tp' => ($order->tp == 1) ? 30 : (($order->tp == 2) ? 1 : (($order->tp == 3) ? 2 : (($order->tp == 4) ? 3 : (($order->tp == 5) ? 4 : (($order->tp == 6) ? 5 : (($order->tp == 0) ? 30 : $order->tp)))))),
				'tt' => $order->tt,
				'country' => $order->country,
				'speed' => $order->speed,
				'keyword' => $order->keyword,
				'ref' => $order->ref,
				'social' => $order->social,
				'click_type' => $order->click_type,
				'lang' => $order->lang,
				'random_time_page' => $order->random_time_page,
			]);
				if ($response === null) {
				continue; // Skip to the next iteration of the loop
				}

				$response = json_decode($response);

				if ($response === null || property_exists($response, 'error') && $response->error) {
				continue;
				}
				// Check if the response indicates success
				if (property_exists($response, 'order') && $response->order === 'success') {
					// Order placed
					$order->order_placed_to_api = 1;
						$order->save();
			}
		}
	}

	public function stopOrderToApi()
	{
		$apiOrders = Order::where('status', 6)
		->orWhere('status', 0)
		->with('provider')
		->where('api_provider_id', '!=', 0)
		->where('order_placed_to_api', 3)
		->where('api_order_id', '!=', 0)
		->whereIn('category_id', [17, 20])
		->get();
		foreach ($apiOrders as $order) {
			Log::info('Order ID: ' . $order->id);
			$response = CurlRequest::curlPostContent($order->provider->api_url, [
				'key' => $order->provider->api_key,
				'action' => "stop",
				'id' => $order->api_order_id,
				'link' => $order->link,
				'link2' => $order->link2,
				'link3' => $order->link3,
				'td' => $order->td,
				'tp' => ($order->tp == 1) ? 30 : (($order->tp == 2) ? 1 : (($order->tp == 3) ? 2 : (($order->tp == 4) ? 3 : (($order->tp == 5) ? 4 : (($order->tp == 6) ? 5 : (($order->tp == 0) ? 30 : $order->tp)))))),
				'tt' => $order->tt,
				'country' => $order->country,
				'speed' => $order->speed,
				'keyword' => $order->keyword,
				'ref' => $order->ref,
				'social' => $order->social,
				'click_type' => $order->click_type,
				'lang' => $order->lang,
				'random_time_page' => $order->random_time_page,
			]);

				// Log the raw response
				Log::info('Raw Response for Order ID ' . $order->id . ': ' . $response);

			if ($response === null) {
					continue; // Skip to the next iteration of the loop
			}

			$response = json_decode($response);

			if ($response === null || property_exists($response, 'error') && $response->error) {
				continue;
			}
				// Check if the response indicates success
				if (property_exists($response, 'order') && $response->order === 'success') {
					// Order placed
					$order->order_placed_to_api = 1;
						$order->save();
			}
		}
	}

	public function resumeOrderToApi()
	{
		$apiOrders = Order::where('status', 1)
			->with('provider')
			->where('api_provider_id', '!=', 0)
			->where('order_placed_to_api', 4)
			->where('api_order_id', '!=', 0)
			->whereIn('category_id', [17, 20])
			->get();
		foreach ($apiOrders as $order) {
		Log::info('Order ID: ' . $order->id);
		$response = CurlRequest::curlPostContent($order->provider->api_url, [
			'key' => $order->provider->api_key,
			'action' => "resume",
			'id' => $order->api_order_id,
			'link' => $order->link,
			'link2' => $order->link2,
			'link3' => $order->link3,
			'td' => $order->td,
			'tp' => ($order->tp == 1) ? 30 : (($order->tp == 2) ? 1 : (($order->tp == 3) ? 2 : (($order->tp == 4) ? 3 : (($order->tp == 5) ? 4 : (($order->tp == 6) ? 5 : (($order->tp == 0) ? 30 : $order->tp)))))),
			'tt' => $order->tt,
			'country' => $order->country,
			'speed' => $order->speed,
			'keyword' => $order->keyword,
			'ref' => $order->ref,
			'social' => $order->social,
			'click_type' => $order->click_type,
			'lang' => $order->lang,
			'random_time_page' => $order->random_time_page,
		]);
		if ($response === null) {
		continue; // Skip to the next iteration of the loop
		}

		$response = json_decode($response);

		if ($response === null || property_exists($response, 'error') && $response->error) {
		continue;
		}
	        // Check if the response indicates success
	        if (property_exists($response, 'order') && $response->order === 'success') {
	            // Order placed
		        $order->order_placed_to_api = 1;
	            	$order->save();
 		       }
		}
	}


	public function apiPause()
	{
		$apiOrders = Order::where(function ($query) {
        		$query->where('status', 6)
            		->orWhere('status', 0);
   			})
    			->with('provider')
    			->where('traffic_plan', 101)
    			->where('api_provider_id', '!=', 0)
    			->where('api_order_id', '!=', 0)
    			->where('order_placed_to_api', 3)
    			->get();
			Log::info('Orders: ' . $orders);

		foreach ($apiOrders as $order) {
			Log::info('Order ID: ' . $order->id);
			$response = CurlRequest::curlPostContent($order->provider->api_url, [
				'key' => $order->provider->api_key,
				'action' => "pause",
				'status' => $order->status,
			]);
			$response = json_decode($response);

			Log::info($response);
			if ($response && property_exists($response, 'Success') && $response->Success == 1) {
    			// Order Updated
    			$order->order_placed_to_api = 1;
    			$order->save();
			Log::info('Order paused, Status: ' . $response->Success);
			}
		}
		
	}

	public function expiryCheck()
	{
		$users = User::whereIn('mem_type', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15])->get();
		foreach ($users as $user) {
			if (Carbon::now() > $user->mem_exp && $user->trial_exp == 0) {
				if ($user->mem_type == 1) {
					$user->update(['mem_credit' => 0]);
					$user->update(['trial_exp' => 1]);
					notify($user, 'TRIAL_EXPIRY');
				} else {
					if ($user->mem_type >= 10 && $user->mem_type <= 15 && $user->mem_status == 1) {
						$user->update(['seocredit' => 0]);
					} else {
						$user->update(['mem_credit' => 0]);
					}
					$user->update(['mem_status' => 0]);
					$user->update(['trial_exp' => 1]);
					notify($user, 'MEM_EXPIRY');
				}
			} elseif (Carbon::now() >= $user->mem_exp->subDays(3) && $user->mem_alert == 0) {
				$user->update(['mem_alert' => 1]);
				notify($user, 'MEM_EXPIRY_ALERT');
			}
		}
		
		$deposits = Deposit::all();
        	foreach ($deposits as $order) {
            	if (Carbon::now()->subMinutes(10) > Carbon::parse($order->updated_at) && $order->status == 0) {
                	$order->update(['status' => 2]);
            		}
        	}

		$general = GeneralSetting::first();
		$general->last_cron = now();
		$general->save();
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
					} elseif($order->category_id == 20){
					notify($user, 'REALISTIC_COMPLETED', [
						'service_name' => $order->service->name,
						'order_id' => $order->id,
						'name' => $order->name,
						'link' => urldecode($order->link),
						'clicks' => $order->start_counter,
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

	function updateCurrencyRates()
	{
		$apiKey = 'cur_live_Hq977VcLSaqTzqgKgDdUqVBWZ5WAbc1sZH20qQBG';

			// Define overrides for currency codes
			$currencyOverrides = [
				'USDT.TRC20' => 'USDT',
				// Add more overrides here as needed
			];
	
		try {
			$response = Http::withHeaders([
				'apikey' => $apiKey
			])->timeout(30)->get('https://api.currencyapi.com/v3/latest');

			// //local
			// $response = Http::withHeaders([
			// 	'apikey' => $apiKey
			// ])->withoutVerifying()->get('https://api.currencyapi.com/v3/latest');


			if (!$response->successful()) {
				Log::error('Currency API request failed: ' . $response->body());
				return false;
			}
	
			$rates = $response->json('data');
	
			if (!$rates || !is_array($rates)) {
				Log::error('Invalid or empty currency data from API.');
				return false;
			}
	
			$currencies = GatewayCurrency::all();
	
			foreach ($currencies as $currencyRecord) {
				$code = strtoupper($currencyRecord->currency);
				$baseCode = $currencyOverrides[$code] ?? $code;
	
				if (!isset($rates[$baseCode])) {
					Log::info("Currency '{$baseCode}' not found in API response. Skipped.");
					continue;
				}
	
				$rate = $rates[$baseCode]['value'];
	
				// Add +1 only for INR
				if ($baseCode === 'INR') {
					$rate += 1;
				}
	
				$currencyRecord->rate = $rate;
				$currencyRecord->save();
	
				Log::info("Updated rate for {$code}: {$rate}");
			}
	
			Log::info("Currency rates update completed.");
			return true;
	
		} catch (\Exception $e) {
			Log::error("Currency update failed: " . $e->getMessage());
			return false;
		}
	}


}
