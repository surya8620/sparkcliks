<?php

namespace App\Http\Controllers\Admin;

use App\Lib\SMM;
use App\Models\Order;
use App\Models\Category;
use App\Constants\Status;
use App\Models\User;
use App\Models\ApiProvider;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\WebTrafficReports;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7;
use GuzzleHttp\RequestOptions;
use App\Lib\CurlRequest;

class OrderController extends Controller
{
    public function reports($id)
    {

        $hits = WebTrafficReports::where('order_id', $id)->selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->orderBy('created_at', 'desc')->get();

        $pageTitle = "Campaign Reports for {$id}";
        return view('admin.order.webtraffic_reports', compact('pageTitle', 'hits'));
    }

    public function update(Request $request, $id)
    {
        $order = Order::with('user', 'category')->findOrFail($id);

        $request->validate([
            'start_count' => 'required|integer|gte:0|lte:' . $order->quantity,
            'status' => 'required|integer|in:0,1,2,3,4,5,6',
        ]);
        $order->start_counter = $request->start_count;
        $order->remain = ($order->quantity - $request->start_count);
        $order->attempt = $request->attempt;
        $order->error = $request->error;
        $user = $order->user;
        
        //Processing
        if ($request->status == Status::ORDER_PROCESSING) {
            if (($order->category_id == 17 || $order->category_id == 20) && $order->api_order_id == 0){
				$order->order_placed_to_api = 0;
			}
            elseif ($order->status == Status::ORDER_PENDING || $order->status == Status::ORDER_PAUSED) {
                $order->order_placed_to_api = 4;
            } else {
				$order->order_placed_to_api = 2;
		    }
            $order->status = Status::ORDER_PROCESSING;
            $order->save();
        }
        //Error
        if ($request->status == Status::ORDER_PENDING) {
            $order->order_placed_to_api = 3;
            $order->status = Status::ORDER_PENDING;
            $order->save();

            notify($user, 'PENDING_ORDER', [
                'service_name' => $order->service->name,
                'error' => $order->error,
                'name' => $order->name,
                'order_id' => $order->id,                
            ]);
        }

        //Expired
        if ($request->status == Status::ORDER_EXPIRED) {
            $order->order_placed_to_api = 3;
            $order->status = Status::ORDER_EXPIRED;
            $order->save();
        }

        //Paused
        if ($request->status == Status::ORDER_PAUSED) {
            $order->order_placed_to_api = 3;
            $order->status = Status::ORDER_PAUSED;
            $order->save();
        }

        //Complete Order
        if ($request->status == Status::ORDER_COMPLETED) {
            $order->status = Status::ORDER_COMPLETED;
            $order->save();
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

        //Cancelled
        if ($request->status == Status::ORDER_CANCELLED) {
            $order->status = Status::ORDER_CANCELLED;
            $order->save();

            //Refund balance
            if ($user->mem_type >= 9) {
                $user->seocredit += (($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity)));
            } else {
                $user->mem_credit += (($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity)));
            }
            $user->save();

            //Create Transaction
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->credits = getAmount(($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity)));
            $transaction->post_balance = getAmount($user->seocredit);
            $transaction->trx_type = '+';
            $transaction->details = 'Refund for Cancelled Campaign - ' . $order->name . '(ID:' . $order->id . ')';
            $transaction->trx = getTrx();
            $transaction->remark = 'REFUNDED';
            $transaction->save();

