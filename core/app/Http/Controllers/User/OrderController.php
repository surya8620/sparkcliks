<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use App\Models\Service;
use App\Lib\CurlRequest;
use App\Models\Category;
use App\Constants\Status;
use App\Models\ApiProvider;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Clicks;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Carbon\Carbon;
use App\Models\AdminNotification;
use App\Models\WebTrafficReports;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
	// ========================================
	// GENERAL ORDER MANAGEMENT
	// ========================================

	/**
	 * Display order overview page with categories and services
	 * 
	 * @param int $sId Service ID (optional)
	 * @return \Illuminate\View\View
	 */
	public function orderOverview($sId = 0)
	{
		$pageTitle = "Make New Order";
		$categories = Category::active()
			->whereHas('services', function ($query) {
				return $query->active()->withoutDripfeed();
			})->with([
					'services' => function ($query) {
						$query->active()->withoutDripfeed()->with('userServices', function ($userServices) {
							$userServices->where('user_id', auth()->id());
						});
					}
				])
			->withCount('services')->orderBy('name')->get()->map(function ($category) {
				$minService = $category->services()
					->orderBy('price_per_k', 'asc')
					->orderBy('min', 'asc')
					->first();
				$category->service_min_start = $minService->min;
				$category->price_per_k = $minService->price_per_k;
				return $category;
			});

		$service = Service::active()->with('category')->find($sId);

		if (!auth()->user()) {
			$notify[] = ["error", 'Login is required for order overview!'];
			return to_route('services')->withNotify($notify);
		}
		return view('Template::user.orders.overview', compact('pageTitle', 'categories', 'service'));
	}

	public function order(Request $request, $serviceId = 0)
	{
		$user = auth()->user();
		$service = Service::with([
			'category',
			'userServices' => function ($userServices) {
				$userServices->where('user_id', auth()->id());
			}
		])->active()->findOrFail($serviceId);
		$request->validate([
			'link' => 'required|url',
			'quantity' => 'required|integer|gte:' . $service->min . '|lte:' . $service->max,
		]);

		$pricePerK = $service->price_per_k;
		if (@$service->userServices[0]) {
			$pricePerK = $service->userServices[0]->price;
		}
		$price = ($pricePerK / 1000) * $request->quantity;

		if ($user->balance < $price) {
			$notify[] = ["error", 'Insufficient balance. Please deposit and try again'];
			return to_route('user.deposit.index')->withNotify($notify);
		}

		$user->balance -= $price;
		$user->save();

		// Create Transaction
		$transaction = new Transaction();
		$transaction->user_id = $user->id;
		$transaction->amount = $price;
		$transaction->post_balance = $user->balance;
		$transaction->trx_type = '-';
		$transaction->details = 'Order for ' . $service->name;
		$transaction->trx = getTrx();
		$transaction->remark = 'order';
		$transaction->save();

		// Make order
		$order = new Order();
		$order->user_id = $user->id;
		$order->category_id = $service->category->id;
		$order->service_id = $serviceId;
		$order->api_service_id = $service->api_service_id ?? Status::NO;
		$order->api_provider_id = $service->api_provider_id ?? Status::NO;
		$order->link = $request->link;
		$order->quantity = $request->quantity;
		$order->price = $price;
		$order->remain = $request->quantity;
		$order->api_order = $service->api_service_id ? Status::YES : Status::NO;
		$order->save();

		// Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'New order request for ' . $service->name;
		$adminNotification->click_url = urlPath('admin.orders.details', $order->id);
		$adminNotification->save();

		notify($user, 'PENDING_ORDER', [
			'service_name' => $service->name,
			'category' => $service->category->name,
			'username' => $user->username,
			'full_name' => $user->fullname,
			'price' => $price,
			'post_balance' => getAmount($user->balance),
		]);

		if ($service->api_provider_id) {
			$apiProvider = ApiProvider::active()->findOrFail($service->api_provider_id);
			$arr = [
				'key' => $apiProvider->api_key,
				'action' => 'add',
				'service' => $service->api_service_id,
				'link' => $order->link,
				'quantity' => $order->quantity
			];

			$response = CurlRequest::curlPostContent($apiProvider->api_url, $arr);
			$response = json_decode($response);
			if (!@$response->error) {
				$order->status = Status::ORDER_PROCESSING;
				$order->order_placed_to_api = Status::YES;
				$order->api_order_id = $response->order;
				$order->save();
			}
		}

		$notify[] = ['success', 'Successfully placed your order!'];
		return to_route('user.order.details', $order->id)->withNotify($notify);
	}

	protected function categoryData()
	{
		$order = Order::where('user_id', auth()->id())->get();
		$categoryIds = $order->pluck('category_id')->unique();
		return Category::active()
			->whereIn('id', $categoryIds)
			->whereHas('services', function ($query) {
				$query->active();
			})
			->orderBy('name')
			->get();
	}

	protected function orderData($scope = null)
	{
		if ($scope) {
			$orders = Order::$scope();
		} else {
			$orders = Order::query();
		}
		return $orders->directOrder()->where('user_id', auth()->id())->searchable(['id', 'service:name'])->filter(['category_id'])->dateFilter()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
	}

	// ========================================
	// SEO / SEARCH CONSOLE CAMPAIGNS
	// ========================================

	/**
	 * Create a new SEO campaign order
	 * 
	 * @param Request $request
	 * @param int $serviceId
	 * @return RedirectResponse
	 */
	public function seoOrder(Request $request, $serviceId)
	{
		$user = auth()->user();
		$service = Service::with('category')->active()->findOrFail($serviceId);
		$request->validate([
			'link' => 'required|url',
			'quantity' => 'required|integer|gte:' . $service->min . '|lte:' . $service->max,
		]);
		if ($request->country == "Worldwide" && $request->quality == "Mixed") {
			$price = getAmount(($service->price_per_k) * $request->quantity * 1);
		} elseif ($request->country == "Worldwide" && $request->quality == "High") {
			$price = getAmount(($service->price_per_k) * $request->quantity * 1.2);
		} elseif ($request->quality == "Mixed") {
			$price = getAmount(($service->price_per_k) * $request->quantity * 2);
		} elseif ($request->quality == "High") {
			$price = getAmount(($service->price_per_k) * $request->quantity * 2.4);
		}

		if ($user->mem_type >= 9 && $user->seocredit < $price) {
			$notify[] = ['error', 'Insufficient Credits!'];
			return back()->withNotify($notify);
		} elseif ($user->mem_type < 9 && $user->mem_credit < $price) {
			$notify[] = ['error', 'Insufficient Credits!'];
			return back()->withNotify($notify);
		}
		if ($user->mem_type >= 9) {
			$user->seocredit -= $price;
		} else {
			$user->mem_credit -= $price;
		}
		$user->save();

		//Make order
		$order = new Order();
		$order->user_id = $user->id;
		$order->category_id = $service->category->id;
		$order->service_id = $serviceId;
		$order->api_service_id = $service->api_service_id ? $service->api_service_id : 0;
		$order->name = $request->title;
		$order->link = $request->link;
		$order->link2 = $request->link2;
		$order->keyword = $request->keyword;
		$order->clicks = $request->clicks;
		$order->country = $request->country;
		$order->quality = $request->quality;
		$order->quantity = $request->quantity;
		$order->price = $price;
		$order->remain = $request->quantity;
		$order->api_order = $service->api_service_id ? 1 : 0;
		$order->save();

		//Create Transaction
		$transaction = new Transaction();
		$transaction->user_id = $user->id;
		$transaction->credits = $price;
		$transaction->trx_type = '-';
		$transaction->details = 'New SEO Campaign - ' . $order->name . '(ID:' . $order->id . ')';
		$transaction->trx = getTrx();
		$transaction->remark = 'NEW';
		$transaction->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'New Camapaign for ' . $service->name . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.serp.details', $order->id);
		$adminNotification->save();

		//Send email to user
		notify($user, 'PROCESSING_ORDER', [
			'service_name' => $service->name,
			'price' => $price,     
			'link' => $order->link,
			'order_id' => $order->id,
			'keyword' => $order->keyword,
			'country' => $order->country,
			'quantity' => $order->quantity,
			'clicks/day' => $order->clicks,
		]);
		$notify[] = ['success', 'Successfully created the campaign!'];
		return to_route('user.seo.details', ['id' => $order->id])->withNotify($notify);
	}

	public function seoDetails($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$clicks = Clicks::where('user_id', auth()->id())
						->where('order_id', $order->id)
						->latest('updated_at')
						->paginate(getPaginate());
	
		$pageTitle = 'Campaign Details';
		// Return the full page view for normal requests
		return view('Template::user.seo_orders.details', compact('pageTitle', 'order', 'clicks'));
	}
	

	public function seoUpdate(Request $request, $id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$request->validate([
			'link' => 'required|url',
		]);
		if (in_array($order->status, [0, 1])) {
		$order->name = $request->title;
		$order->link = $request->link;
		$order->link2 = $request->link2;
		// Process keywords from textarea
		$keywords = preg_split('/[\r\n,]+/', $request->keyword); // Split by new lines or commas
		$keywords = array_map('trim', $keywords); // Trim each keyword
		$keywords = array_filter($keywords); // Remove empty entries
		$order->keyword = implode(',', $keywords); // Convert back to comma-separated
		$order->clicks = $request->clicks;
		$order->status = 1;

		if ($order->country != 'Worldwide') {
			$order->country = $request->country;
		}

		$order->save();
		}
		$user = auth()->user();
		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Campaign Update for ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.serp.details', $order->id);
		$adminNotification->save();

		//Send email to user
		notify($user, 'SEO_ORDER_UPDATE', [
			'service_name' => $order->service->name,
			'order_id' => $order->id,
			'name' => $order->name,
			'link' => $order->link,
			'link2' => $order->link2,
			'keyword' => $order->keyword,
			'clicks/day' => $order->clicks,
			'country' => $order->country,
		]);
		$notify[] = ['success', 'Successfully updated!'];
		return back()->withNotify($notify);
	}

	public function seoCancel($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$order->status = 4;
		$order->save();

		$user = auth()->user();
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
		$transaction->trx_type = '+';
		$transaction->details = 'Refund for Cancelled Campaign - ' . $order->name . '(ID:' . $order->id . ')';
		$transaction->trx = getTrx();
		$transaction->remark = 'REFUNDED';
		$transaction->save();

		//Send email to user
		notify($user, 'CANCELLED_ORDER_SELF', [
			'service_name' => $order->service->name,
			'price' => getAmount(($order->price) * ((($order->quantity) - ($order->start_counter)) / ($order->quantity))),
			'trx' => $transaction->trx,
			'order_id' => $order->id,
			'link' => $order->link,
			'keyword' => $order->keyword,
			'clicks' => $order->start_counter,
			'clicks/day' => $order->clicks,
			'attempt' => $order->attempt,
		]);

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Campaign Cancelled for ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.serp.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign Cancelled!'];
		return back()->withNotify($notify);
	}

	protected function seoData($scope = null)
	{

		if ($scope) {
			$orders = Order::$scope();
		} else {
			$orders = Order::query();
		}
		return $orders->directOrder()->where('user_id', auth()->id())->whereHas('category', function ($query) {
			$query->where('id', [11, 12]);
		})->searchable(['id', 'service:name'])->filter(['category_id'])->dateFilter()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
	}

	public function seoHistory()
	{
		$pageTitle = 'Search Console - Campaign History';
		$orders = $this->seoData();
		$empty_message = "No result found";
		return view('Template::user.seo_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function seoPending()
	{
		$pageTitle = "On-hold Campaigns";
		$orders = $this->seoData('pending');
		$empty_message = "No result found";
		return view('Template::user.seo_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function seoProcessing()
	{
		$pageTitle = "Processing Campaigns";
		$orders = $this->seoData('processing');
		$empty_message = "No result found";
		return view('Template::user.seo_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function seoCompleted()
	{
		$pageTitle = "Completed Campaigns";
		$orders = $this->seoData('completed');
		$empty_message = "No result found";
		return view('Template::user.seo_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function seoDenied()
	{
		$pageTitle = "Denied Campaigns";
		$orders = $this->seoData('denied');
		$empty_message = "No result found";
		return view('Template::user.seo_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function seoCancelled()
	{
		$pageTitle = "Cancelled Campaigns";
		$orders = $this->seoData('cancelled');
		$empty_message = "No result found";
		return view('Template::user.seo_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function seoClicks()
	{
		$pageTitle = 'Click History';
		$empty_message = 'No history found.';
		$clicks = Clicks::where('user_id', \auth()->id())->latest()->paginate(getPaginate());
		return view('Template::user.seo_orders.clicks_history', compact('pageTitle', 'empty_message', 'clicks'));
	}

	public function seoReports($id)
	{
		$user = auth()->user();
		$order = Order::where('id', $id)->where('user_id', $user->id)->whereHas('category', function ($query) {$query->where('id', [11, 12]);})->first();
		if ($order) {
			$hits = WebTrafficReports::where('user_id', auth()->id())->where('order_id', $id)->selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->get();
			$empty_message = 'No history found.';
			$pageTitle = 'Campaign Reports - ' . $order->id;
			return view('Template::user.seo_orders.reports', compact('pageTitle', 'hits', 'empty_message'));
		} else {
			$notify[] = ['error', 'Not Found!'];
			return redirect()->back()->withNotify($notify);
		}
	}

	// ========================================
	// WEBSITE TRAFFIC CAMPAIGNS
	// ========================================

	/**
	 * Create a new website traffic order
	 * 
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function webOrder(Request $request)
	{
		$request->validate([
			'link' => 'required|url',
			'plan' => 'required|integer',
		]);
		$user = auth()->user();
		// Check if user has any credit for the selected traffic plan
		if (
			($request->plan == 101 && $user->traffic_nano == 0) ||
			($request->plan == 102 && $user->traffic_mini == 0) ||
			($request->plan == 103 && $user->traffic_small == 0) ||
			($request->plan == 104 && $user->traffic_medium == 0) ||
			($request->plan == 105 && $user->traffic_large == 0) ||
			($request->plan == 106 && $user->traffic_ultimate == 0)
		) {
			$notify[] = ['error', 'Insufficient Credits!'];
			return back()->withNotify($notify);
		}

		$service = Service::with('category')->active()->findOrFail($request->plan);

		if ($request->plan == 101) {
			$domainsJson = file_get_contents(storage_path('domains.json'));
			$checkDomains = json_decode($domainsJson, true);
			$pattern = '/\b(?:' . implode('|', $checkDomains) . ')\b/i';
			$request->validate([
				'link' => ['required', 'url', "not_regex:{$pattern}"],
			], [
				'link.required' => 'URL is required.',
				'link.url' => 'The URL format is invalid.',
				'link.not_regex' => 'Website is not eligible in the Nano Pack. Kindly create your campaign using paid credits instead.',
			]);
			// Extract domain from the request link
			$domain = parse_url($request->link, PHP_URL_HOST);
			
			// Check if ANY user has used this domain in Nano Pack (service_id 101) within the last 30 days
			// This prevents multi-account abuse
			$url = Order::where('service_id', 101) // Only check Nano Pack orders (service_id, not traffic_plan)
				->where('category_id', 17) // Only check website traffic orders
				->where(function ($query) use ($domain) {
					$query->where('link', 'like', "%://{$domain}%") // Match exact domain in URL
						->orWhere('link2', 'like', "%://{$domain}%")
						->orWhere('link3', 'like', "%://{$domain}%");
				})
				->where('created_at', '>=', Carbon::now()->subDays(30)) // Created within last 30 days
				->latest('created_at')
				->first();
			if ($url) {
				// Calculate days until the 30-day restriction period ends
				$expiryDate = Carbon::parse($url->created_at)->addDays(30);
				$daysRemaining = Carbon::now()->diffInDays($expiryDate, false);
				if ($daysRemaining < 0) $daysRemaining = 0; // Just in case
				
				$notify[] = ['error', "This domain was recently used in the Nano Pack. Please try again after {$daysRemaining} days."];
				return back()->withNotify($notify);
			}
		} else {
			$request->validate([
				'link' => ['required', 'url'],
			], [
				'link.url' => 'The URL format is invalid.',
			]);
		}

		$price = 1;

		//Subtract user balance
		$user = auth()->user();
		if ($request->plan == 101 && $user->traffic_nano >= $price) {
			$user->traffic_nano -= $price;
		} elseif ($request->plan == 102 && $user->traffic_mini >= $price) {
			$user->traffic_mini -= $price;
		} elseif ($request->plan == 103 && $user->traffic_small >= $price) {
			$user->traffic_small -= $price;
		} elseif ($request->plan == 104 && $user->traffic_medium >= $price) {
			$user->traffic_medium -= $price;
		} elseif ($request->plan == 105 && $user->traffic_large >= $price) {
			$user->traffic_large -= $price;
		} elseif ($request->plan == 106 && $user->traffic_ultimate >= $price) {
			$user->traffic_ultimate -= $price;
		} else {
			$notify[] = ['error', 'Something Went Wrong'];
			return back()->withNotify($notify);
		}
		$user->traffic_plan = $request->plan;
		$user->save();

		//Make order
		$order = new Order();
		$order->user_id = $user->id;
		$order->service_id = $request->plan;
		$order->category_id = $service->category->id;
		$order->link = $request->link;
		$order->country = "Worldwide";
		$order->name = $request->title;
		$order->traffic_plan = $request->plan;
		$order->br = 0;
		$order->td = "Random";
		$order->tt = 1;
		$order->tp = 1;
		$order->api_order = 1;
		$order->api_provider_id = 1;
		$order->api_order_id = 0;
		$order->order_placed_to_api = 0;
		$order->traffic_exp = \Carbon\Carbon::now()->addDays(30);
		if ($request->plan == 101) {
			$order->quantity = 2000;
			$order->remain = 2500;
			$order->speed = 2000;
			$order->api_order = 1;
			$order->api_provider_id = 1;
		} elseif ($request->plan == 102) {
			$order->quantity = 20000;
			$order->remain = 22000;
			$order->speed = 667;
		} elseif ($request->plan == 103) {
			$order->quantity = 100000;
			$order->remain = 110000;
			$order->speed = 3334;
		} elseif ($request->plan == 104) {
			$order->quantity = 200000;
			$order->remain = 210000;
			$order->speed = 6667;
		} elseif ($request->plan == 105) {
			$order->quantity = 333333;
			$order->remain = 350000;
			$order->speed = 11112;
		} elseif ($request->plan == 106) {
			$order->quantity = 666666;
			$order->remain = 690000;
			$order->speed = 22222;
		}
		$order->price = $price;
		$order->save();

		//Create Transaction
		$transaction = new Transaction();
		$transaction->user_id = $user->id;
		$transaction->credits = $price;
		$transaction->trx_type = '-';
		$transaction->details = 'New Website Traffic Campaign - ' . $order->name . '(ID:' . $order->id . ')';
		$transaction->trx = getTrx();
		$transaction->remark = 'NEW';
		$transaction->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Web Traffic Campaign for ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.wt.details', $order->id);
		$adminNotification->save();

		// notify($user, 'PROCESSING_ORDER_WEBTRAFFIC', [
		// 	'service_name' => $service->name,
		// 	'order_id' => $order->id,
		// 	'name' => $order->name,
		// 	'link' => $order->link,
		// 	'speed' => $order->speed,
		// 	'qty' => $order->quantity,
		// 	'country' => $order->country,
		// 	'exp' => $order->traffic_exp,
		// ]);
		$notify[] = ['success', 'Successfully created the campaign!'];
		return to_route('user.web.details', ['id' => $order->id])->withNotify($notify);
	}

	public function webUpdate(Request $request, $id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		if ($order->traffic_plan == 101 && $order->status == 0) {
			$domainsJson = file_get_contents(storage_path('domains.json'));
			$checkDomains = json_decode($domainsJson, true);
			$pattern = '/\b(?:' . implode('|', $checkDomains) . ')\b/i';
			$request->validate([
				'link' => ['required', 'url', "not_regex:{$pattern}"],
				'link2' => ['nullable', 'url', "not_regex:{$pattern}"],
				'link3' => ['nullable', 'url', "not_regex:{$pattern}"],
			], [
				'link.url' => '"URL" format is invalid.',
				'link.not_regex' => 'The "URL" is not permitted in the Nano Pack',
				'link2.url' => 'The "Next URL" format is invalid.',
				'link2.not_regex' => 'The "Next URL" is not permitted in the Nano Pack',
				'link3.url' => 'The "Exit URL" format is invalid.',
				'link3.not_regex' => 'The "Exit URL" is not permitted in the Nano Pack',
			]);
			// Check main URL (link) - only if order doesn't have a link yet
			if ($order->link === null && !empty($request->link)) {
				$domain = parse_url($request->link, PHP_URL_HOST);
				// Check if ANY user has used this domain in Nano Pack within 30 days (anti-abuse)
				$url = Order::where('service_id', 101) // Check Nano Pack orders from all users
					->where('category_id', 17) // Only website traffic
					->where('id', '!=', $order->id) // Exclude current order
					->where(function ($query) use ($domain) {
						$query->where('link', 'like', "%://{$domain}%")
							->orWhere('link2', 'like', "%://{$domain}%")
							->orWhere('link3', 'like', "%://{$domain}%");
					})
					->where('created_at', '>=', Carbon::now()->subDays(30))
					->latest('created_at')
					->first();
					
				if ($url) {
					$expiryDate = Carbon::parse($url->created_at)->addDays(30);
					$daysRemaining = Carbon::now()->diffInDays($expiryDate, false);
					if ($daysRemaining < 0) $daysRemaining = 0;
					$notify[] = ['error', "This domain was recently used in the Nano Pack. Please try again after {$daysRemaining} days."];
					return back()->withNotify($notify);
				}
			}
			
			// Check Next URL (link2) - only if order doesn't have link2 yet
			if ($order->link2 === null && !empty($request->link2)) {
				$domain2 = parse_url($request->link2, PHP_URL_HOST);
				// Check if ANY user has used this domain in Nano Pack within 30 days (anti-abuse)
				$url2 = Order::where('service_id', 101) // Check Nano Pack orders from all users
					->where('category_id', 17) // Only website traffic
					->where('id', '!=', $order->id)
					->where(function ($query) use ($domain2) {
						$query->where('link', 'like', "%://{$domain2}%")
							->orWhere('link2', 'like', "%://{$domain2}%")
							->orWhere('link3', 'like', "%://{$domain2}%");
					})
					->where('created_at', '>=', Carbon::now()->subDays(30))
					->latest('created_at')
					->first();
					
				if ($url2) {
					$expiryDate = Carbon::parse($url2->created_at)->addDays(30);
					$daysRemaining = Carbon::now()->diffInDays($expiryDate, false);
					if ($daysRemaining < 0) $daysRemaining = 0;
					$notify[] = ['error', "This Next URL domain was recently used in the Nano Pack. Please try again after {$daysRemaining} days."];
					return back()->withNotify($notify);
				}
			}
			
			// Check Exit URL (link3) - only if order doesn't have link3 yet
			if ($order->link3 === null && !empty($request->link3)) {
				$domain3 = parse_url($request->link3, PHP_URL_HOST);
				// Check if ANY user has used this domain in Nano Pack within 30 days (anti-abuse)
				$url3 = Order::where('service_id', 101) // Check Nano Pack orders from all users
					->where('category_id', 17) // Only website traffic
					->where('id', '!=', $order->id)
					->where(function ($query) use ($domain3) {
						$query->where('link', 'like', "%://{$domain3}%")
							->orWhere('link2', 'like', "%://{$domain3}%")
							->orWhere('link3', 'like', "%://{$domain3}%");
					})
					->where('created_at', '>=', Carbon::now()->subDays(30))
					->latest('created_at')
					->first();
					
				if ($url3) {
					$expiryDate = Carbon::parse($url3->created_at)->addDays(30);
					$daysRemaining = Carbon::now()->diffInDays($expiryDate, false);
					if ($daysRemaining < 0) $daysRemaining = 0;
					$notify[] = ['error', "This Exit URL domain was recently used in the Nano Pack. Please try again after {$daysRemaining} days."];
					return back()->withNotify($notify);
				}
			}
		} elseif ($order->traffic_plan == 101 && $order->status !== 0) {
			$domainsJson = file_get_contents(storage_path('domains.json'));
			$checkDomains = json_decode($domainsJson, true);
			$pattern = '/\b(?:' . implode('|', $checkDomains) . ')\b/i';
			$request->validate([
				'link2' => ['nullable', 'url', "not_regex:{$pattern}"],
				'link3' => ['nullable', 'url', "not_regex:{$pattern}"],
			], [
				'link2.url' => 'The "Next URL" format is invalid.',
				'link2.not_regex' => 'The "Next URL" is not permitted in the Nano Pack',
				'link3.url' => 'The "Exit URL" format is invalid.',
				'link3.not_regex' => 'The "Exit URL" is not permitted in the Nano Pack',
			]);
			// Check Next URL - only if order doesn't have link2 yet
			if ($order->link2 === null && !empty($request->link2)) {
				$domain2 = parse_url($request->link2, PHP_URL_HOST);
				// Check if ANY user has used this domain in Nano Pack within 30 days (anti-abuse)
				$url2 = Order::where('service_id', 101)
					->where('category_id', 17)
					->where('id', '!=', $order->id)
					->where(function ($query) use ($domain2) {
						$query->where('link', 'like', "%://{$domain2}%")
							->orWhere('link2', 'like', "%://{$domain2}%")
							->orWhere('link3', 'like', "%://{$domain2}%");
					})
					->where('created_at', '>=', Carbon::now()->subDays(30))
					->latest('created_at')
					->first();

				if ($url2) {
					$expiryDate = Carbon::parse($url2->created_at)->addDays(30);
					$daysRemaining = Carbon::now()->diffInDays($expiryDate, false);
					if ($daysRemaining < 0) $daysRemaining = 0;
					$notify[] = ['error', "This Next URL domain was recently used in the Nano Pack. Please try again after {$daysRemaining} days."];
					return back()->withNotify($notify);
				}
			}
			// Check Exit URL - only if order doesn't have link3 yet
			if ($order->link3 === null && !empty($request->link3)) {
				$domain3 = parse_url($request->link3, PHP_URL_HOST);
				// Check if ANY user has used this domain in Nano Pack within 30 days (anti-abuse)
				$url3 = Order::where('service_id', 101)
					->where('category_id', 17)
					->where('id', '!=', $order->id)
					->where(function ($query) use ($domain3) {
						$query->where('link', 'like', "%://{$domain3}%")
							->orWhere('link2', 'like', "%://{$domain3}%")
							->orWhere('link3', 'like', "%://{$domain3}%");
					})
					->where('created_at', '>=', Carbon::now()->subDays(30))
					->latest('created_at')
					->first();

				if ($url3) {
					$expiryDate = Carbon::parse($url3->created_at)->addDays(30);
					$daysRemaining = Carbon::now()->diffInDays($expiryDate, false);
					if ($daysRemaining < 0) $daysRemaining = 0;
					$notify[] = ['error', "This Exit URL domain was recently used in the Nano Pack. Please try again after {$daysRemaining} days."];
					return back()->withNotify($notify);
				}
			}
		} else {
			$request->validate([
				'link' => ['required', 'url'],
				'link2' => ['nullable', 'url'],
				'link3' => ['nullable', 'url'],
			], [
				'link.url' => 'The "Your URL" format is invalid.',
				'link2.url' => 'The "Next URL" format is invalid.',
				'link3.url' => 'The "Exit URL" format is invalid.',
			]);
		}

		$link1 = $request->link;
		$domain = preg_replace('/^(.*?)\.?[^.]+\.(?:[^.]+\.)?([^.]+\.[^.]+)$/', '$1$2', parse_url($link1, PHP_URL_HOST));
		if ($request->link2 !== null) {
			$link2 = $request->link2;
			$domain2 = preg_replace('/^(.*?)\.?[^.]+\.(?:[^.]+\.)?([^.]+\.[^.]+)$/', '$1$2', parse_url($link2, PHP_URL_HOST));
			if ($domain !== $domain2) {
				$notify[] = ['error', "Next URL does not match the domain of Your URL: $domain. You can use only 1 domain per Campaign"];

				return back()->withNotify($notify);
			}
		}
		if ($request->link3 !== null) {
			$link3 = $request->link3;
			$domain3 = preg_replace('/^(.*?)\.?[^.]+\.(?:[^.]+\.)?([^.]+\.[^.]+)$/', '$1$2', parse_url($link3, PHP_URL_HOST));
			if ($domain !== $domain3) {
				$notify[] = ['error', "Exit URL does not match the domain of Your URL: $domain. You can use only 1 domain per Campaign"];

				return back()->withNotify($notify);
			}
		}

		$is_autorenew = $request->has('autorenew');
		$is_random_time_page = $request->has('randomize_time');
		$is_engagement = $request->has('engagement');

		$hasChanges = false;
		if ($order->name != $request->name) {
			$hasChanges = true;
		}
		if ($order->link != $request->link) {
			$hasChanges = true;
		}
		if ($order->link2 != $request->link2) {
			$hasChanges = true;
		}
		if ($order->link3 != $request->link3) {
			$hasChanges = true;
		}
		if ($order->speed != $request->bouncerate_range) {
			$hasChanges = true;
		}
		if ($order->tp != $request->timeonpage) {
			$hasChanges = true;
		}
		if ($order->lang != $request->lang) {
			$hasChanges = true;
		}
		// Example comparison for the 'behaviour' field
		$behaviour = $request->input('behaviour', $order->td); // Use the current value as default
		if ($order->td != $behaviour) {
			$hasChanges = true;
		}

		// Example comparison for the 'country' field
		$country = $request->input('country', $order->country); // Use the current value as default
		if ($order->country != $country) {
			$hasChanges = true;
		}

		// Example comparison for the 'traffictype' field
		$traffictype = $request->input('traffictype', $order->tt); // Use the current value as default
		if ($order->tt != $traffictype) {
			$hasChanges = true;
		}

		if ($request->traffictype == 2 && empty($request->keyword)) {
			$notify[] = ['error', 'Please add keywords.'];
			return back()->withNotify($notify);
		} elseif ($request->traffictype == 3 && empty($request->social)) {
			$notify[] = ['error', 'Please add social links.'];
			return back()->withNotify($notify);
		} elseif ($request->traffictype == 4 && empty($request->referrer)) {
			$notify[] = ['error', 'Please add referral links.'];
			return back()->withNotify($notify);
		}


		$order->auto_renew = $is_autorenew ? 1 : 0;
		$order->random_time_page = $is_random_time_page ? 1 : 0;
		$order->engagement = $is_engagement ? 1 : 0;
		if ($hasChanges) {
			$order->name = $request->title;
			$order->link = $request->link;
			$order->link2 = $request->link2;
			$order->link3 = $request->link3;
			if ($request->traffictype == 1) {
				$order->tt = $request->traffictype;
			} elseif ($request->traffictype == 2) {
				$order->tt = $request->traffictype;

				// Process keywords from textarea
				$keywords = preg_split('/[\r\n,]+/', $request->keyword); // Split by new lines or commas
				$keywords = array_map('trim', $keywords); // Trim each keyword
				$keywords = array_filter($keywords); // Remove empty entries
				$order->keyword = implode(',', $keywords); // Convert back to comma-separated
			} elseif ($request->traffictype == 3) {
				$order->tt = $request->traffictype;

				// Process social input
				$socials = preg_split('/[\r\n,]+/', $request->social); // Split by new lines or commas
				$socials = array_map('trim', $socials); // Trim each social value
				$socials = array_filter($socials); // Remove empty entries

				$invalid = [];

				$socials = array_filter($socials, function ($url) use (&$invalid) {
					if (!filter_var($url, FILTER_VALIDATE_URL)) {
						$invalid[] = $url;
						return false;
					}
					return true;
				});
				
				if (!empty($invalid)) {
					return back()->withErrors(['social' => 'Invalid Format: ' . implode(', ', $invalid) . '. <br><br>Each social referrer must be a valid URL starting with https://']);
				}

				$order->social = implode(',', $socials); // Convert back to comma-separated
			} elseif ($request->traffictype == 4) {
				$order->tt = $request->traffictype;

				// Process referrer input
				$referrers = preg_split('/[\r\n,]+/', $request->referrer); // Split by new lines or commas
				$referrers = array_map('trim', $referrers); // Trim each referrer value
				$referrers = array_filter($referrers); // Remove empty entries

				$invalid = [];

				$referrers = array_filter($referrers, function ($url) use (&$invalid) {
					if (!filter_var($url, FILTER_VALIDATE_URL)) {
						$invalid[] = $url;
						return false;
					}
					return true;
				});
				
				if (!empty($invalid)) {
					return back()->withErrors(['referrer' => 'Invalid Format: ' . implode(', ', $invalid) . '. <br><br>Each referrer must be a valid URL starting with https://']);
				}
				

				$order->ref = implode(',', $referrers); // Convert back to comma-separated
			}

			if ($order->traffic_plan == 101) {
				$order->country = "Worldwide";
				$order->speed = 2000;
			} else {
				$order->country = $request->country;
				$order->speed = $request->bouncerate_range;				
			}
			if ($order->api_order_id == 0) {
				$order->order_placed_to_api = 0;
			} else {
				$order->order_placed_to_api = 2;
			}
			$order->tp = $request->timeonpage;
			$order->td = $request->behaviour;
			// Process Language input
			$langs = preg_split('/[\r\n,]+/', $request->lang); // Split by new lines or commas
			$langs = array_map('trim', $langs); // Trim each social value
			$langs = array_filter($langs); // Remove empty entries
			$order->lang = implode(',', $langs); // Convert back to comma-separated

			if ($order->traffic_plan == 101 && $order->status == 0) {
				$order->status = 1;
			}

			$order->save();

			//Create admin notification
			$adminNotification = new AdminNotification();
			$adminNotification->user_id = $order->user_id;
			$adminNotification->title = 'WT Campaign Updated for ' . $order->name . '(ID:' . $order->id . ')';
			$adminNotification->click_url = urlPath('admin.wt.details', $order->id);
			$adminNotification->save();

			$notify[] = ['success', 'Successfully Updated! Changes may take some time to reflect'];
		} else {
			$order->save();
			$notify[] = ['info', 'Saved'];
		}
		return back()->withNotify($notify);
	}

	public function webPause($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$user = auth()->user();

	    // Check status before resuming
	        if ($order->status == 2) {
        	    $notify[] = ['info', 'Campaign has been completed.'];
        	    return back()->withNotify($notify);
    	        }

    		if ($order->status == 5) {
        	    $notify[] = ['info', 'Campaign has been expired, please renew it.'];
        	    return back()->withNotify($notify);
    		}


		$order->status = 6;
		if ($order->api_order_id != 0) {
			$order->order_placed_to_api = 3;
		}
		$order->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Pause Campaign ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.wt.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign has been paused!'];
		return back()->withNotify($notify);
	}

	public function webResume($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$user = auth()->user();

	    // Check status before resuming
	        if ($order->status == 2) {
        	    $notify[] = ['info', 'Campaign has been completed.'];
        	    return back()->withNotify($notify);
    	        }

    		if ($order->status == 5) {
        	    $notify[] = ['info', 'Campaign has been expired, please renew it.'];
        	    return back()->withNotify($notify);
    		}

		$order->status = 1;
		if ($order->api_order_id != 0) {
			$order->order_placed_to_api = 4;
		}
		$order->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Resume Campaign ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.wt.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign has been resumed!'];
		return back()->withNotify($notify);
	}

	public function webNano()
	{
		$date = Carbon::now();
		$user = auth()->user();
		if ($user->traffic_nano == 0 & $user->traffic_exp < $date) {
			$user->traffic_nano = 1;
			$user->traffic_exp = \Carbon\Carbon::now()->addDays(30);
			$user->save();

			$transaction = new Transaction();
			$transaction->user_id = $user->id;
			$transaction->credits = 1;
			$transaction->trx_type = '+';
			$transaction->details = 'Web Traffic - Free Nano Credit';
			$transaction->trx = getTrx();
			$transaction->remark = 'FREE CREDIT';
			$transaction->save();

			$notify[] = ['success', 'Nano Credit has been added to your account.'];
			return to_route('user.web.home')->withNotify($notify);
		} elseif ($user->traffic_exp > $date) {
			$notify[] = ['error', 'Next Credit will be available after ' . showMonthTime($user->traffic_exp)];
			return back()->withNotify($notify);
		} else {
			$notify[] = ['error', 'You can have a maximum of 1 Nano credit on your balance.'];
			return back()->withNotify($notify);
		}
	}

	public function webRenew($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$price = 1;
		$user = auth()->user();
		// Check if user has any credit for the selected traffic plan
		if (
			($order->traffic_plan == 101 && $user->traffic_nano == 0) ||
			($order->traffic_plan == 102 && $user->traffic_mini == 0) ||
			($order->traffic_plan == 103 && $user->traffic_small == 0) ||
			($order->traffic_plan == 104 && $user->traffic_medium == 0) ||
			($order->traffic_plan == 105 && $user->traffic_large == 0) ||
			($order->traffic_plan == 106 && $user->traffic_ultimate == 0)
		) {
			$notify[] = ['error', 'Insufficient Credits!'];
			return back()->withNotify($notify);
		}

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

		$order->traffic_exp = \Carbon\Carbon::now()->addDays(30);
		$order->created_at = \Carbon\Carbon::now();
		if ($order->traffic_plan == 101) {
			$order->remain = 2000;
		} elseif ($order->traffic_plan == 102) {
			$order->remain = 20000;
		} elseif ($order->traffic_plan == 103) {
			$order->remain = 100000;
		} elseif ($order->traffic_plan == 104) {
			$order->remain = 200000;
		} elseif ($order->traffic_plan == 105) {
			$order->remain = 333333;
		} elseif ($order->traffic_plan == 106) {
			$order->remain = 666666;
		}
		$order->start_counter = 0;
		$order->status = 1;
		$order->price = $price;
		$order->api_order = 1;
		$order->api_provider_id = 1;
		$order->api_order_id = 0;
		$order->order_placed_to_api = 0;

		$order->save();

		//Create Transaction
		$transaction = new Transaction();
		$transaction->user_id = $user->id;
		$transaction->credits = $price;
		$transaction->trx_type = '-';
		$transaction->details = 'Web Traffic Campaign(Renewal) - ' . $order->id;
		$transaction->trx = getTrx();
		$transaction->remark = 'RENEW';
		$transaction->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Web Traffic Campaign Renewed for ' . $order->id;
		$adminNotification->click_url = urlPath('admin.wt.details', $order->id);
		$adminNotification->save();

		//Send email to user
		notify($user, 'WEBTRAFFIC_RENEW', [
			'price' => $price,
			'trx' => $transaction->trx,
			'order_id' => $order->id,
			'link' => $order->link,
			'quantity' => $order->quantity,
			'expiry' => $order->traffic_exp,
			'speed' => $order->speed,
			'name' => $order->name,
			'geo' => $order->country,
		]);


		$notify[] = ['success', 'Successfully Renewed!'];
		return back()->withNotify($notify);
	}

	public function webReports($id)
	{
		$user = auth()->user();
		$order = Order::where('id', $id)->where('user_id', $user->id)->where('category_id', '17')->first();
		if ($order) {
			$hits = WebTrafficReports::where('user_id', auth()->id())->where('order_id', $id)->selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->orderBy('created_at', 'desc')->get();
			$empty_message = 'No history found.';
			$pageTitle = 'Campaign Reports - ' . $order->id;
			return view('Template::user.traffic_orders.reports', compact('pageTitle', 'hits', 'empty_message'));
		} else {
			$notify[] = ['error', 'Not Found!'];
			return redirect()->back()->withNotify($notify);
		}
	}

	public function WebDetails($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);

		$pageTitle = 'Campaign Details';
		return view('Template::user.traffic_orders.details', compact('pageTitle', 'order'));
	}


	protected function webData($scope = null)
	{
		if ($scope) {
			$orders = Order::$scope();
		} else {
			$orders = Order::query();
		}
		return $orders->directOrder()->where('user_id', auth()->id())->whereHas('category', function ($query) {
			$query->where('id', [17]);
		})->searchable(['id', 'service:name'])->filter(['category_id'])->dateFilter()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
	}

	public function webHistory()
	{
		$pageTitle = 'Website Traffic - Campaign History';
		$orders = $this->webData();
		$empty_message = 'Not Found';
		return view('Template::user.traffic_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function webPending()
	{
		$pageTitle = "On-hold Campaigns";
		$orders = $this->webData('pending');
		$empty_message = "No result found";
		return view('Template::user.traffic_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function webProcessing()
	{
		$pageTitle = "Processing Campaigns";
		$orders = $this->webData('processing');
		$empty_message = "No result found";
		return view('Template::user.traffic_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function webCompleted()
	{
		$pageTitle = "Completed Campaigns";
		$orders = $this->webData('completed');
		$empty_message = "No result found";
		return view('Template::user.traffic_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function webDenied()
	{
		$pageTitle = "Denied Campaigns";
		$orders = $this->webData('denied');
		$empty_message = "No result found";
		return view('Template::user.traffic_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function webPaused()
	{
		$pageTitle = "Paused Campaigns";
		$orders = $this->webData('paused');
		$empty_message = "No result found";
		return view('Template::user.traffic_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	// ========================================
	// REALISTIC WEB TRAFFIC CAMPAIGNS
	// ========================================

	/**
	 * Create a new realistic web traffic order
	 * 
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function realisticOrder(Request $request)
	{
		$request->validate([
			'link' => 'required|url',
			'plan' => 'required|integer',
		]);
		$user = auth()->user();
		// Check if user has any credit for the selected traffic plan
		if (
			($request->plan == 111 && $user->traffic_r_nano == 0) ||
			($request->plan == 112 && $user->traffic_r_mini == 0) ||
			($request->plan == 113 && $user->traffic_r_small == 0) ||
			($request->plan == 114 && $user->traffic_r_medium == 0) ||
			($request->plan == 115 && $user->traffic_r_large == 0) ||
			($request->plan == 116 && $user->traffic_r_ultimate == 0)
		) {
			$notify[] = ['error', 'Insufficient Credits!'];
			return back()->withNotify($notify);
		}

		$service = Service::with('category')->active()->findOrFail($request->plan);

		if ($request->plan == 1) {
			$domainsJson = file_get_contents(storage_path('domains.json'));
			$checkDomains = json_decode($domainsJson, true);
			$pattern = '/\b(?:' . implode('|', $checkDomains) . ')\b/i';
			$request->validate([
				'link' => ['required', 'url', "not_regex:{$pattern}"],
			], [
				'link.required' => 'URL is required.',
				'link.url' => 'The URL format is invalid.',
				'link.not_regex' => 'Website is not eligible in the Nano Pack. Kindly create your campaign using paid credits instead.',
			]);
			$url = Order::where(function ($query) use ($request) {
				$domain = parse_url($request->link, PHP_URL_HOST); // Extract domain from the request link
				$query->where('link', 'like', "%$domain%")
					->orWhere('link2', 'like', "%$domain%")
					->orWhere('link3', 'like', "%$domain%");
			})->latest('created_at')->first();
			if ($url && Carbon::now()->diffInDays($url->created_at) < 30) {

				$daysRemaining = 30 - Carbon::now()->diffInDays($url->created_at);
				$notify[] = ['error', "URL is not eligible. Try again after $daysRemaining days"];

				return back()->withNotify($notify);
			}
		} else {
			$request->validate([
				'link' => ['required', 'url'],
			], [
				'link.url' => 'The URL format is invalid.',
			]);
		}

		$price = 1;

		//Subtract user balance
		$user = auth()->user();
		if ($request->plan == 111 && $user->traffic_r_nano >= $price) {
			$user->traffic_r_nano -= $price;
		} elseif ($request->plan == 112 && $user->traffic_r_mini >= $price) {
			$user->traffic_r_mini -= $price;
		} elseif ($request->plan == 113 && $user->traffic_r_small >= $price) {
			$user->traffic_r_small -= $price;
		} elseif ($request->plan == 114 && $user->traffic_r_medium >= $price) {
			$user->traffic_r_medium -= $price;
		} elseif ($request->plan == 115 && $user->traffic_r_large >= $price) {
			$user->traffic_r_large -= $price;
		} elseif ($request->plan == 116 && $user->traffic_r_ultimate >= $price) {
			$user->traffic_r_ultimate -= $price;
		} else {
			$notify[] = ['error', 'Something Went Wrong'];
			return back()->withNotify($notify);
		}
		$user->traffic_plan = $request->plan;
		$user->save();

		//Make order
		$order = new Order();
		$order->user_id = $user->id;
		$order->service_id = $request->plan;
		$order->category_id = $service->category->id;
		$order->link = $request->link;
		$order->country = "Worldwide";
		$order->name = $request->title;
		$order->traffic_plan = $request->plan;
		$order->br = 0;
		$order->td = "Random";
		$order->tt = 1;
		$order->tp = 1;
		$order->traffic_exp = \Carbon\Carbon::now()->addDays(30);
		$order->api_order = 1;
		$order->api_provider_id = 1;
		if ($request->plan == 111) {
			$order->quantity = 2000;
			$order->remain = 2500;
			$order->speed = 2000;
		} elseif ($request->plan == 112) {
			$order->quantity = 20000;
			$order->remain = 22000;
			$order->speed = 667;
		} elseif ($request->plan == 113) {
			$order->quantity = 100000;
			$order->remain = 110000;
			$order->speed = 3334;
		} elseif ($request->plan == 114) {
			$order->quantity = 200000;
			$order->remain = 210000;
			$order->speed = 6667;
		} elseif ($request->plan == 115) {
			$order->quantity = 333333;
			$order->remain = 350000;
			$order->speed = 11112;
		} elseif ($request->plan == 116) {
			$order->quantity = 666666;
			$order->remain = 690000;
			$order->speed = 22222;
		}
		$order->price = $price;
		$order->save();

		//Create Transaction
		$transaction = new Transaction();
		$transaction->user_id = $user->id;
		$transaction->credits = $price;
		$transaction->trx_type = '-';
		$transaction->details = 'New Realistic Website Traffic Campaign - ' . $order->name . '(ID:' . $order->id . ')';
		$transaction->trx = getTrx();
		$transaction->remark = 'NEW';
		$transaction->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Realistic Traffic Campaign for ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.rt.details', $order->id);
		$adminNotification->save();

		notify($user, 'PROCESSING_REALISTICTRAFFIC', [
			'service_name' => $service->name,
			'order_id' => $order->id,
			'name' => $order->name,
			'link' => $order->link,
			'speed' => $order->speed,
			'qty' => $order->quantity,
			'country' => $order->country,
			'exp' => $order->traffic_exp,
		]);
		$notify[] = ['success', 'Successfully created the campaign!'];
		return to_route('user.realistic.details', ['id' => $order->id])->withNotify($notify);
	}

	public function realisticUpdate(Request $request, $id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$request->validate([
			'link' => ['required', 'url'],
		], [
			'link.url' => 'The "Your URL" format is invalid.',
		]);

		$is_autorenew = $request->has('autorenew');
		$is_random_time_page = $request->has('randomize_time');
		$hasChanges = false;
		if ($order->link != $request->link) {
			$hasChanges = true;
		}
		if ($order->name != $request->name) {
			$hasChanges = true;
		}
		if ($order->lang != $request->lang) {
			$hasChanges = true;
		}
		if ($order->speed != $request->bouncerate_range) {
			$hasChanges = true;
		}
		if ($order->tp != $request->timeonpage) {
			$hasChanges = true;
		}
		// Example comparison for the 'behaviour' field
		$behaviour = $request->input('behaviour', $order->td); // Use the current value as default
		if ($order->td != $behaviour) {
			$hasChanges = true;
		}

		// Example comparison for the 'country' field
		$country = $request->input('country', $order->country); // Use the current value as default
		if ($order->country != $country) {
			$hasChanges = true;
		}

		// Example comparison for the 'traffictype' field
		$traffictype = $request->input('traffictype', $order->tt); // Use the current value as default
		if ($order->tt != $traffictype) {
			$hasChanges = true;
		}

		if ($request->traffictype == 2 && empty($request->keyword)) {
			$notify[] = ['error', 'Please add keywords.'];
			return back()->withNotify($notify);
		} elseif ($request->traffictype == 3 && empty($request->social)) {
			$notify[] = ['error', 'Please add social links.'];
			return back()->withNotify($notify);
		} elseif ($request->traffictype == 4 && empty($request->referrer)) {
			$notify[] = ['error', 'Please add referral links.'];
			return back()->withNotify($notify);
		}

		if ($hasChanges) {
			$order->name = $request->title;
			$order->auto_renew = $is_autorenew ? 1 : 0;
			$order->random_time_page = $is_random_time_page ? 1 : 0;
			$order->engagement = 1;
			$order->link = $request->link;
			$order->click_type = $request->click_type;
			$order->speed = $request->bouncerate_range;
			if ($request->traffictype == 1) {
				$order->tt = $request->traffictype;
			} elseif ($request->traffictype == 2) {
				$order->tt = $request->traffictype;

				// Process keywords from textarea
				$keywords = preg_split('/[\r\n,]+/', $request->keyword); // Split by new lines or commas
				$keywords = array_map('trim', $keywords); // Trim each keyword
				$keywords = array_filter($keywords); // Remove empty entries
				$order->keyword = implode(',', $keywords); // Convert back to comma-separated
			} elseif ($request->traffictype == 3) {
				$order->tt = $request->traffictype;

				// Process social input
				$socials = preg_split('/[\r\n,]+/', $request->social); // Split by new lines or commas
				$socials = array_map('trim', $socials); // Trim each social value
				$socials = array_filter($socials); // Remove empty entries
				$invalid = [];

				$socials = array_filter($socials, function ($url) use (&$invalid) {
					if (!filter_var($url, FILTER_VALIDATE_URL)) {
						$invalid[] = $url;
						return false;
					}
					return true;
				});
				
				if (!empty($invalid)) {
					return back()->withErrors(['social' => 'Invalid Format: ' . implode(', ', $invalid) . '. <br><br>Each social referrer must be a valid URL starting with https://']);
				}
				$order->social = implode(',', $socials); // Convert back to comma-separated
			} elseif ($request->traffictype == 4) {
				$order->tt = $request->traffictype;

				// Process referrer input
				$referrers = preg_split('/[\r\n,]+/', $request->referrer); // Split by new lines or commas
				$referrers = array_map('trim', $referrers); // Trim each referrer value
				$referrers = array_filter($referrers); // Remove empty entries
				$invalid = [];

				$referrers = array_filter($referrers, function ($url) use (&$invalid) {
					if (!filter_var($url, FILTER_VALIDATE_URL)) {
						$invalid[] = $url;
						return false;
					}
					return true;
				});
				
				if (!empty($invalid)) {
					return back()->withErrors(['referrer' => 'Invalid Format: ' . implode(', ', $invalid) . '. <br><br>Each referrer must be a valid URL starting with https://']);
				}
				
				$order->ref = implode(',', $referrers); // Convert back to comma-separated
			}

			if ($order->traffic_plan == 111) {
				$order->country = "Worldwide";
			} else {
				$order->country = $request->country;
			}
			$order->tp = $request->timeonpage;
			$order->td = $request->behaviour;
			$order->lang = $request->lang;
			if ($order->api_order_id == 0) {
				$order->order_placed_to_api = 0;
			} else {
				$order->order_placed_to_api = 2;
			}
			$order->save();

			//Create admin notification
			$adminNotification = new AdminNotification();
			$adminNotification->user_id = $order->user_id;
			$adminNotification->title = 'Realistic Traffic Campaign Updated for ' . $order->name . '(ID:' . $order->id . ')';
			$adminNotification->click_url = urlPath('admin.rt.details', $order->id);
			$adminNotification->save();

			$notify[] = ['success', 'Successfully Updated! Changes may take some time to reflect'];
		} else {
			$order->save();
			$notify[] = ['info', 'Saved'];
		}
		return back()->withNotify($notify);
	}
	public function realisticNano()
	{
		$date = Carbon::now();
		$user = auth()->user();
		if ($user->traffic_r_nano == 0 & $user->traffic_r_exp < $date) {
			$user->traffic_r_nano = 1;
			$user->traffic_r_exp = \Carbon\Carbon::now()->addDays(30);
			$user->save();

			$transaction = new Transaction();
			$transaction->user_id = $user->id;
			$transaction->credits = 1;
			$transaction->trx_type = '+';
			$transaction->details = 'Realistic Traffic - Free Nano Credit';
			$transaction->trx = getTrx();
			$transaction->remark = 'FREE CREDIT';
			$transaction->save();

			$notify[] = ['success', 'Nano Credit has been added to your account.'];
			return to_route('user.realistic.home')->withNotify($notify);
		} elseif ($user->traffic_r_exp > $date) {
			$notify[] = ['error', 'Next Credit will be available after ' . showMonthTime($user->traffic_r_exp)];
			return back()->withNotify($notify);
		} else {
			$notify[] = ['error', 'You can have a maximum of 1 Nano credit on your balance.'];
			return back()->withNotify($notify);
		}
	}
	public function realisticPause($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$user = auth()->user();

		$order->status = 6;
		$order->order_placed_to_api = 3;
		$order->save();

		// $response = CurlRequest::curlPostContent($order->provider->api_url, [
		// 	'key' => $order->provider->api_key,
		// 	'action' => "stop",
		// 	'id' => $order->api_order_id,
		// 	]);
		// $response = json_decode($response);				

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Pause Realistic Campaign ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.rt.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign has been paused!'];
		return back()->withNotify($notify);
	}

	public function realisticResume($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$user = auth()->user();

		$order->status = 1;
		$order->order_placed_to_api = 4;
		$order->save();

		// $response = CurlRequest::curlPostContent($order->provider->api_url, [
		// 	'key' => $order->provider->api_key,
		// 	'action' => "resume",
		// 	'id' => $order->api_order_id,
		// 	]);


		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Resume Realistic Campaign ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.rt.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign has been resumed!'];
		return back()->withNotify($notify);
	}

	public function realisticRenew($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$price = 1;
		$user = auth()->user();
		if ($order->traffic_plan == 112 && $user->traffic_r_mini >= $price) {
			$user->traffic_r_mini -= $price;
		} elseif ($order->traffic_plan == 113 && $user->traffic_r_small >= $price) {
			$user->traffic_r_small -= $price;
		} elseif ($order->traffic_plan == 114 && $user->traffic_r_medium >= $price) {
			$user->traffic_r_medium -= $price;
		} elseif ($order->traffic_plan == 115 && $user->traffic_r_large >= $price) {
			$user->traffic_r_large -= $price;
		} elseif ($order->traffic_plan == 116 && $user->traffic_r_ultimate >= $price) {
			$user->traffic_r_ultimate -= $price;
		} else {
			$notify[] = ['error', 'Insufficient Credits!'];
			return back()->withNotify($notify);
		}
		$user->save();

		$order->traffic_exp = \Carbon\Carbon::now()->addDays(30);
		$order->created_at = \Carbon\Carbon::now();
		if ($order->traffic_plan == 2) {
			$order->remain = 20000;
		} elseif ($order->traffic_plan == 3) {
			$order->remain = 100000;
		} elseif ($order->traffic_plan == 4) {
			$order->remain = 200000;
		} elseif ($order->traffic_plan == 5) {
			$order->remain = 333333;
		} elseif ($order->traffic_plan == 6) {
			$order->remain = 666666;
		}
		$order->start_counter = 0;
		$order->status = 1;
		$order->price = $price;
		$order->api_order = 1;
		$order->api_provider_id = 1;
		$order->api_order_id = 0;
		$order->order_placed_to_api = 0;

		$order->save();

		//Create Transaction
		$transaction = new Transaction();
		$transaction->user_id = $user->id;
		$transaction->credits = $price;
		$transaction->trx_type = '-';
		$transaction->details = 'Realistic Web Traffic Campaign(Renewal) - ' . $order->id;
		$transaction->trx = getTrx();
		$transaction->remark = 'RENEW';
		$transaction->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Realistic Traffic Campaign Renewed for ' . $order->id;
		$adminNotification->click_url = urlPath('admin.rt.details', $order->id);
		$adminNotification->save();

		//Send email to user
		notify($user, 'REALISITICTRAFFIC_RENEW', [
			'price' => $price,
			'trx' => $transaction->trx,
			'order_id' => $order->id,
			'link' => $order->link,
			'quantity' => $order->quantity,
			'expiry' => $order->traffic_exp,
			'speed' => $order->speed,
			'name' => $order->name,
			'geo' => $order->country,
		]);


		$notify[] = ['success', 'Successfully Renewed!'];
		return back()->withNotify($notify);
	}

	public function realisticReports($id)
	{
		$user = auth()->user();
		$order = Order::where('id', $id)->where('user_id', $user->id)->where('category_id', '20')->first();
		if ($order) {
			$hits = WebTrafficReports::where('user_id', auth()->id())->where('order_id', $id)->selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->orderBy('created_at', 'desc')->get();
			$empty_message = 'No history found.';
			$pageTitle = 'Campaign Reports - ' . $order->id;
			return view('Template::user.traffic_r_orders.reports', compact('pageTitle', 'hits', 'empty_message'));
		} else {
			$notify[] = ['error', 'Not Found!'];
			return redirect()->back()->withNotify($notify);
		}
	}

	protected function realisticData($scope = null)
	{
		if ($scope) {
			$orders = Order::$scope();
		} else {
			$orders = Order::query();
		}
		return $orders->directOrder()->where('user_id', auth()->id())->whereHas('category', function ($query) {
			$query->where('id', [20]);
		})->searchable(['id', 'service:name'])->filter(['category_id'])->dateFilter()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
	}

	public function realisticDetails($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);

		$pageTitle = 'Campaign Details';
		return view('Template::user.traffic_r_orders.details', compact('pageTitle', 'order'));
	}
	public function realisticHistory()
	{
		$pageTitle = 'Realistic Website Traffic - Campaign History';
		$orders = $this->realisticData();
		$empty_message = 'Not Found';
		return view('Template::user.traffic_r_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function realisticPending()
	{
		$pageTitle = "On-hold Campaigns";
		$orders = $this->realisticData('pending');
		$empty_message = "No result found";
		return view('Template::user.traffic_r_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function realisticProcessing()
	{
		$pageTitle = "Active Campaigns";
		$orders = $this->realisticData('processing');
		$empty_message = "No result found";
		return view('Template::user.traffic_r_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function realisticCompleted()
	{
		$pageTitle = "Completed Campaigns";
		$orders = $this->realisticData('completed');
		$empty_message = "No result found";
		return view('Template::user.traffic_r_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function realisticDenied()
	{
		$pageTitle = "Denied Campaigns";
		$orders = $this->realisticData('denied');
		$empty_message = "No result found";
		return view('Template::user.traffic_r_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	public function realisticPaused()
	{
		$pageTitle = "Paused Campaigns";
		$orders = $this->realisticData('paused');
		$empty_message = "No result found";
		return view('Template::user.traffic_r_orders.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	// ========================================
	// BOT TRAFFIC CAMPAIGNS
	// ========================================

	/**
	 * Store a new bot traffic campaign
	 * Validates input, checks bot status and credit availability
	 * 
	 * @param Request $request
	 * @return RedirectResponse
	 */
	public function botStore(Request $request)
    {

        $user = auth()->user();
        
        // Check if user has an active bot plan
        if ($user->bot_status != 1) {
            $notify[] = ['error', 'Sparky Traffic Bot is inactive. Please purchase a plan to create campaigns.'];
            return back()->withNotify($notify);
        }
        
        $urls = preg_split('/\r\n|\r|\n/', $request->input('urls'));
        $request->merge(['urls' => $urls]);
        $referrer_urls = preg_split('/\r\n|\r|\n/', $request->input('referrer_urls'));
        $request->merge(['referrer_urls' => $referrer_urls]);
        $custom_proxies = preg_split('/\r\n|\r|\n/', $request->input('custom_proxies'));
        $request->merge(['custom_proxies' => $custom_proxies]);

        $request->validate([
            'urls' => 'required|array',
            'urls.*' => 'required|url', // This validates each URL in the array
            'active_users' => 'required|integer',
            // 'custom_proxies' => 'nullable|array',
            // 'custom_proxies.*' => [
            //     'nullable',
            //     function ($attribute, $value, $fail) {
            //         if (!empty($value) && !preg_match('/^\d{1,3}(\.\d{1,3}){3}:\d+$/', $value) &&
            //             !preg_match('/^\d{1,3}(\.\d{1,3}){3}:\d+:[^:]+:[^:]+$/', $value)) {
            //             $fail($attribute . ' is not in the correct format (IP:port or IP:port:username:password).');
            //         }
            //     },
            // ],
        ], [
            'urls.required' => 'URLs are required.',
            'urls.array' => 'Add URLs each per line.',
            'urls.*.required' => 'Each URL is required.',
            'urls.*.url' => 'Invalid URL Found.',
            'active_users.required' => 'Active users field is required.',
            'active_users.integer' => 'Active users field must be a number.',
            // 'custom_proxies.array' => 'Custom proxies must be an array if provided.',
        ]);

        if ($request->custom_proxy_enabled == 1 && (is_null($request->custom_proxies) || empty(array_filter($request->custom_proxies, 'trim')))) {
            $notify[] = ['error', 'Proxy List is Empty. Please add proxies to the list!'];
            return back()->withNotify($notify);
        }
        // Validate custom proxy format if enabled
        if ($request->custom_proxy_enabled == 1 && !empty($request->custom_proxies)) {
            // Normalize proxy input first (handle commas, spaces, newlines)
            $proxyInput = is_array($request->custom_proxies) ? implode(',', $request->custom_proxies) : $request->custom_proxies;
            // Split by any combination of whitespace and commas
            $proxyInput = preg_replace('/[\s,]+/', ',', $proxyInput);
            $proxyInput = trim($proxyInput, ',');
            $proxies = array_filter(array_map('trim', explode(',', $proxyInput)));
            
            foreach ($proxies as $index => $proxy) {
                if (empty($proxy)) continue;
                
                // Pattern: IP:PORT or DOMAIN:PORT or IP:PORT:USERNAME:PASSWORD or DOMAIN:PORT:USERNAME:PASSWORD
                if (!preg_match('/^([a-zA-Z0-9.-]+):(\d+)(:[^:]+:[^:]+)?$/', $proxy, $matches)) {
                    $notify[] = ['error', "Invalid proxy format on line " . ($index + 1) . ": \"{$proxy}\". Expected IP:PORT or DOMAIN:PORT or IP:PORT:USERNAME:PASSWORD"];
                    return back()->withNotify($notify);
                }
                
                $host = $matches[1];
                $port = (int)$matches[2];
                
                // Validate IP if it's an IP address (not domain)
                if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $host)) {
                    $octets = explode('.', $host);
                    foreach ($octets as $octet) {
                        if ($octet < 0 || $octet > 255) {
                            $notify[] = ['error', "Invalid IP address in proxy on line " . ($index + 1) . ": \"{$proxy}\""];
                            return back()->withNotify($notify);
                        }
                    }
                }
                
                // Validate port range
                if ($port < 1 || $port > 65535) {
                    $notify[] = ['error', "Invalid port in proxy on line " . ($index + 1) . ": \"{$proxy}\". Port must be between 1 and 65535"];
                    return back()->withNotify($notify);
                }
            }
        }
        if ($request->referrer_enabled == 1 && (is_null($request->referrer_urls) || empty(array_filter($request->referrer_urls, 'trim')))) {
            $notify[] = ['error', 'Referrer Links is Empty. Please add referrer links to the list!'];
            return back()->withNotify($notify);
        }
        if ($request->search_enabled == 1 && is_null($request->search_keywords)) {
            $notify[] = ['error', 'Search keywords is Empty. Please add keywords to the list!'];
            return back()->withNotify($notify);
        }
        // Check bot credit limits for new campaign
        $activeUsers = $request->active_users;
        $availableCredit = $user->bot_credit - $user->bot_used;
        
        if ($activeUsers > $availableCredit) {
            $notify[] = ['error', 'Insufficient bot credit! You need ' . $activeUsers . ' credits, but only ' . $availableCredit . ' available. (Total: ' . $user->bot_credit . ', Used: ' . $user->bot_used . ')'];
            return back()->withNotify($notify);
        }
        
        // Reserve credits for this campaign
        $user->bot_used += $activeUsers;
        $user->save();

        //Make Project
		$order = new Order();
        $order->user_id = $user->id;
		$order->traffic_exp = $user->bot_exp;
		$order->category_id = 21;
		$order->status = 1;
		$order->api_order = 1;
		$order->api_provider_id = 1;
		$order->api_order_id = 0;
		$order->order_placed_to_api = 0;
        
        // Convert URLs to comma-separated string - handle array or string input with multiple delimiters
        $urlsString = is_array($request->urls) ? implode(',', $request->urls) : $request->urls;
        $urlsString = preg_replace('/[,\s]+/', ',', $urlsString);
        $urlsArray = array_filter(array_map('trim', explode(',', $urlsString)));
        $order->urls = implode(',', $urlsArray);
        
        $order->name = $request->name;
        $order->url_order = $request->url_order;
        $order->td = $request->devices;

        // Set ext_type based on conditional logic
        if ($request->referrer_enabled == 1) {
            $order->ext_type = 'referrer';
            $order->tt = 4;
            // Normalize referrer URLs to comma-separated string
            $referrerString = is_array($request->referrer_urls) ? implode(',', $request->referrer_urls) : $request->referrer_urls;
            $referrerString = preg_replace('/[,\s]+/', ',', $referrerString);
            $referrerArray = array_filter(array_map('trim', explode(',', $referrerString)));
            $order->ref = implode(',', $referrerArray);
        } elseif ($request->search_enabled == 1) {
            $order->ext_type = $request->search_engine; // Store the actual search engine value (google, google_maps, bing)
        } else {
            $order->ext_type = 'direct';
			$order->tt = 1;
        }
        
        $order->min_delay = $request->min_delay;
        $order->max_delay = $request->max_delay;
        $order->min_task_delay = $request->min_task_delay;
        $order->max_task_delay = $request->max_task_delay;
        $order->quantity = $request->max_visits;
        $order->time_out = $request->timeout;
        $order->speed = $request->active_users;
        
        // Build script string from widget data
        $scriptParts = [];
        $secs = $request->input('secs', []);
        
        foreach ($secs as $index => $widgetType) {
            if ($widgetType === 'URL') {
                $url = $request->input("url.{$index}");
                if ($url) {
                    $scriptParts[] = "url='{$url}'";
                }
            } elseif ($widgetType === 'Click') {
                $clickType = $request->input("click_type.{$index}");
                $clickPercentage = $request->input("click_percentage.{$index}");
                if ($clickType && $clickPercentage) {
                    $scriptParts[] = "click={$clickType}:{$clickPercentage}";
                }
            } elseif ($widgetType === 'Wait') {
                $minWait = $request->input("min_wait.{$index}");
                $maxWait = $request->input("max_wait.{$index}");
                if ($minWait && $maxWait) {
                    $scriptParts[] = "wait={$minWait}:{$maxWait}";
                }
            } elseif ($widgetType === 'Scroll') {
                $scrollType = $request->input("scroll_type.{$index}");
                $scrollCount = $request->input("scroll_count.{$index}", '1');
                $scrollPercentage = $request->input("scroll_percentage.{$index}", '0');
                $scrollDelay = $request->input("scroll_delay.{$index}", '0');
                if ($scrollType) {
                    $scriptParts[] = "scroll={$scrollType}:{$scrollCount}:{$scrollPercentage}:{$scrollDelay}";
                }
            } elseif ($widgetType === 'Refresh') {
                $scriptParts[] = 'refresh';
            } elseif ($widgetType === 'NavigateForward') {
                $scriptParts[] = 'nav=forward';
            } elseif ($widgetType === 'NavigateBack') {
                $scriptParts[] = 'nav=back';
            } elseif ($widgetType === 'LoadPageFull') {
                $scriptParts[] = 'loadpage=load';
            }
        }
        
        $order->flow = !empty($scriptParts) ? implode(';', $scriptParts) : '';
        
        // Convert accept_language to comma-separated string - handle multiple delimiters
        $acceptLanguage = $request->accept_language ?? 'en-US';
        $acceptLanguageString = preg_replace('/[,\s\r\n]+/', ',', $acceptLanguage);
        $acceptLanguageArray = array_filter(array_map('trim', explode(',', $acceptLanguageString)));
        $order->lang = !empty($acceptLanguageArray) ? implode(',', $acceptLanguageArray) : 'en-US';

        // Set proxy_type and proxy based on conditional logic (one is always mandatory)
        if ($request->free_proxy_enabled == 1) {
            $order->proxy_type = 'free';
            
            // Load proxy map from centralized config file
            $proxyMap = config('proxies.proxy_urls');
            $defaultProxy = config('proxies.default');
            $defaultProxyUrl = $proxyMap[$defaultProxy] ?? 'premium-proxy.sparkcliks.com:18803';
            
            // Set proxy value based on country selection, default to configured default
            $order->proxy = $proxyMap[$request->free_proxy_country] ?? $defaultProxyUrl;
            
        } elseif ($request->custom_proxy_enabled == 1) {
            $order->proxy_type = 'custom';
            
            // Normalize custom proxies to comma-separated string
            $proxyString = is_array($request->custom_proxies) ? implode(',', $request->custom_proxies) : $request->custom_proxies;
            $proxyString = preg_replace('/[,\s]+/', ',', $proxyString);
            $proxyArray = array_filter(array_map('trim', explode(',', $proxyString)));
            $order->proxy = implode(',', $proxyArray);
        }

        // Build config JSON object for resource loading settings
        $configArray = [
            'image' => $request->load_images == 1 ? 'enabled' : 'disabled',
            'video' => $request->load_videos == 1 ? 'enabled' : 'disabled',
            'font' => $request->load_fonts == 1 ? 'enabled' : 'disabled',
            'css' => $request->load_css == 1 ? 'enabled' : 'disabled',
            'script' => $request->load_scripts == 1 ? 'enabled' : 'disabled'
        ];
        $order->config = json_encode($configArray);
		$order->api_order = 1;
		$order->api_provider_id = 1;
		$order->api_order_id = 0;
		$order->order_placed_to_api = 0;

        $order->save();

        // Script string is now stored in $order->flow field
        // No need to create individual sections as the script is stored as a formatted string
        // Format: "url='<url>';click=<type>:<ctr>;wait=<min>:<max>;scroll=<type>:<count>:<percentage>:<delay>;refresh;nav=<type>"
        
        // foreach ($request->input('secs', []) as $sectionKey => $sectionValue) {
        //     $settings = [];
    
        //     if ($sectionValue === 'URL') {
        //         $settings['url'] = $request->input('url')[$sectionKey] ?? '';
        //     } elseif ($sectionValue === 'Wait') {
        //         $settings['min_wait'] = $request->input('min_wait')[$sectionKey] ?? 0;
        //         $settings['max_wait'] = $request->input('max_wait')[$sectionKey] ?? 0;
        //     } elseif ($sectionValue === 'Scroll') {
        //         $settings['scroll_type'] = $request->input('scroll_type')[$sectionKey] ?? 'Random';
        //         $settings['scroll_percentage'] = $request->input('scroll_percentage')[$sectionKey] ?? 0;
        //     } elseif ($sectionValue === 'Click') {
        //         $settings['click_type'] = $request->input('click_type')[$sectionKey] ?? 'Internal';
        //         $settings['click_percentage'] = $request->input('click_percentage')[$sectionKey] ?? 0;
        //     } elseif (in_array($sectionValue, ['Refresh', 'NavigateForward', 'NavigateBack', 'LoadPageFull'])) {
        //         $settings['enabled'] = 'true';
        //     }
    
        //     $sectionData = [
        //         'type' => $sectionValue,
        //         'settings' => $settings,
        //         'order' => $sectionKey,
        //     ];
                
        //     $order->sections()->create($sectionData);
        // }

		//Create Transaction (tracking bot credit reservation)
		// $transaction = new Transaction();
		// $transaction->user_id = $user->id;
		// $transaction->credits = $activeUsers;
		// $transaction->trx_type = '-';
		// $transaction->details = 'Bot Credits Reserved - New Campaign: ' . $order->name . ' (ID:' . $order->id . ')';
		// $transaction->trx = getTrx();
		// $transaction->remark = 'BOT_RESERVED';
		// $transaction->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'New Traffic Bot Campaign ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.tb.details', $order->id);
		$adminNotification->save();

        $notify[] = ['success', 'Campaign created successfully!'];
        return redirect()->route('user.bot.details', $order->id)->withNotify($notify);
    }

	/**
	 * Get filtered bot campaign data with pagination
	 * 
	 * @param string|null $scope
	 * @return \Illuminate\Pagination\LengthAwarePaginator
	 */
	protected function botData($scope = null)
	{
		if ($scope) {
			$orders = Order::$scope();
		} else {
			$orders = Order::query();
		}
		return $orders->directOrder()->where('user_id', auth()->id())->whereHas('category', function ($query) {
			$query->where('id', [21]);
		})->searchable(['id', 'service:name'])->filter(['category_id'])->dateFilter()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
	}

	/**
	 * Display bot campaign details
	 * 
	 * @param int $id
	 * @return \Illuminate\View\View
	 */
	public function botDetails($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$user = auth()->user();
		$pageTitle = 'Campaign Details';
		return view('Template::user.bot.details', compact('pageTitle', 'order', 'user'));
	}

	/**
	 * Display all bot campaign history
	 * 
	 * @return \Illuminate\View\View
	 */
	public function botHistory()
	{
		$pageTitle = 'Bot Traffic - Campaign History';
		$orders = $this->botData();
		$empty_message = 'Not Found';
		return view('Template::user.bot.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	/**
	 * Display pending bot campaigns
	 * 
	 * @return \Illuminate\View\View
	 */
	public function botPending()
	{
		$pageTitle = "On-hold Campaigns";
		$orders = $this->botData('pending');
		$empty_message = "No result found";
		return view('Template::user.bot.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	/**
	 * Display processing/active bot campaigns
	 * 
	 * @return \Illuminate\View\View
	 */
	public function botProcessing()
	{
		$pageTitle = "Active Campaigns";
		$orders = $this->botData('processing');
		$empty_message = "No result found";
		return view('Template::user.bot.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	/**
	 * Display completed bot campaigns
	 * 
	 * @return \Illuminate\View\View
	 */
	public function botCompleted()
	{
		$pageTitle = "Completed Campaigns";
		$orders = $this->botData('completed');
		$empty_message = "No result found";
		return view('Template::user.bot.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	/**
	 * Display cancelled bot campaigns
	 * 
	 * @return \Illuminate\View\View
	 */
	public function botCancelled()
	{
		$pageTitle = "Cancelled Campaigns";
		$orders = $this->botData('cancelled');
		$empty_message = "No result found";
		return view('Template::user.bot.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	/**
	 * Display paused bot campaigns
	 * 
	 * @return \Illuminate\View\View
	 */
	public function botPaused()
	{
		$pageTitle = "Paused Campaigns";
		$orders = $this->botData('paused');
		$empty_message = "No result found";
		return view('Template::user.bot.history', compact('pageTitle', 'orders', 'empty_message'));
	}

	/**
	 * Update an existing bot traffic campaign
	 * Validates input, checks bot status and adjusts credit usage
	 * 
	 * @param Request $request
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function botUpdate(Request $request, $id)
	{
		$user = auth()->user();
		
		// Check if user has an active bot plan
		if ($user->bot_status != 1) {
			$notify[] = ['error', 'Sparky Traffic Bot is inactive. Please purchase a plan to update campaigns.'];
			return back()->withNotify($notify);
		}
		
		$order = Order::where('user_id', $user->id)->findOrFail($id);
		
		// Prevent updates for completed, cancelled, or expired campaigns
		if (in_array($order->status, [2, 4, 5])) {
			$statusLabel = $order->status == 2 ? 'completed' : ($order->status == 4 ? 'cancelled' : 'expired');
			$notify[] = ['error', 'Cannot update a ' . $statusLabel . ' campaign.'];
			return back()->withNotify($notify);
		}
		
		// Process URLs input
		$urls = preg_split('/\r\n|\r|\n/', $request->input('urls'));
		$request->merge(['urls' => $urls]);
		$referrer_urls = preg_split('/\r\n|\r|\n/', $request->input('referrer_urls'));
		$request->merge(['referrer_urls' => $referrer_urls]);
		$custom_proxies = preg_split('/\r\n|\r|\n/', $request->input('custom_proxies'));
		$request->merge(['custom_proxies' => $custom_proxies]);

		// Validate the request
		$request->validate([
			'urls' => 'required|array',
			'urls.*' => 'required|url',
			'active_users' => 'required|integer',
		], [
			'urls.required' => 'URLs are required.',
			'urls.array' => 'Add URLs each per line.',
			'urls.*.required' => 'Each URL is required.',
			'urls.*.url' => 'Invalid URL Found.',
			'active_users.required' => 'Active users field is required.',
			'active_users.integer' => 'Active users field must be a number.',
		]);

		// Validate conditional fields
		if ($request->custom_proxy_enabled == 1 && !empty($request->custom_proxies)) {
			// Normalize proxy input first (handle commas, spaces, newlines)
			$proxyInput = is_array($request->custom_proxies) ? implode(',', $request->custom_proxies) : $request->custom_proxies;
			// Split by any combination of whitespace and commas
			$proxyInput = preg_replace('/[\s,]+/', ',', $proxyInput);
			$proxyInput = trim($proxyInput, ',');
			$proxies = array_filter(array_map('trim', explode(',', $proxyInput)));
			
			foreach ($proxies as $index => $proxy) {
				if (empty($proxy)) continue;
				
				// Pattern: IP:PORT or DOMAIN:PORT or IP:PORT:USERNAME:PASSWORD or DOMAIN:PORT:USERNAME:PASSWORD
				if (!preg_match('/^([a-zA-Z0-9.-]+):(\d+)(:[^:]+:[^:]+)?$/', $proxy, $matches)) {
					$notify[] = ['error', "Invalid proxy format on line " . ($index + 1) . ": \"{$proxy}\". Expected IP:PORT or DOMAIN:PORT or IP:PORT:USERNAME:PASSWORD"];
					return back()->withNotify($notify);
				}
				
				$host = $matches[1];
				$port = (int)$matches[2];
				
				// Validate IP if it's an IP address (not domain)
				if (preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $host)) {
					$octets = explode('.', $host);
					foreach ($octets as $octet) {
						if ($octet < 0 || $octet > 255) {
							$notify[] = ['error', "Invalid IP address in proxy on line " . ($index + 1) . ": \"{$proxy}\""];
							return back()->withNotify($notify);
						}
					}
				}
				
				// Validate port range
				if ($port < 1 || $port > 65535) {
					$notify[] = ['error', "Invalid port in proxy on line " . ($index + 1) . ": \"{$proxy}\". Port must be between 1 and 65535"];
					return back()->withNotify($notify);
				}
			}
		}
		if ($request->referrer_enabled == 1 && (is_null($request->referrer_urls) || empty(array_filter($request->referrer_urls, 'trim')))) {
			$notify[] = ['error', 'Referrer Links is Empty. Please add referrer links to the list!'];
			return back()->withNotify($notify);
		}
		if ($request->search_enabled == 1 && is_null($request->search_keywords)) {
			$notify[] = ['error', 'Search keywords is Empty. Please add keywords to the list!'];
			return back()->withNotify($notify);
		}

		// Check bot credit limits if active users changed
		$currentUsed = $order->speed ?? 0;
		$newUsed = $request->active_users;
		$difference = $newUsed - $currentUsed;
		
		if ($difference > 0) {
			// Increasing active users - check if user has enough available credit
			$availableCredit = $user->bot_credit - $user->bot_used;
			if ($difference > $availableCredit) {
				$notify[] = ['error', 'Insufficient bot credit! You need ' . $difference . ' more credits, but only ' . $availableCredit . ' available.'];
				return back()->withNotify($notify);
			}
			// Increase bot_used
			$user->bot_used += $difference;
			$user->save();
		} elseif ($difference < 0) {
			// Decreasing active users - free up credits (add back to available pool)
			$releaseAmount = abs($difference); // Convert negative to positive
			$user->bot_used = max(0, $user->bot_used - $releaseAmount); // Ensure bot_used doesn't go below 0
			$user->save();
		}

		// Update basic campaign details
		$order->name = $request->name;
		
		// Convert URLs to comma-separated string
		$urlsString = is_array($request->urls) ? implode(',', $request->urls) : $request->urls;
		$urlsString = preg_replace('/[,\s]+/', ',', $urlsString);
		$urlsArray = array_filter(array_map('trim', explode(',', $urlsString)));
		$order->urls = implode(',', $urlsArray);
		
		$order->url_order = $request->url_order;
		$order->td = $request->devices;

		// Update ext_type and related fields
		if ($request->referrer_enabled == 1) {
			$order->ext_type = 'referrer';
			$order->tt = 4;
			// Normalize referrer URLs to comma-separated string
			$referrerString = is_array($request->referrer_urls) ? implode(',', $request->referrer_urls) : $request->referrer_urls;
			$referrerString = preg_replace('/[,\s]+/', ',', $referrerString);
			$referrerArray = array_filter(array_map('trim', explode(',', $referrerString)));
			$order->ref = implode(',', $referrerArray);
		} elseif ($request->search_enabled == 1) {
			$order->ext_type = $request->search_engine;
			$order->ref = $request->search_keywords ?? '';
		} else {
			$order->ext_type = 'direct';
			$order->tt = 1;
			$order->ref = null;
		}

		// Update timing and limits
		$order->min_task_delay = $request->min_task_delay ?? 10;
		$order->max_task_delay = $request->max_task_delay ?? 30;
		$order->min_delay = $request->min_delay ?? 10;
		$order->max_delay = $request->max_delay ?? 30;
		$order->quantity = $request->max_visits ?? 100;
		$order->time_out = $request->timeout ?? 60;
		$order->speed = $request->active_users;

		// Build script string from widget data (maintaining order)
		$scriptParts = [];
		$secs = $request->input('secs', []);
		
		// Sort by keys to maintain order from the frontend
		ksort($secs);
		
		foreach ($secs as $index => $widgetType) {
			if ($widgetType === 'URL') {
				$url = $request->input("url.{$index}");
				if ($url) {
					$scriptParts[] = "url='{$url}'";
				}
			} elseif ($widgetType === 'Click') {
				$clickType = $request->input("click_type.{$index}");
				$clickPercentage = $request->input("click_percentage.{$index}");
				if ($clickType && $clickPercentage) {
					$scriptParts[] = "click={$clickType}:{$clickPercentage}";
				}
			} elseif ($widgetType === 'Wait') {
				$minWait = $request->input("min_wait.{$index}");
				$maxWait = $request->input("max_wait.{$index}");
				if ($minWait && $maxWait) {
					$scriptParts[] = "wait={$minWait}:{$maxWait}";
				}
			} elseif ($widgetType === 'Scroll') {
				$scrollType = $request->input("scroll_type.{$index}");
				$scrollCount = $request->input("scroll_count.{$index}", '1');
				$scrollPercentage = $request->input("scroll_percentage.{$index}", '0');
				$scrollDelay = $request->input("scroll_delay.{$index}", '0');
				if ($scrollType) {
					$scriptParts[] = "scroll={$scrollType}:{$scrollCount}:{$scrollPercentage}:{$scrollDelay}";
				}
			} elseif ($widgetType === 'Refresh') {
				$scriptParts[] = 'refresh';
			} elseif ($widgetType === 'NavigateForward') {
				$scriptParts[] = 'nav=forward';
			} elseif ($widgetType === 'NavigateBack') {
				$scriptParts[] = 'nav=back';
			} elseif ($widgetType === 'LoadPageFull') {
				$scriptParts[] = 'loadpage=load';
			}
		}
		
		$order->flow = !empty($scriptParts) ? implode(';', $scriptParts) : '';

		// Update accept language
		$acceptLanguage = $request->accept_language ?? 'en-US';
		$acceptLanguageString = preg_replace('/[,\s\r\n]+/', ',', $acceptLanguage);
		$acceptLanguageArray = array_filter(array_map('trim', explode(',', $acceptLanguageString)));
		$order->lang = !empty($acceptLanguageArray) ? implode(',', $acceptLanguageArray) : 'en-US';

		// Update proxy settings - always set proxy_type and proxy
		if ($request->free_proxy_enabled == 1) {
			$order->proxy_type = 'free';
			
			// Load proxy map from centralized config file
			$proxyMap = config('proxies.proxy_urls');
			$defaultProxy = config('proxies.default');
			$defaultProxyUrl = $proxyMap[$defaultProxy] ?? 'premium-proxy.sparkcliks.com:18803';
			
			$order->proxy = $proxyMap[$request->free_proxy_country] ?? $defaultProxyUrl;
			
		} elseif ($request->custom_proxy_enabled == 1) {
			$order->proxy_type = 'custom';
			
			// Normalize custom proxies to comma-separated string (handles newlines, commas, spaces)
			$proxyString = is_array($request->custom_proxies) ? implode(',', $request->custom_proxies) : $request->custom_proxies;
			// Replace any combination of whitespace (spaces, newlines, tabs) and commas with a single comma
			$proxyString = preg_replace('/[\s,]+/', ',', $proxyString);
			// Remove leading/trailing commas
			$proxyString = trim($proxyString, ',');
			$proxyArray = array_filter(array_map('trim', explode(',', $proxyString)));
			$order->proxy = implode(',', $proxyArray);
		} else {
			// Fallback: if neither is enabled, maintain existing proxy
			// This shouldn't happen due to frontend validation, but handle it gracefully
			$order->proxy_type = $order->proxy_type ?? 'custom';
		}

		// Update configuration settings
		$configArray = [
			'image' => $request->load_images == 1 ? 'enabled' : 'disabled',
			'video' => $request->load_videos == 1 ? 'enabled' : 'disabled',
			'font' => $request->load_fonts == 1 ? 'enabled' : 'disabled',
			'css' => $request->load_css == 1 ? 'enabled' : 'disabled',
			'script' => $request->load_scripts == 1 ? 'enabled' : 'disabled'
		];
		$order->config = json_encode($configArray);
		if ($order->api_order_id != 0) {
			$order->order_placed_to_api = 2;
		}
		// Save the updated order
		$order->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Traffic Bot Update ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.tb.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign updated successfully!'];
		return redirect()->route('user.bot.details', $order->id)->withNotify($notify);
	}

	/**
	 * Pause a bot traffic campaign
	 * Sets status to paused (6) and flags API for pause action
	 * 
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function botPause($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$user = auth()->user();

		// Prevent pausing completed, cancelled, or expired campaigns
		if (in_array($order->status, [2, 4, 5])) {
			$statusLabel = $order->status == 2 ? 'completed' : ($order->status == 4 ? 'cancelled' : 'expired');
			$notify[] = ['error', 'Cannot pause a ' . $statusLabel . ' campaign.'];
			return back()->withNotify($notify);
		}


		$order->status = 6;
		if ($order->api_order_id != 0) {
			$order->order_placed_to_api = 3;
		}
		$order->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Pause Campaign ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.tb.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign has been paused!'];
		return back()->withNotify($notify);
	}

	/**
	 * Resume a paused bot traffic campaign
	 * Sets status to active (1) and flags API for resume action
	 * 
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function botResume($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$user = auth()->user();

		// Prevent resuming completed, cancelled, or expired campaigns
		if (in_array($order->status, [2, 4, 5])) {
			$statusLabel = $order->status == 2 ? 'completed' : ($order->status == 4 ? 'cancelled' : 'expired');
			$notify[] = ['error', 'Cannot resume a ' . $statusLabel . ' campaign.'];
			return back()->withNotify($notify);
		}

		$order->status = 1;
		if ($order->api_order_id != 0) {
			$order->order_placed_to_api = 4;
		}
		$order->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Resume Campaign ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.tb.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign has been resumed!'];
		return back()->withNotify($notify);
	}

	/**
	 * Renew an expired or completed bot traffic campaign
	 * Checks bot credit availability and adjusts speed if needed
	 * No payment required - uses subscription credits only
	 * 
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function botRenew($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$user = auth()->user();
		// Check if user has an active bot plan
		if ($user->bot_status != 1) {
			$notify[] = ['error', 'Sparky Traffic Bot is inactive. Please purchase a plan to update campaigns.'];
			return back()->withNotify($notify);
		}
		// Check if campaign is expired (5) or completed (2) - credits need to be reserved
		if (in_array($order->status, [2, 5])) {
			$originalSpeed = $order->speed;
			$availableCredits = $user->bot_credit - $user->bot_used;
			
			// Check if user has at least 1 credit available
			if ($availableCredits < 1) {
				$notify[] = ['error', 'Insufficient bot credits! You need at least 1 browser slot to renew. Available: ' . $availableCredits . ' (Total: ' . $user->bot_credit . ', Used: ' . $user->bot_used . ')'];
				return back()->withNotify($notify);
			}
			
			// Adjust speed if original speed exceeds available credits
			if ($originalSpeed > $availableCredits) {
				$order->speed = 1; // Set to minimum
				$creditsToUse = 1;
				$notify[] = ['info', 'Campaign speed adjusted from ' . $originalSpeed . ' to 1 browser due to limited credits. You can increase it later from campaign settings.'];
			} else {
				// User has enough credits for original speed
				$creditsToUse = $originalSpeed;
			}
			
			// Increase bot_used by the speed amount
			$user->bot_used += $creditsToUse;
			$user->save();
		}
		// If campaign is active/paused (not expired/completed), credits are already reserved - no action needed

		// Extend campaign expiry
		$order->traffic_exp = $user->bot_exp;
		$order->created_at = \Carbon\Carbon::now();
		$order->start_counter = 0; // Reset visit counter
		$order->status = 1; // Set to active
		
		// Reset API placement flags if needed
		if ($order->api_order_id != 0) {
			$order->order_placed_to_api = 0;
		}
		
		$order->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Bot Campaign Renewed - ' . $order->name . ' (ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.tb.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign Successfully Renewed!'];
		return back()->withNotify($notify);
	}

	/**
	 * Cancel a bot traffic campaign
	 * Releases reserved bot credits back to available pool
	 * No refund - bot credits are subscription-based
	 * 
	 * @param int $id
	 * @return RedirectResponse
	 */
	public function botCancel($id)
	{
		$order = Order::where('user_id', auth()->id())->findOrFail($id);
		$order->status = 4;
		$order->save();

		$user = auth()->user();
		// Release the reserved bot credits (browser slots) back to available pool
		$releasedCredits = $order->speed; // Active users (browsers) that were reserved
		
		// Free up the bot_used credits
		$user->bot_used = max(0, $user->bot_used - $releasedCredits);
		$user->save();

		//Create Transaction (for tracking purposes - no monetary refund, just credit release)
		$transaction = new Transaction();
		$transaction->user_id = $user->id;
		$transaction->credits = $releasedCredits;
		$transaction->trx_type = '+';
		$transaction->details = 'Cancelled Campaign: ' . $order->name . ' (ID:' . $order->id . ')';
		$transaction->trx = getTrx();
		$transaction->remark = 'REFUNDED';
		$transaction->save();

		//Create admin notification
		$adminNotification = new AdminNotification();
		$adminNotification->user_id = $user->id;
		$adminNotification->title = 'Campaign Cancelled for ' . $order->name . '(ID:' . $order->id . ')';
		$adminNotification->click_url = urlPath('admin.tb.details', $order->id);
		$adminNotification->save();

		$notify[] = ['success', 'Campaign Cancelled!'];
		return back()->withNotify($notify);
	}
}
