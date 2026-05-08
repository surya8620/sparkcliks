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

	/**
	 * Unified API operation handler for all order actions
	 * Handles: place, update, stop, resume operations automatically
	 * Call this method from cron without any parameters
	 */
	public function processOrderApiOperations()
	{
		// All operations with their configurations
		$operations = [
			'add' => [
				'status_filter' => [1],
				'api_status' => 0,
				'api_order_id_filter' => [0, 2],
				'success_field' => 'order',
				'success_value' => null,  // Check for any non-empty value
				'next_status' => 1,
				'log_message' => 'Order placed successfully'
			],
			'update' => [
				'status_filter' => [1],
				'api_status' => 2,
				'api_order_id_filter' => ['!=', 0],
				'success_field' => 'order',
				'success_value' => 'success',
				'next_status' => 1,
				'log_message' => 'Order updated successfully'
			],
			'stop' => [
				'status_filter' => [6, 0],
				'api_status' => 3,
				'api_order_id_filter' => ['!=', 0],
				'success_field' => 'order',
				'success_value' => 'success',
				'next_status' => 1,
				'log_message' => 'Order stopped successfully'
			],
			'resume' => [
				'status_filter' => [1],
				'api_status' => 4,
				'api_order_id_filter' => ['!=', 0],
				'success_field' => 'order',
				'success_value' => 'success',
				'next_status' => 1,
				'log_message' => 'Order resumed successfully'
			]
		];

		// Automatically process all operations
		foreach ($operations as $actionType => $config) {
			$this->processApiAction($actionType, $config);
		}

		// Update cron timestamp
		$general = GeneralSetting::first();
		$general->last_cron = now();
		$general->save();
	}

	/**
	 * Individual action methods (for backward compatibility)
	 */
	public function placeOrderToApi()
	{
		$this->processOrderApiOperations();
	}

	public function updateOrderToApi()
	{
		$this->processOrderApiOperations();
	}

	public function stopOrderToApi()
	{
		$this->processOrderApiOperations();
	}

	public function resumeOrderToApi()
	{
		$this->processOrderApiOperations();
	}

	/**
	 * Process a specific API action with given configuration
	 */
	private function processApiAction($action, $config)
	{
	    // Build query based on configuration
	    $query = Order::with('provider')
	        ->where('api_provider_id', '!=', 0)
	        ->where('order_placed_to_api', $config['api_status'])
	        ->whereIn('category_id', [17, 20, 21]);

	    // Add status filter
	    if (count($config['status_filter']) > 1) {
	        $query->where(function($q) use ($config) {
	            foreach ($config['status_filter'] as $status) {
	                $q->orWhere('status', $status);
	            }
	        });
	    } else {
	        $query->where('status', $config['status_filter'][0]);
	    }

	    // Add API order ID filter
	    $apiOrderIdFilter = $config['api_order_id_filter'];
	    
	    if (is_array($apiOrderIdFilter) && count($apiOrderIdFilter) === 2 && !is_numeric($apiOrderIdFilter[0])) {
	        // It's an operator-based filter like ['!=', 0] or ['>', 100]
	        $query->where('api_order_id', $apiOrderIdFilter[0], $apiOrderIdFilter[1]);
	    } elseif (is_array($apiOrderIdFilter)) {
	        // It's a list of specific IDs like [0, 2]
	        $query->whereIn('api_order_id', $apiOrderIdFilter);
	    } else {
	        // Single value
	        $query->where('api_order_id', $apiOrderIdFilter);
	    }

	    $apiOrders = $query->get();
	    
	    // Log category 21 orders found for this action
	    $category21Orders = $apiOrders->where('category_id', 21);
	    // if ($category21Orders->count() > 0) {
	    //     Log::info("Category 21 Orders Found for '{$action}' action", [
	    //         'action' => $action,
	    //         'total_found' => $category21Orders->count(),
	    //         'order_ids' => $category21Orders->pluck('id')->toArray(),
	    //         'api_status' => $config['api_status'],
	    //         'status_filter' => $config['status_filter']
	    //     ]);
	    // } else {
	    //     Log::info("No Category 21 Orders Found for '{$action}' action", [
	    //         'action' => $action,
	    //         'api_status' => $config['api_status'],
	    //         'status_filter' => $config['status_filter']
	    //     ]);
	    // }

	    // Process each order and track success
	    $successCount = 0;
	    foreach ($apiOrders as $order) {
	        if ($this->processOrderApiCall($order, $action, $config)) {
	            $successCount++;
	        }
	    }
	    
	    // Log summary for category 21 orders
	    // if ($category21Orders->count() > 0) {
	    //     Log::info("Category 21 Orders Processing Summary for '{$action}' action", [
	    //         'action' => $action,
	    //         'total_processed' => $category21Orders->count(),
	    //         'successful' => $successCount,
	    //         'failed' => ($category21Orders->count() - $successCount)
	    //     ]);
	    // }

	    // Only update individual cron timestamp if at least one order was successfully processed
	    if ($successCount > 0) {
	        $general = GeneralSetting::first();
	        switch ($action) {
	            case 'add':
	                $general->api_process_cron = now();
	                break;
	            case 'update':
	                $general->api_update_cron = now();
	                break;
	            case 'stop':
	                $general->api_stop_cron = now();
	                break;
	            case 'resume':
	                $general->api_resume_cron = now();
	                break;
	        }
	        $general->save();
	    }
	}

	/**
	 * Process individual order API call
	 */
	private function processOrderApiCall($order, $action, $config)
	{
		// Log category 21 order processing attempt
		// if ($order->category_id == 21) {
		// 	Log::info("Processing Category 21 Order - '{$action}' action", [
		// 		'order_id' => $order->id,
		// 		'action' => $action,
		// 		'api_order_id' => $order->api_order_id,
		// 		'status' => $order->status,
		// 		'order_placed_to_api' => $order->order_placed_to_api,
		// 		'api_provider_id' => $order->api_provider_id,
		// 		'quantity' => $order->quantity,
		// 		'speed' => $order->speed,
		// 		'start_counter' => $order->start_counter
		// 	]);
		// }
		
		$response = CurlRequest::curlPostContent(
			$order->provider->api_url, 
			$this->buildApiPayload($order, $action)
		);

		if ($response === null) {
			// if ($order->category_id == 21) {
			// 	Log::error("Category 21 Order API Call Failed - No Response", [
			// 		'order_id' => $order->id,
			// 		'action' => $action
			// 	]);
			// }
			return false;
		}

		$responseData = json_decode($response);

		if ($responseData === null || (property_exists($responseData, 'error') && $responseData->error)) {
			if ($order->category_id == 21) {
				Log::error("Category 21 Order API Call Failed - Error in Response", [
					'order_id' => $order->id,
					'action' => $action,
					'response' => $response,
					'error' => property_exists($responseData, 'error') ? $responseData->error : 'Response decode failed'
				]);
			}
			return false;
		}
		// Check for success based on configuration
		if ($this->isApiCallSuccessful($responseData, $config)) {
			$order->order_placed_to_api = $config['next_status'];
			
			// For 'add' action, also save the API order ID
			if ($action === 'add' && !empty($responseData->order)) {
				$order->api_order_id = $responseData->order;
			}
			
			$order->save();
			
			// Log success for category 21
			// if ($order->category_id == 21) {
			// 	Log::info("Category 21 Order API Call Successful", [
			// 		'order_id' => $order->id,
			// 		'action' => $action,
			// 		'new_api_order_id' => $order->api_order_id,
			// 		'new_order_placed_to_api' => $order->order_placed_to_api
			// 	]);
			// }
			
			return true;
		}

		// if ($order->category_id == 21) {
		// 	Log::warning("Category 21 Order API Call - Success Check Failed", [
		// 		'order_id' => $order->id,
		// 		'action' => $action,
		// 		'response' => $response
		// 	]);
		// }

		return false;
	}

	/**
	 * Check if API call was successful based on configuration
	 */
	private function isApiCallSuccessful($response, $config)
	{
	    $field = $config['success_field'];
	    
	    if (!property_exists($response, $field)) {
	        return false;
	    }

	    if ($config['success_value'] === null) {
	        // For 'add' action - check if field has any non-empty value
	        return !empty($response->$field);
	    } else {
	        // For other actions - check if field matches expected value
	        return $response->$field === $config['success_value'];
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

		foreach ($apiOrders as $order) {
			$response = CurlRequest::curlPostContent($order->provider->api_url, $this->buildApiPayload($order, 'pause'));
			$response = json_decode($response);

			if ($response && property_exists($response, 'Success') && $response->Success == 1) {
    			// Order Updated
    			$order->order_placed_to_api = 1;
    			$order->save();
			}
		}
		
	}

	public function expiryCheck()
	{
		// Fetch all users with membership types or bot status in one query
		$users = User::where(function($query) {
				$query->whereIn('mem_type', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15])
					  ->orWhere('bot_status', 1);
			})
			->get();

		$expiredUserIds = []; // Track users whose membership expired

		foreach ($users as $user) {
			// Process membership expiry checks
			if (in_array($user->mem_type, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15])) {
				if (Carbon::now() > $user->mem_exp && $user->trial_exp == 0) {
					$expiredUserIds[] = $user->id; // Add to expired users list
					
					if ($user->mem_type == 1) {
						$user->update(['mem_credit' => 0, 'trial_exp' => 1]);
						notify($user, 'TRIAL_EXPIRY');
					} else {
						$updateData = ['mem_status' => 0, 'trial_exp' => 1];
						
						if ($user->mem_type >= 10 && $user->mem_type <= 15 && $user->mem_status == 1) {
							$updateData['seocredit'] = 0;
						} else {
							$updateData['mem_credit'] = 0;
						}
						
						$user->update($updateData);
						notify($user, 'MEM_EXPIRY');
					}
				} elseif (Carbon::now() >= $user->mem_exp->subDays(3) && $user->mem_alert == 0) {
					$user->update(['mem_alert' => 1]);
					notify($user, 'MEM_EXPIRY_ALERT');
				}
			}

			// Process bot expiry checks
			if ($user->bot_status == 1) {
				if (Carbon::now() > $user->bot_exp) {
					$expiredUserIds[] = $user->id; // Add to expired users list
					
					// Set fields directly on the model instance and save
					$user->bot_credit = 0;
					$user->bot_status = 0;
					$user->bot_alert = 1;
					$user->bot_used = 0;
					$user->save();
					
					if ($user->bot_plan == 121) {
						notify($user, 'BOT_TRIAL_EXPIRY');
					} else {
						notify($user, 'BOT_MEM_EXPIRY', [
							'exp' => $user->bot_exp->format('Y-m-d'),
						]);
					}
				} elseif (Carbon::now() >= $user->bot_exp->subDays(3) && $user->bot_alert == 0 && $user->bot_plan != 121) {
					$user->bot_alert = 1;
					$user->save();
					notify($user, 'BOT_MEM_EXPIRY_ALERT', [
						'exp' => $user->bot_exp->format('Y-m-d'),
					]);
				}
			}
		}

		// Update order status to 5 (expired) for all orders belonging to expired users
		// Only update orders that are in progress (status = 1) or pending (status = 0)
		// Only update orders from specific categories (11, 12, 21)
		if (!empty($expiredUserIds)) {
			// Mark all orders as expired
			Order::whereIn('user_id', array_unique($expiredUserIds))
				->whereIn('category_id', [11, 12, 21]) // Only specific categories
				->whereIn('status', [0, 1, 6]) // Only update pending or in-progress orders
				->update(['status' => 5]); // Set to expired
		}
		
		$deposits = Deposit::all();
        	foreach ($deposits as $order) {
            	if (Carbon::now()->subMinutes(10) > Carbon::parse($order->updated_at) && $order->status == 0) {
                	$order->update(['status' => 2]);
            		}
        	}

		$general = GeneralSetting::first();
		$general->exp_cron = now();
		$general->save();
	}

	public function completed()
	{
    		$orders = Order::where('status', 1)->get();
    		foreach ($orders as $order) {
				// Fetch user directly from database using user_id to get fresh data
				$user = User::find($order->user_id);
				
				// Skip if user doesn't exist
				if (!$user) {
					continue;
				}
				
        		// Special handling for category 21 (Bot Traffic) with unlimited quantity (quantity = 0)
        		// Unlimited orders should NEVER auto-complete, they run indefinitely until manually stopped
        		// Expiry handling is done in expiryCheck() function, not here
        		if ($order->category_id == 21 && $order->quantity == 0) {
            		// Skip this order - unlimited bot campaigns never auto-complete
            		continue;
        		}
        		
        		// For all other orders (including category 21 with quantity > 0): check completion when quantity is reached
        		if ($order->quantity > 0 && $order->start_counter > $order->quantity && $order->status == 1) {
		            $order->status = 2;
		            $order->save();
					
					// For category 21 limited orders, release bot_used when completed (only if bot_status is active)
					if ($order->category_id == 21 && $user->bot_status == 1) {
						$user->bot_used = max(0, $user->bot_used - $order->speed);
						$user->save();
					}
					
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
					} elseif($order->category_id == 21){

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
		$general = gs();
		$general->completed_cron = now();
		$general->save();
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
	
			// Round to 2 decimal places
			$rate = round($rate, 2);
	
			$currencyRecord->rate = $rate;
			$currencyRecord->save();
	
			Log::info("Updated rate for {$code}: {$rate}");
			}
	
			Log::info("Currency rates update completed.");
			return true;
	
		} catch (\Exception $e) {
			Log::error("Currency update failed: " . $e->getMessage());
			return false;
		} finally {
			// Always update cron timestamp, regardless of success or failure
			$general = gs();
			$general->currency_cron = now();
			$general->save();
		}
	}

    /**
     * Build API payload with ALL order fields
     */
    private function buildApiPayload($order, $action = 'add')
    {
        // Start with base required fields
        $payload = [
            'key' => $order->provider->api_key,
            'action' => $action,
        ];

        // Action-specific ID field
        if ($action === 'add') {
            $payload['id'] = $order->id;
        } else {
            $payload['id'] = $order->api_order_id;
        }

        // Get all order attributes and include them
        $orderData = $order->toArray();
        
        // Exclude sensitive/unnecessary fields
        $excludedFields = [
            'created_at', 'updated_at', 'api_order_id', 'dripfeed', 'runs', 'interval', 'price', 'clicks', 'auto_renew', 'traffic_exp', 'name', 'traffic_plan', 'api_service_id', 'start_counter', 'attempt', 'remain', 'api_provider_id', 'status', 'api_order', 'order_placed_to_api'
        ];

        // Add all order fields to payload
        foreach ($orderData as $field => $value) {
            // Skip excluded fields and already added fields
            if (in_array($field, $excludedFields) || isset($payload[$field])) {
                continue;
            }

            // Only include non-null, non-empty values
            if ($value !== null && $value !== '') {
                try {
                    $transformedValue = $this->transformFieldValue($field, $value);
                    $payload[$field] = $transformedValue;
                } catch (\Exception $e) {
                    // Skip this field if transformation fails
                    continue;
                }
            }
        }

        return $payload;
    }

    /**
     * Transform field values for API compatibility
     */
    private function transformFieldValue($field, $value)
    {
        switch ($field) {
            case 'tp':
                return $this->transformTpValue($value);
                
            case 'link':
            case 'link2': 
            case 'link3':
            case 'ref':
            case 'landing_page':
                // Ensure URLs are properly decoded
                return urldecode($value);
                
            case 'quantity':
            case 'speed':
            case 'daily_limit':
            case 'service_id':
            case 'category_id':
            case 'user_id':
            case 'provider_id':
            case 'api_provider_id':
            case 'traffic_plan':
            case 'traffic_exp':
                // Ensure numeric values are integers
                return (int) $value;
                
            case 'bounce_rate':
            case 'conversion_rate':
            case 'price':
            case 'amount':
                // Ensure float values are properly formatted
                return (float) $value;
                
            case 'start_date':
            case 'end_date':
            case 'created_at':
            case 'updated_at':
                // Format dates consistently
                if ($value instanceof \DateTime) {
                    return $value->format('Y-m-d H:i:s');
                } elseif (is_string($value)) {
                    return \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s');
                }
                return $value;
                
            case 'analytics':
            case 'utm_parameters':
            case 'custom_headers':
            case 'geo_targeting':
            case 'demographic_targeting':
            case 'interest_targeting':
                // Handle JSON fields
                if (is_array($value)) {
                    return json_encode($value);
                } elseif (is_string($value) && $this->isJson($value)) {
                    return $value; // Already JSON string
                }
                return $value;

            case 'status':
            case 'order_placed_to_api':
                // Keep status fields as integers
                return (int) $value;
                
            default:
                // Handle arrays properly
                if (is_array($value)) {
                    return json_encode($value);
                }
                
                // Handle objects
                if (is_object($value)) {
                    return json_encode($value);
                }
                
                // Handle null values
                if ($value === null) {
                    return '';
                }
                
                // Return value as-is for most fields, but ensure it's a string for API
                if (is_bool($value)) {
                    return $value ? 1 : 0;
                }
                
                // Final safety check before casting to string
                if (is_scalar($value)) {
                    return (string) $value;
                }
                
                // If all else fails, convert to JSON
                return json_encode($value);
        }
    }

    /**
     * Check if a string is valid JSON
     */
    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Transform tp value with existing logic
     */
    private function transformTpValue($tp)
    {
        return ($tp == 1) ? 30 : 
               (($tp == 2) ? 1 : 
               (($tp == 3) ? 2 : 
               (($tp == 4) ? 3 : 
               (($tp == 5) ? 4 : 
               (($tp == 6) ? 5 : 
               (($tp == 0) ? 30 : $tp))))));
    }
}