            //Send email to user
            notify($user, 'CANCELLED_ORDER', [
                'service_name' => $order->service->name,
                'price' => getAmount(($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity))),
                'trx' => $transaction->trx,
                'order_id' => $order->id,
                'link' => urldecode($order->link),
                'keyword' => $order->keyword,
                'clicks' => $order->start_counter,
                'clicks/day' => $order->clicks,
                'attempt' => $order->attempt,
            ]);
        }

        //Denied
        if ($request->status == Status::ORDER_DENIED) {
            if ($order->status == Status::ORDER_COMPLETED || $order->status == Status::ORDER_CANCELLED) {
                $notify[] = ['error', 'This order is not refundable'];
                return back()->withNotify($notify);
            }

            $order->status = Status::ORDER_DENIED;
            $order->save();

            //Refund balance
            if ($user->mem_type >= 9) {
                $user->seocredit += (($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity)));
            } else {
                $user->mem_credit += (($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity)));
            }
            $user->save();

            //Create Transaction
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->credits = getAmount(($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity)));
            $transaction->post_balance = getAmount($user->seocredit);
            $transaction->trx_type = '+';
            $transaction->details = 'Refund for Denied Campaign - ' . $order->name . '(ID:' . $order->id . ')';
            $transaction->trx = getTrx();
            $transaction->remark = 'REFUNDED';
            $transaction->save();

            //Send email to user

            notify($user, 'DENIED_ORDER', [
                'service_name' => $order->service->name,
                'price' => getAmount(($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity))),
                'order_id' => $order->id,
                'link' => urldecode($order->link),
                'keyword' => $order->keyword,
                'clicks' => $order->start_counter,
                'clicks/day' => $order->clicks,
                'attempt' => $order->attempt,
                'trx' => $transaction->trx
            ]);
        }

        $order->save();
        $notify[] = ['success', 'Order successfully updated'];
        return back()->withNotify($notify);
    }

    public function renew($id)
    {
        $order = Order::findOrFail($id);

        $user = User::findOrFail($order->user_id);
        $price = $order->price;

        if ($order->category_id = 17) {

            if ($order->traffic_plan == 101 && $user->traffic_nano >= $price) {
                $user->traffic_nano -= $price;
            } elseif ($order->traffic_plan == 102 && $user->traffic_mini >= $price) {
                $user->traffic_mini -= $price;
            } elseif ($order->traffic_plan == 103 && $user->traffic_small >= $price) {
                $user->traffic_small -= $price;
            } elseif ($order->traffic_plan == 104 && $user->traffic_medium >= $price) {
                $user->traffic_medium -= $price;
            } elseif ($order->traffic_plan == 105 && $user->traffic_large >= $price) {
                $user->traffic_large -= $price;
            } elseif ($order->traffic_plan == 106 && $user->traffic_ultimate >= $price) {
                $user->traffic_ultimate -= $price;
            } else {
                $notify[] = ['error', 'Insufficient Credits!'];
                return back()->withNotify($notify);
            }
            $user->save();

            $order->start_counter = 0;
            $order->attempt = 0;
            $order->status = 1;
            $order->remain = $order->quantity;
            $order->traffic_exp = \Carbon\Carbon::now()->addDays(30);
            $order->save();

            //Create Transaction
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->credits = $price;
            $transaction->post_balance = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Website Traffic Campaign(Renewal) - ' . $order->name . '(ID:' . $order->id . ')';
            $transaction->trx = getTrx();
            $transaction->remark = 'RENEW';
            $transaction->save();

            //Send email to user
            notify($user, 'WEBTRAFFIC_RENEW', [
                'price' => $price,
                'trx' => $transaction->trx,
                'order_id' => $order->id,
                'link' => urldecode($order->link),
                'quantity' => $order->quantity,
                'expiry' => $order->traffic_exp,
                'speed' => $order->speed,
                'name' => $order->name,
                'geo' => $order->country,
            ]);
            $notify[] = ['success', 'Successfully Renewed!'];
            return back()->withNotify($notify);
        } elseif ($order->category_id = 18) {
            if ($user->premium_credit >= $price) {
                $user->premium_credit -= $price;
            } else {
                $notify[] = ['error', 'Insufficient Credits! Available Credits: ' . $user->premium_credit];
                return back()->withNotify($notify);
            }
            $user->save();

            $order->remain = $order->quantity;
            $order->start_counter = 0;
            $order->status = 1;

            $order->save();
            //Create Transaction
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->credits = $price;
            $transaction->post_balance = getAmount($user->balance);
            $transaction->trx_type = '-';
            $transaction->details = 'Premium Traffic Campaign(Renewal) - ' . $order->name . '(ID:' . $order->id . ')';
            $transaction->trx = getTrx();
            $transaction->remark = 'RENEW';
            $transaction->save();

            //Send email to user
            notify($user, 'PREMIUMTRAFFIC_RENEW', [
                'price' => getAmount($price),
                'trx' => $transaction->trx,
                'order_id' => $order->id,
                'link' => urldecode($order->link),
                'quantity' => $order->quantity,
                'name' => $order->name,
                'geo' => $order->country,
            ]);
            $notify[] = ['success', 'Successfully Renewed!'];
            return back()->withNotify($notify);
        } elseif ($order->category_id = 19) {
            if ($user->ad_credit >= $price) {
                $user->ad_credit -= $price;
            } else {
                $notify[] = ['error', 'Insufficient Credits!'];
                return back()->withNotify($notify);
            }
            $user->save();

            $order->traffic_exp = \Carbon\Carbon::now()->addDays(30);
            $order->remain = $order->quantity;
            $order->start_counter = 0;
            $order->status = 1;

            $order->save();

            //Create Transaction
            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->credits = $price;
            $transaction->post_balance = getAmount($user->balance);
            $transaction->trx_type = '-';
            $transaction->details = 'Ad Traffic Campaign(Renewal) - ' . $order->name . '(ID:' . $order->id . ')';
            $transaction->trx = getTrx();
            $transaction->remark = 'RENEW';
            $transaction->save();

            //Send email to user
            notify($user, 'ADTRAFFIC_RENEW', [
                'price' => $price,
                'trx' => $transaction->trx,
                'order_id' => $order->id,
                'link' => urldecode($order->link),
                'quantity' => $order->quantity,
                'name' => $order->name,
                'geo' => $order->country,
            ]);
            $notify[] = ['success', 'Successfully Renewed!'];
            return back()->withNotify($notify);
        }
    }

    //SERP Controller
    protected function serpData($scope = null, $customFilter = false)
    {
        if ($scope) {
            $orders = Order::$scope();
        } else {
            $orders = Order::query();
        }
        if ($customFilter) {
            $orders = $orders->searchable(['user:username', 'id'])
                ->dateFilter()
                ->filter(['category_id', 'api_provider_id', 'service:refill']);
        } else {
            $orders = $orders->searchable(['user:username', 'category:name', 'service:name']);
        }
        // 👇 Add this restriction here
        $orders = $orders->whereIn('category_id', [11, 12]);
        return $orders->directOrder()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
    }
    
    public function serpPending()
    {
        $pageTitle = "Pending Campaigns";
        $orders    = $this->serpData('pending');
        return view('admin.serp_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function serpActive()
    {
        $pageTitle = "Processing Campaigns";
        $orders    = $this->serpData('processing');
        return view('admin.serp_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function serpCompleted()
    {
        $pageTitle = "Completed Campaigns";
        $orders    = $this->serpData('completed');
        return view('admin.serp_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function serpCancelled()
    {
        $pageTitle = "Cancelled Campaigns";
        $orders    = $this->serpData('cancelled');
        return view('admin.serp_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function serpDenied()
    {
        $pageTitle = "Denied Campaigns";
        $orders    = $this->serpData('denied');
        return view('admin.serp_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function serpIndex()
    {
        $pageTitle = "SERP Campaigns";
        $categories = Category::active()->orderBy('name')->get();
        $apiLists   = ApiProvider::active()->get();
        $orders    = $this->serpData(null, true);
        return view('admin.serp_campaigns.index', compact('pageTitle', 'orders', 'categories', 'apiLists'));
    }

        public function serpDetails($id)
    {
        $pageTitle = 'Camapign Details';
        $order     = Order::directOrder()->whereIn('category_id', [11, 12])->findOrFail($id);
        return view('admin.serp_campaigns.details', compact('pageTitle', 'order'));
    }
    
    //WT Controller
    protected function wtData($scope = null, $customFilter = false)
    {
        if ($scope) {
            $orders = Order::$scope();
        } else {
            $orders = Order::query();
        }
        if ($customFilter) {
            $orders = $orders->searchable(['user:username', 'id'])
                ->dateFilter()
                ->filter(['category_id', 'api_provider_id', 'service:refill']);
        } else {
            $orders = $orders->searchable(['user:username', 'category:name', 'service:name']);
        }
        // 👇 Add this restriction here
        $orders = $orders->whereIn('category_id', [17]);
        return $orders->directOrder()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
    }
    
    public function wtPending()
    {
        $pageTitle = "Pending Campaigns";
        $orders    = $this->wtData('pending');
        return view('admin.wt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function wtActive()
    {
        $pageTitle = "Processing Campaigns";
        $orders    = $this->wtData('processing');
        return view('admin.wt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function wtCompleted()
    {
        $pageTitle = "Completed Campaigns";
        $orders    = $this->wtData('completed');
        return view('admin.wt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function wtCancelled()
    {
        $pageTitle = "Cancelled Campaigns";
        $orders    = $this->wtData('cancelled');
        return view('admin.wt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function wtPaused()
    {
        $pageTitle = "Paused Campaigns";
        $orders    = $this->wtData('paused');
        return view('admin.wt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function wtExpired()
    {
        $pageTitle = "Expired Campaigns";
        $orders    = $this->wtData('expired');
        return view('admin.wt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function wtIndex()
    {
        $pageTitle = "WT Campaigns";
        $categories = Category::active()->orderBy('name')->get();
        $apiLists   = ApiProvider::active()->get();
        $orders    = $this->wtData(null, true);
        return view('admin.wt_campaigns.index', compact('pageTitle', 'orders', 'categories', 'apiLists'));
    }

    public function wtDetails($id)
    {
        $pageTitle = 'Camapign Details';
        $order     = Order::directOrder()->whereIn('category_id', [17])->findOrFail($id);
        return view('admin.wt_campaigns.details', compact('pageTitle', 'order'));
    }
    
    //RT Controller
    protected function rtData($scope = null, $customFilter = false)
    {
        if ($scope) {
            $orders = Order::$scope();
        } else {
            $orders = Order::query();
        }
        if ($customFilter) {
            $orders = $orders->searchable(['user:username', 'id'])
                ->dateFilter()
                ->filter(['category_id', 'api_provider_id', 'service:refill']);
        } else {
            $orders = $orders->searchable(['user:username', 'category:name', 'service:name']);
        }
        // 👇 Add this restriction here
        $orders = $orders->whereIn('category_id', [20]);
        return $orders->directOrder()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
    }
    
    public function rtPending()
    {
        $pageTitle = "Pending Campaigns";
        $orders    = $this->rtData('pending');
        return view('admin.rt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function rtActive()
    {
        $pageTitle = "Processing Campaigns";
        $orders    = $this->rtData('processing');
        return view('admin.rt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function rtCompleted()
    {
        $pageTitle = "Completed Campaigns";
        $orders    = $this->rtData('completed');
        return view('admin.rt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function rtCancelled()
    {
        $pageTitle = "Cancelled Campaigns";
        $orders    = $this->rtData('cancelled');
        return view('admin.rt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function rtPaused()
    {
        $pageTitle = "Paused Campaigns";
        $orders    = $this->rtData('paused');
        return view('admin.rt_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function rtIndex()
    {
        $pageTitle = "SERP Campaigns";
        $categories = Category::active()->orderBy('name')->get();
        $apiLists   = ApiProvider::active()->get();
        $orders    = $this->rtData(null, true);
        return view('admin.rt_campaigns.index', compact('pageTitle', 'orders', 'categories', 'apiLists'));
    }

    public function rtDetails($id)
    {
        $pageTitle = 'Camapign Details';
        $order     = Order::directOrder()->whereIn('category_id', [20])->findOrFail($id);
        return view('admin.rt_campaigns.details', compact('pageTitle', 'order'));
    }

    //Block Domains
	public function block($id)
	{
		$order = Order::findOrFail($id);
		$url = $order->link;
		$host = parse_url($url, PHP_URL_HOST);

		$filePath = storage_path('domains.json');

		if (!file_exists($filePath)) {
			file_put_contents($filePath, json_encode([]));
		}

		$existingDomainsJson = file_get_contents($filePath);
		$existingDomains = json_decode($existingDomainsJson, true);

		if (!is_array($existingDomains)) {
			$existingDomains = [];
		}

		$newDomains = [];

		// Count dots to check for subdomains
		$dotCount = substr_count($host, '.');

		if ($dotCount >= 2) {
			// e.g., abc.xyz.com → add abc.xyz.com and xyz.com
			$newDomains[] = $host;
			preg_match('/([^.]+\.[^.]+)$/', $host, $matches);
			$baseDomain = $matches[1] ?? null;
			if ($baseDomain) {
				$newDomains[] = $baseDomain;
			}
		} elseif ($dotCount === 1) {
			// e.g., xyz.com → add just that
			$newDomains[] = $host;
		}

		// Remove already-existing domains
		$newDomains = array_filter($newDomains, function ($domain) use ($existingDomains) {
			return !in_array($domain, $existingDomains);
		});

		if (!empty($newDomains)) {
			$updatedDomains = array_merge($existingDomains, $newDomains);
			file_put_contents($filePath, json_encode($updatedDomains, JSON_PRETTY_PRINT));
		}

		$notify[] = ['success', 'Domain(s) Blacklisted'];
		return back()->withNotify($notify);
	}

    //Traffic Bot Controller
    protected function tbData($scope = null, $customFilter = false)
    {
        if ($scope) {
            $orders = Order::$scope();
        } else {
            $orders = Order::query();
        }
        if ($customFilter) {
            $orders = $orders->searchable(['user:username', 'id'])
                ->dateFilter()
                ->filter(['category_id', 'api_provider_id', 'service:refill']);
        } else {
            $orders = $orders->searchable(['user:username', 'category:name', 'service:name']);
        }
        // 👇 Add this restriction here
        $orders = $orders->whereIn('category_id', [21]);
        return $orders->directOrder()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
    }
    
    public function tbPending()
    {
        $pageTitle = "Pending Campaigns";
        $orders    = $this->tbData('pending');
        return view('admin.tb_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function tbActive()
    {
        $pageTitle = "Processing Campaigns";
        $orders    = $this->tbData('processing');
        return view('admin.tb_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function tbCompleted()
    {
        $pageTitle = "Completed Campaigns";
        $orders    = $this->tbData('completed');
        return view('admin.tb_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function tbCancelled()
    {
        $pageTitle = "Cancelled Campaigns";
        $orders    = $this->tbData('cancelled');
        return view('admin.tb_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function tbPaused()
    {
        $pageTitle = "Paused Campaigns";
        $orders    = $this->tbData('paused');
        return view('admin.tb_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function tbExpired()
    {
        $pageTitle = "Expired Campaigns";
        $orders    = $this->tbData('expired');
        return view('admin.tb_campaigns.index', compact('pageTitle', 'orders'));
    }

    public function tbIndex()
    {
        $pageTitle = "TB Campaigns";
        $categories = Category::active()->orderBy('name')->get();
        $apiLists   = ApiProvider::active()->get();
        $orders    = $this->tbData(null, true);
        return view('admin.tb_campaigns.index', compact('pageTitle', 'orders', 'categories', 'apiLists'));
    }

    public function tbDetails($id)
    {
        $pageTitle = 'Campaign Details';
        $order     = Order::directOrder()->whereIn('category_id', [21])->findOrFail($id);
        return view('admin.tb_campaigns.details', compact('pageTitle', 'order'));
    }

    /**
     * Display logs page for a specific bot campaign order (Admin version)
     * Admin can access any order without user ownership check
     * 
     * @param int|null $id Order ID
     * @return \Illuminate\View\View
     */
    public function tbLogs($id = null)
    {
        $pageTitle = 'Campaign Logs - Real-time Monitoring';
        $orderId = $id;
        
        // Validate that the order exists and is a bot campaign if ID is provided
        // Admin can access any order (no user_id check)
        if ($orderId) {
            $order = Order::where('id', $orderId)
                ->where('category_id', 21) // Bot category
                ->firstOrFail();
        }
        
        return view('admin.tb_campaigns.logs', compact('pageTitle', 'orderId'));
    }

    /**
     * Generate secure WebSocket token for admin logs access
     * Admin can access any order without user ownership check
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function tbLogsToken(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id'
        ]);
        
        $orderId = $request->order_id;
        
        // Verify order exists and is a bot campaign
        // Admin can access any order (no user_id check)
        $order = Order::where('id', $orderId)
            ->where('category_id', 21)
            ->firstOrFail();
        
        try {
            // Fetch dynamic token from external API (same as user version)
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post('https://lb.probotronix.com/api/generate-token', [
                    'key' => 'd1aedfb0a56fbbdab84df75ed39cfa3545f8ec24',
                    'id' => (string) $order->api_order_id
                ]);
            
            if (!$response->successful()) {
                throw new \Exception('Failed to generate token: HTTP ' . $response->status());
            }
            
            $tokenData = $response->json();
            
            // Validate response structure
            if (!isset($tokenData['auth']) || !isset($tokenData['server_ip'])) {
                throw new \Exception('Invalid response format from token server');
            }
            
            $token = $tokenData['auth'];
            $serverIp = $tokenData['server_ip'];
            
            // Convert server_ip to WebSocket URL format
            $wsUrl = 'wss://' . $serverIp;
            
            return response()->json([
                'success' => true,
                'token' => $token,
                'ws_url' => $wsUrl,
                'order_id' => $orderId,
                'expires_in' => 3600 // Token valid for 60 minutes (3600 seconds)
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Admin Logs Token exception', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate connection token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download complete log file for an order (Admin version)
     * Admin can access any order without user ownership check
     * 
     * @param Request $request
     * @param int $id Order ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function tbLogsDownload(Request $request, $id)
    {
        // Verify order exists and is a bot campaign
        // Admin can access any order (no user_id check)
        $order = Order::where('id', $id)
            ->where('category_id', 21)
            ->firstOrFail();
        
        $request->validate([
            'token' => 'required|string',
            'serverUrl' => 'required|string'
        ]);
        
        try {
            $token = $request->input('token');
            $serverUrl = $request->input('serverUrl');
            
            // Convert WebSocket URL to HTTP URL
            $httpUrl = str_replace(['ws://', 'wss://'], ['http://', 'https://'], $serverUrl);
            
            // Fetch logs using WebSocket token
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-WS-Token' => $token,
                    'Accept' => 'application/json',
                ])
                ->get("{$httpUrl}/api/logs/{$id}/download");
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Return JSON response (not file download)
                return response()->json([
                    'success' => true,
                    'lines' => $data['lines'] ?? [],
                    'totalLines' => $data['totalLines'] ?? count($data['lines'] ?? []),
                    'fileSize' => $data['fileSize'] ?? strlen(implode("\n", $data['lines'] ?? []))
                ]);
            }
            
            // Handle error response
            $errorData = $response->json();
            return response()->json([
                'success' => false,
                'error' => $errorData['error'] ?? 'Failed to fetch logs'
            ], $response->status());
            
        } catch (\Exception $e) {
            \Log::error('Admin Logs download exception', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // General details method for all order types
    public function details($id)
    {
        $pageTitle = 'Order Details';
        $order     = Order::with(['category', 'user', 'service'])->findOrFail($id);
        
        // Determine which view to use based on category_id
        if (in_array($order->category_id, [11, 12])) {
            return view('admin.serp_campaigns.details', compact('pageTitle', 'order'));
        } elseif ($order->category_id == 17) {
            return view('admin.wt_campaigns.details', compact('pageTitle', 'order'));
        } elseif ($order->category_id == 20) {
            return view('admin.rt_campaigns.details', compact('pageTitle', 'order'));
        } elseif ($order->category_id == 21) {
            return view('admin.tb_campaigns.details', compact('pageTitle', 'order'));
        } else {
            // Default view for other order types
            return view('admin.order.details', compact('pageTitle', 'order'));
        }
    }
}
