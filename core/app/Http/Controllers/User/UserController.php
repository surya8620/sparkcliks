<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\GoogleAuthenticator;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\Referral;
use App\Models\Service;
use App\Models\Clicks;
use App\Models\Category;
use App\Models\SupportTicket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use App\Models\WebTrafficReports;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function home()
    {
        $pageTitle                   = 'Dashboard';
        $user                        = auth()->user();
        $widget['balance']           = $user->balance;
        $widget['total_spent']       = Order::where('status', '!=', Status::ORDER_REFUNDED)->where('user_id', $user->id)->sum('price');
        $widget['total_transaction'] = Transaction::where('user_id', $user->id)->count();
        $widget['total_order']       = Order::directOrder()->where('user_id', $user->id)->count();
        $widget['pending_order']     = Order::directOrder()->where('user_id', $user->id)->pending()->count();
        $widget['processing_order']  = Order::directOrder()->where('user_id', $user->id)->processing()->count();
        $widget['completed_order']   = Order::directOrder()->where('user_id', $user->id)->completed()->count();
        $widget['cancelled_order']   = Order::directOrder()->where('user_id', $user->id)->cancelled()->count();

        $widget['total_dripfeed_order']      = Order::dripfeedOrder()->where('user_id', $user->id)->count();
        $widget['pending_dripfeed_order']    = Order::dripfeedOrder()->where('user_id', $user->id)->pending()->count();
        $widget['processing_dripfeed_order'] = Order::dripfeedOrder()->where('user_id', $user->id)->processing()->count();
        $widget['completed_dripfeed_order']  = Order::dripfeedOrder()->where('user_id', $user->id)->completed()->count();

        $widget['refunded_order'] = Order::directOrder()->where('user_id', $user->id)->refunded()->count();
        $widget['deposit']        = Deposit::successful()->where('user_id', $user->id)->sum('amount');

        $widget['total_ticket']      = SupportTicket::where('user_id', $user->id)->count();
        $widget['referral_earnings'] = Transaction::where('remark', 'referral_commission')->where('user_id', auth()->id())->sum('amount');
        $orders                      = Order::where('user_id', $user->id)->with(['category', 'user', 'service'])->orderBy('id', 'desc')->take(10)->get();
        $bestSellingServices         = Service::active()->with('category')
            ->withCount(['orders as total_orders' => function ($query) {
                $query->where('status', '!=', Status::ORDER_CANCELLED);
            }])->having('total_orders', '>', 0)->orderBy('total_orders', 'desc')->take(10)->get();

        return view('Template::user.dashboard', compact('pageTitle', 'widget', 'orders', 'bestSellingServices'));
    }

    public function seoHome()
    {
        $pageTitle = 'SEO (Search Console) - Dashboard';
        $general = gs();
        $user = auth()->user();
        $widget['balance'] = $user->balance;
        $widget['seocredit'] = $user->seocredit;
        $widget['mem_credit'] = $user->mem_credit;
        $widget['cur_time'] = \Carbon\Carbon::now()->toDateTimeString();
        $widget['mem_type'] = $user->mem_type;
        $widget['mem_exp'] = $user->mem_exp;
        $widget['total_spent'] = Order::where('status', '!=', 4)->where('user_id', $user->id)->sum('price');
        $widget['total_transaction'] = Transaction::where('user_id', $user->id)->count();
        $widget['total_order'] = Order::directOrder()->where('user_id', $user->id)->whereIn('category_id', [11, 12])->count();
        $widget['pending_order'] = Order::directOrder()->where('user_id', $user->id)->whereIn('category_id', [11, 12])->pending()->count();
        $widget['processing_order'] = Order::directOrder()->where('user_id', $user->id)->whereIn('category_id', [11, 12])->processing()->count();
        $widget['completed_order'] = Order::directOrder()->where('user_id', $user->id)->whereIn('category_id', [11, 12])->completed()->count();
        $widget['denied_order'] = Order::directOrder()->where('user_id', $user->id)->whereIn('category_id', [11, 12])->denied()->count();
        $widget['cancelled_order'] = Order::directOrder()->where('user_id', $user->id)->whereIn('category_id', [11, 12])->cancelled()->count();
        return view('Template::user.seo_orders.dashboard', compact('pageTitle', 'widget', 'general'));
    }

    public function botHome()
    {
        $pageTitle = 'Sparky Traffic Bot - Dashboard';
        $general = gs();
        $user = auth()->user();
        $widget['balance'] = $user->balance;
        $widget['bot_ack'] = $user->bot_ack;
        $widget['mem_credit'] = $user->bot_credit;
        $widget['cur_time'] = \Carbon\Carbon::now()->toDateTimeString();
        $widget['mem_type'] = $user->bot_plan;
        $widget['mem_exp'] = $user->bot_exp;
        $widget['mem_used'] = $user->bot_used;
        $widget['mem_status'] = $user->bot_status;
        $widget['total_spent'] = Order::where('status', '!=', 4)->where('user_id', $user->id)->sum('price');
        $widget['total_transaction'] = Transaction::where('user_id', $user->id)->count();
        $widget['total_order'] = Order::directOrder()->where('user_id', $user->id)->where('category_id', '21')->count();
        $widget['pending_order'] = Order::directOrder()->where('user_id', $user->id)->where('category_id', '21')->pending()->count();
        $widget['processing_order'] = Order::directOrder()->where('user_id', $user->id)->where('category_id', '21')->processing()->count();
        $widget['completed_order'] = Order::directOrder()->where('user_id', $user->id)->where('category_id', '21')->completed()->count();
        $widget['denied_order'] = Order::directOrder()->where('user_id', $user->id)->where('category_id', '21')->denied()->count();
        $widget['cancelled_order'] = Order::directOrder()->where('user_id', $user->id)->where('category_id', '21')->cancelled()->count();
        $widget['paused_order'] = Order::where('user_id', $user->id)->where('category_id', '21')->paused()->count();
        return view('Template::user.bot.dashboard', compact('pageTitle', 'widget', 'general'));
    }

    public function webHome()
    {
        $pageTitle = 'Website Traffic - Dashboard';
        $user = auth()->user();
        $widget['nano'] = $user->traffic_nano;
        $widget['mini'] = $user->traffic_mini;
        $widget['small'] = $user->traffic_small;
        $widget['medium'] = $user->traffic_medium;
        $widget['large'] = $user->traffic_large;
        $widget['ultimate'] = $user->traffic_ultimate;
        $widget['total_order'] = Order::where('user_id', $user->id)->where('category_id', '17')->count();
        $widget['processing_order'] = Order::where('user_id', $user->id)->where('category_id', '17')->processing()->count();
        $widget['completed_order'] = Order::where('user_id', $user->id)->where('category_id', '17')->completed()->count();
        $widget['pending_order'] = Order::where('user_id', $user->id)->where('category_id', '17')->pending()->count();
        $widget['paused_order'] = Order::where('user_id', $user->id)->where('category_id', '17')->paused()->count();
        $widget['nano_exp'] = $user->traffic_exp;
        $widget['time'] = \Carbon\Carbon::now();
        return view('Template::user.traffic_orders.dashboard', compact('pageTitle', 'widget'));
    }


    public function realisticHome()
    {
        $pageTitle = 'Realistic Website Traffic - Dashboard';
        $user = auth()->user();
        $widget['nano'] = $user->traffic_r_nano;
        $widget['mini'] = $user->traffic_r_mini;
        $widget['small'] = $user->traffic_r_small;
        $widget['medium'] = $user->traffic_r_medium;
        $widget['large'] = $user->traffic_r_large;
        $widget['ultimate'] = $user->traffic_r_ultimate;
        $widget['total_order'] = Order::where('user_id', $user->id)->where('category_id', '20')->count();
        $widget['processing_order'] = Order::where('user_id', $user->id)->where('category_id', '20')->processing()->count();
        $widget['completed_order'] = Order::where('user_id', $user->id)->where('category_id', '20')->completed()->count();
        $widget['pending_order'] = Order::where('user_id', $user->id)->where('category_id', '20')->pending()->count();
        $widget['paused_order'] = Order::where('user_id', $user->id)->where('category_id', '20')->paused()->count();
        $widget['nano_exp'] = $user->traffic_r_exp;
        $widget['time'] = \Carbon\Carbon::now();

        return view('Template::user.traffic_r_orders.dashboard', compact('pageTitle', 'widget'));
    }
    public function seoNew()
    {
        $pageTitle = 'Search Console - Create a New Campaign';
        $categories = Category::active()->whereHas('services', function ($services) {
            return $services->active();
        })->orderBy('name')->get();
        $user = auth()->user();
        return view('Template::user.seo_orders.add', compact('pageTitle', 'categories', 'user'));
    }

    public function webNew()
    {
        $pageTitle = 'Web Traffic - Create a New Campaign';
        $categories = Category::active()->whereHas('services', function ($services) {
            return $services->active();
        })->orderBy('name')->get();
        $user = auth()->user();
        return view('Template::user.traffic_orders.add', compact('pageTitle', 'categories', 'user'));
    }

    public function realisticNew()
    {
        $pageTitle = 'Realistic Website Traffic - Create a New Campaign';
        $categories = Category::active()->whereHas('services', function ($services) {
            return $services->active();
        })->orderBy('name')->get();
        $user = auth()->user();
        return view('Template::user.traffic_r_orders.add', compact('pageTitle', 'categories', 'user'));
    }

    public function botNew()
    {
        $pageTitle = 'Create a New Campaign';
        $categories = Category::active()->whereHas('services', function ($services) {
            return $services->active();
        })->orderBy('name')->get();
        $user = auth()->user();
        return view('Template::user.bot.create', compact('pageTitle', 'categories', 'user'));
    }

    public function botLogs($id = null)
    {
        $pageTitle = 'Campaign Logs - Real-time Monitoring';
        $user = auth()->user();
        $orderId = $id;
        
        // Validate that the order belongs to the user if ID is provided
        if ($orderId) {
            $order = Order::where('user_id', $user->id)
                ->where('id', $orderId)
                ->where('category_id', 21) // Bot category
                ->firstOrFail();
        }
        
        return view('Template::user.bot.logs', compact('pageTitle', 'user', 'orderId'));
    }

    /**
     * Fetch logs for a specific order via backend API call
     * This endpoint is called by AJAX from the frontend
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function botLogsFetch(Request $request)
    {
        $user = auth()->user();
        $orderId = $request->input('order_id');
        $lastLineCount = $request->input('last_line_count', 0);
        
        // Validate order ownership
        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Order ID is required'
            ], 400);
        }
        
        $order = Order::where('user_id', $user->id)
            ->where('id', $orderId)
            ->where('category_id', 21)
            ->first();
            
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized access'
            ], 404);
        }
        
        // Fetch logs from external API
        $logsData = $this->fetchLogsFromServer($user, $orderId, $lastLineCount);
        
        return response()->json($logsData);
    }

    /**
     * Download complete log file for an order
     * 
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function botLogsDownload(Request $request, $id)
    {
        $user = auth()->user();
        
        // Validate order ownership
        $order = Order::where('user_id', $user->id)
            ->where('id', $id)
            ->where('category_id', 21)
            ->first();
            
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or unauthorized access'
            ], 404);
        }
        
        try {
            // Check if WebSocket token is provided (new method)
            if ($request->has('token') && $request->has('serverUrl')) {
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
            }
            
            // Fallback: Use API Key method (old method)
            $credentials = $this->getLogServerCredentials($user);
            $serverUrl = str_replace(['ws://', 'wss://'], ['http://', 'https://'], $credentials['server_url']);
            $apiKey = $credentials['api_key'];
            
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$serverUrl}/api/logs/{$id}/download");
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Return JSON response
                return response()->json([
                    'success' => true,
                    'lines' => $data['lines'] ?? [],
                    'totalLines' => $data['totalLines'] ?? count($data['lines'] ?? []),
                    'fileSize' => $data['fileSize'] ?? strlen(implode("\n", $data['lines'] ?? []))
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch logs from server'
            ], 500);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while downloading logs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate WebSocket token for real-time log monitoring
     * This fetches a temporary token from the external log server
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function botLogsToken(Request $request)
    {
        $user = auth()->user();
        $orderId = $request->input('order_id');
        
        // Validate order_id is provided
        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Order ID is required'
            ], 400);
        }
        
        // Verify the order belongs to the authenticated user
        $order = Order::where('user_id', $user->id)
            ->where('id', $orderId)
            ->where('category_id', 21) // Bot category
            ->first();
            
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or you do not have permission to access this order'
            ], 404);
        }
        
        try {
            // Fetch dynamic token from external API
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
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate connection token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch logs from the log server via HTTP API
     * 
     * @param \App\Models\User $user
     * @param int $orderId
     * @param int $lastLineCount
     * @return array
     */
    private function fetchLogsFromServer($user, $orderId, $lastLineCount = 0)
    {
        try {
            $credentials = $this->getLogServerCredentials($user);
            $serverUrl = str_replace(['ws://', 'wss://'], ['http://', 'https://'], $credentials['server_url']);
            $apiKey = $credentials['api_key'];
            
            $response = Http::timeout(15)
                ->withHeaders([
                    'X-API-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->get("{$serverUrl}/api/logs/{$orderId}", [
                    'since_line' => $lastLineCount,
                    'limit' => 100
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                return [
                    'success' => true,
                    'lines' => $data['lines'] ?? [],
                    'total_lines' => $data['total_lines'] ?? 0,
                    'has_more' => $data['has_more'] ?? false,
                    'timestamp' => time()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to fetch logs from server',
                    'lines' => [],
                    'total_lines' => 0
                ];
            }
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while fetching logs',
                'error' => $e->getMessage(),
                'lines' => [],
                'total_lines' => 0
            ];
        }
    }

    /**
     * Get log server credentials from external API or fallback to hardcoded values
     * 
     * @param \App\Models\User $user
     * @return array ['server_url' => string, 'api_key' => string]
     */
    private function getLogServerCredentials($user)
    {
        // Get default values from config
        $defaultServerUrl = config('logs.server_url', '');
        $defaultApiKey = config('logs.api_key', '');
        
        // Check if external API is enabled
        if (!config('logs.external_api.enabled', false)) {
            return [
                'server_url' => $defaultServerUrl,
                'api_key' => $defaultApiKey,
            ];
        }
        
        // Fetch from external API
        try {
            $apiUrl = config('logs.external_api.url');
            $apiToken = config('logs.external_api.token');
            $timeout = config('logs.external_api.timeout', 10);
            
            $response = Http::timeout($timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Accept' => 'application/json',
                ])
                ->get($apiUrl, [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Validate response data
                if (isset($data['server_url']) && isset($data['api_key'])) {
                    return [
                        'server_url' => $data['server_url'],
                        'api_key' => $data['api_key'],
                    ];
                }
            } else {
                // API call failed, continue to fallback
            }
        } catch (\Exception $e) {
            // Exception occurred, continue to fallback
        }
        
        // Return default values as fallback
        return [
            'server_url' => $defaultServerUrl,
            'api_key' => $defaultApiKey,
        ];
    }


    //Realtime Graph Functions
    public function botRealtime()
    {
        $user = auth()->user();
        $id = $user->id;
        $start = Carbon::now()->subMinutes(30);

        // Querying data grouped by minutes
        $data = WebTrafficReports::selectRaw('DATE_FORMAT(created_at, "%H:%i") as created_minute, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('category_id', '21')
            ->where('created_at', '>=', $start)
            ->groupBy('created_minute')
            ->get();

        // Generate timestamps for every minute within the last 30 minutes
        $timestamps = [];
        $currentMinute = Carbon::now()->subMinutes(30)->startOfMinute();
        while ($currentMinute <= Carbon::now()) {
            $timestamps[] = $currentMinute->format('H:i');
            $currentMinute->addMinute();
        }

        // Initialize counts for each minute
        $counts = array_fill_keys($timestamps, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $timestamp = $item->created_minute;
            $counts[$timestamp] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'timestamps' => $timestamps,
            'visit' => $visits,
        ];
    }

    public function webRealtime()
    {
        $user = auth()->user();
        $id = $user->id;
        $start = Carbon::now()->subMinutes(30);

        // Querying data grouped by minutes
        $data = WebTrafficReports::selectRaw('DATE_FORMAT(created_at, "%H:%i") as created_minute, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('category_id', '17')
            ->where('created_at', '>=', $start)
            ->groupBy('created_minute')
            ->get();

        // Generate timestamps for every minute within the last 30 minutes
        $timestamps = [];
        $currentMinute = Carbon::now()->subMinutes(30)->startOfMinute();
        while ($currentMinute <= Carbon::now()) {
            $timestamps[] = $currentMinute->format('H:i');
            $currentMinute->addMinute();
        }

        // Initialize counts for each minute
        $counts = array_fill_keys($timestamps, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $timestamp = $item->created_minute;
            $counts[$timestamp] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'timestamps' => $timestamps,
            'visit' => $visits,
        ];
    }

    public function realisticRealtime()
    {
        $user = auth()->user();
        $id = $user->id;
        $start = Carbon::now()->subMinutes(30);

        // Querying data grouped by minutes
        $data = WebTrafficReports::selectRaw('DATE_FORMAT(created_at, "%H:%i") as created_minute, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('category_id', '20')
            ->where('created_at', '>=', $start)
            ->groupBy('created_minute')
            ->get();

        // Generate timestamps for every minute within the last 30 minutes
        $timestamps = [];
        $currentMinute = Carbon::now()->subMinutes(30)->startOfMinute();
        while ($currentMinute <= Carbon::now()) {
            $timestamps[] = $currentMinute->format('H:i');
            $currentMinute->addMinute();
        }

        // Initialize counts for each minute
        $counts = array_fill_keys($timestamps, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $timestamp = $item->created_minute;
            $counts[$timestamp] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'timestamps' => $timestamps,
            'visit' => $visits,
        ];
    }

    public function webRealtimeCampaign(Request $request)
    {
        $user = auth()->user();
        $id = $user->id;

        // Get the order_id from the request
        $orderId = $request->route('id');

        // Ensure order_id is provided
        if (!$orderId) {
            return response()->json(['error' => 'Unauthorized Access'], 400);
        }
        $orders       = Order::where('user_id', $id)->findOrFail($orderId);;
        if ($orders->user_id !== $id) {
            return response()->json(['error' => 'Unauthorized Access'], 400);
        }
        // Check if the order exists for the user
        $order = WebTrafficReports::where('order_id', $orderId)->where('user_id', $id)->first();

        // If order not found, return a "Not Found" response
        if (!$order) {
            $timestamps = [];
            $visits = [];
            
            for ($i = 29; $i >= 0; $i--) {
                $time = Carbon::now()->subMinutes($i)->format('H:i');
                $timestamps[] = $time;
                $visits[] = 0;
            }
            
            return response()->json([
                'timestamps' => $timestamps,
                'visit' => $visits,
            ]);
        }     
        // Check if the category_id is 17
        if ($order->category_id !== 17) {
            return response()->json(['error' => 'Unauthorized Access'], 403);
        }

        // Calculate the start time (30 minutes ago)
        $start = Carbon::now()->subMinutes(30);

        // Querying data grouped by minutes for this specific order
        $data = WebTrafficReports::selectRaw('DATE_FORMAT(created_at, "%H:%i") as created_minute, COUNT(*) as count')
            ->where('user_id', $id) // Filter by the authenticated user ID
            ->where('order_id', $orderId) // Filter by the provided order_id
            ->where('category_id', '17') // Fixed category filter
            ->where('created_at', '>=', $start) // Last 30 minutes
            ->groupBy('created_minute')
            ->get();

        // Generate timestamps for every minute within the last 30 minutes
        $timestamps = [];
        $currentMinute = Carbon::now()->subMinutes(30)->startOfMinute();
        while ($currentMinute <= Carbon::now()) {
            $timestamps[] = $currentMinute->format('H:i');
            $currentMinute->addMinute();
        }

        // Initialize counts for each minute
        $counts = array_fill_keys($timestamps, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $timestamp = $item->created_minute;
            $counts[$timestamp] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'timestamps' => $timestamps,
            'visit' => $visits,
        ];
    }



    public function realisticRealtimeCampaign(Request $request)
    {
        $user = auth()->user();
        $id = $user->id;

        // Get the order_id from the request
        $orderId = $request->route('id');

        // Ensure order_id is provided
        if (!$orderId) {
            return response()->json(['error' => 'Unauthorized Access'], 400);
        }
        $orders       = Order::where('user_id', $id)->findOrFail($orderId);;
        if ($orders->user_id !== $id) {
            return response()->json(['error' => 'Unauthorized Access'], 400);
        }
        // Check if the order exists for the user
        $order = WebTrafficReports::where('order_id', $orderId)->where('user_id', $id)->first();

        // If order not found, return a "Not Found" response
        if (!$order) {
            $timestamps = [];
            $visits = [];
            
            for ($i = 29; $i >= 0; $i--) {
                $time = Carbon::now()->subMinutes($i)->format('H:i');
                $timestamps[] = $time;
                $visits[] = 0;
            }
            
            return response()->json([
                'timestamps' => $timestamps,
                'visit' => $visits,
            ]);
        }       

        // Check if the category_id is 20
        if ($order->category_id !== 20) {
            return response()->json(['error' => 'Unauthorized Access'], 403);
        }
        // Calculate the start time (30 minutes ago)
        $start = Carbon::now()->subMinutes(30);

        // Querying data grouped by minutes
        $data = WebTrafficReports::selectRaw('DATE_FORMAT(created_at, "%H:%i") as created_minute, COUNT(*) as count')
            ->where('user_id', $id) // Filter by the authenticated user ID
            ->where('category_id', '20') // Fixed category filter
            ->where('order_id', $orderId) // Filter by order_id from the request
            ->where('created_at', '>=', $start) // Last 30 minutes
            ->groupBy('created_minute')
            ->get();

        // Generate timestamps for every minute within the last 30 minutes
        $timestamps = [];
        $currentMinute = Carbon::now()->subMinutes(30)->startOfMinute();
        while ($currentMinute <= Carbon::now()) {
            $timestamps[] = $currentMinute->format('H:i');
            $currentMinute->addMinute();
        }

        // Initialize counts for each minute
        $counts = array_fill_keys($timestamps, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $timestamp = $item->created_minute;
            $counts[$timestamp] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'timestamps' => $timestamps,
            'visit' => $visits,
        ];
    }
    public function botChart()
    {
        $user = auth()->user();
        $id = $user->id;
        $start = Carbon::now()->subDays(30)->startOfDay(); // Start of the 30 days ago

        // Querying data grouped by dates
        $data = WebTrafficReports::selectRaw('DATE(created_at) as created_date, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('category_id', '21')
            ->whereDate('created_at', '>=', $start)
            ->groupBy('created_date')
            ->get();

        // Generate dates for every day within the current day
        $dates = [];
        $currentDate = $start;
        while ($currentDate <= Carbon::now()) {
            $dates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }

        // Initialize counts for each date
        $counts = array_fill_keys($dates, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $date = $item->created_date;
            $counts[$date] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'dates' => $dates,
            'visit' => $visits,
        ];
    }
    public function webChart()
    {
        $user = auth()->user();
        $id = $user->id;
        $start = Carbon::now()->subDays(30)->startOfDay(); // Start of the 30 days ago

        // Querying data grouped by dates
        $data = WebTrafficReports::selectRaw('DATE(created_at) as created_date, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('category_id', '17')
            ->whereDate('created_at', '>=', $start)
            ->groupBy('created_date')
            ->get();

        // Generate dates for every day within the current day
        $dates = [];
        $currentDate = $start;
        while ($currentDate <= Carbon::now()) {
            $dates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }

        // Initialize counts for each date
        $counts = array_fill_keys($dates, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $date = $item->created_date;
            $counts[$date] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'dates' => $dates,
            'visit' => $visits,
        ];
    }
    public function realisticChart()
    {
        $user = auth()->user();
        $id = $user->id;
        $start = Carbon::now()->subDays(30)->startOfDay(); // Start of the 30 days ago

        // Querying data grouped by dates
        $data = WebTrafficReports::selectRaw('DATE(created_at) as created_date, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('category_id', '20')
            ->whereDate('created_at', '>=', $start)
            ->groupBy('created_date')
            ->get();

        // Generate dates for every day within the current day
        $dates = [];
        $currentDate = $start;
        while ($currentDate <= Carbon::now()) {
            $dates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }

        // Initialize counts for each date
        $counts = array_fill_keys($dates, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $date = $item->created_date;
            $counts[$date] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'dates' => $dates,
            'visit' => $visits,
        ];
    }
    public function seoChartCampaign(Request $request)
    {
        $user = auth()->user();
        $id = $user->id;

        // Get the order_id from the request
        $orderId = $request->route('id');

        // Ensure order_id is provided and is valid
        if (!$orderId) {
            return response()->json(['error' => 'Unauthorized Access'], 400);
        }

        // Check if the order belongs to the authenticated user
        $order = Order::where('id', $orderId)->where('user_id', auth()->id())->first();

        if (!$order) {
            return response()->json(['error' => 'Unauthorized Access'], 404);
        }

        if (!$order || $order->user_id !== $id) {
            return response()->json(['error' => 'Unauthorized Access'], 403);
        }


        $start = Carbon::now()->subDays(30)->startOfDay(); // Start of the 30 days ago

        // Querying data grouped by dates
        $data = Clicks::selectRaw('DATE(created_at) as created_date, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('order_id', $orderId)
            ->whereDate('created_at', '>=', $start)
            ->groupBy('created_date')
            ->get();

        // Generate dates for every day within the current day
        $dates = [];
        $currentDate = $start;
        while ($currentDate <= Carbon::now()) {
            $dates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }

        // Initialize counts for each date
        $counts = array_fill_keys($dates, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $date = $item->created_date;
            $counts[$date] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'dates' => $dates,
            'visit' => $visits,
        ];
    }
    public function webChartCampaign(Request $request)
    {
        $user = auth()->user();
        $id = $user->id;

        // Get the order_id from the request
        $orderId = $request->route('id');

        // Ensure order_id is provided and is valid
        if (!$orderId) {
            return response()->json(['error' => 'Unauthorized Access'], 400);
        }

        // Check if the order belongs to the authenticated user
        $order = Order::where('id', $orderId)->where('user_id', auth()->id())->first();

        if (!$order) {
            return response()->json(['error' => 'Unauthorized Access'], 404);
        }

        if (!$order || $order->user_id !== $id) {
            return response()->json(['error' => 'Unauthorized Access'], 403);
        }


        $start = Carbon::now()->subDays(30)->startOfDay(); // Start of the 30 days ago

        // Querying data grouped by dates
        $data = WebTrafficReports::selectRaw('DATE(created_at) as created_date, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('order_id', $orderId)
            ->where('category_id', '17')
            ->whereDate('created_at', '>=', $start)
            ->groupBy('created_date')
            ->get();

        // Generate dates for every day within the current day
        $dates = [];
        $currentDate = $start;
        while ($currentDate <= Carbon::now()) {
            $dates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }

        // Initialize counts for each date
        $counts = array_fill_keys($dates, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $date = $item->created_date;
            $counts[$date] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'dates' => $dates,
            'visit' => $visits,
        ];
    }

    public function realisticChartCampaign(Request $request)
    {
        $user = auth()->user();
        $id = $user->id;
        // Get the order_id from the request
        $orderId = $request->route('id');

        // Ensure order_id is provided and is valid
        if (!$orderId) {
            return response()->json(['error' => 'Unauthorized Access'], 400);
        }

        // Check if the order belongs to the authenticated user
        $order = Order::where('id', $orderId)->where('user_id', auth()->id())->first();

        if (!$order) {
            return response()->json(['error' => 'Unauthorized Access'], 404);
        }

        if (!$order || $order->user_id !== $id) {
            return response()->json(['error' => 'Unauthorized Access'], 403);
        }


        $start = Carbon::now()->subDays(30)->startOfDay(); // Start of the 30 days ago

        // Querying data grouped by dates
        $data = WebTrafficReports::selectRaw('DATE(created_at) as created_date, COUNT(*) as count')
            ->where('user_id', $id)
            ->where('order_id', $orderId)
            ->where('category_id', '20')
            ->whereDate('created_at', '>=', $start)
            ->groupBy('created_date')
            ->get();

        // Generate dates for every day within the current day
        $dates = [];
        $currentDate = $start;
        while ($currentDate <= Carbon::now()) {
            $dates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }

        // Initialize counts for each date
        $counts = array_fill_keys($dates, 0);

        // Fill counts array with data from the database
        foreach ($data as $item) {
            $date = $item->created_date;
            $counts[$date] = $item->count;
        }

        // Prepare the data for the chart
        $visits = array_values($counts);

        // Return the chart data to the view
        return [
            'dates' => $dates,
            'visit' => $visits,
        ];
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Payment History';
        $scopes    = ['', 'initiated', 'successful', 'rejected'];
        $scope     = $request->status;

        if (!in_array($scope, $scopes)) {
            $notify[] = ['error', 'Unauthorized action'];
            return to_route('user.billing.history')->withNotify($notify);
        }

        $user       = auth()->user();
        $currencies = Deposit::where('user_id', $user->id)->distinct()->pluck('method_currency');

        $gateways = Deposit::where('user_id', $user->id)->distinct()->with(['gateway' => function ($gateway) {
            $gateway->select('code', 'name');
        }])->get('method_code');

        $deposits = Deposit::where('user_id', $user->id)->when($scope, function ($query) use ($scope) {
            $query->$scope();
        })->searchable(['trx'])->filter(['method_currency', 'method_code'])->dateFilter()->with('gateway')->orderBy('id', 'desc');

        $deposits = $deposits->paginate(getPaginate());

        return view('Template::user.deposit_history', compact('pageTitle', 'deposits', 'currencies', 'gateways'));
    }

    public function viewInvoice($id)
    {
        $user = auth()->user();
        $pageTitle = 'Invoice';

        // Fetch the deposit details for the given ID
        $invoice = Deposit::where('user_id', $user->id)
            ->where('trx', $id)
            ->where('status', 1)
            ->with('gateway') // Load payment gateway details
            ->firstOrFail();
        if (!$invoice) {
            $notify[] = ['error', 'Not Found'];
            return back()->withNotify($notify);
        }
        if (is_null($invoice->inv_num)) {
            $notify[] = ['warning', 'Processing, invoice will be generated soon.'];
            return back()->withNotify($notify);
        }
        // Decode the JSON address field
        $address = json_decode($invoice->address, true);

        // Get the payment method details (Currency, etc.)
        $gatewayCurrency = $invoice->method_currency;

        return view('Template::user.invoice', compact('pageTitle', 'user', 'invoice', 'gatewayCurrency', 'address'));
    }

    public function downloadInvoice($id)
    {
        $user = auth()->user();
        $pageTitle = 'Invoice';

        // Fetch the deposit details for the given ID
        $invoice = Deposit::where('user_id', $user->id)
            ->where('trx', $id)
            ->where('status', 1)
            ->with('gateway') // Load payment gateway details
            ->firstOrFail();

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found or not approved.');
        }

        // Decode the JSON address field
        $address = json_decode($invoice->address, true);

        // Get the payment method details (Currency, etc.)
        $gatewayCurrency = $invoice->method_currency;

        $pdf = PDF::loadView('Template::user.download_invoice', compact('pageTitle', 'user', 'invoice', 'gatewayCurrency', 'address'));
        return $pdf->download("invoice_{$invoice->trx}.pdf");
    }


    public function referrals()
    {
        $pageTitle = 'My Referrals';
        $user      = auth()->user();
        $maxLevel  = Referral::max('level');
        return view('Template::user.referrals', compact('pageTitle', 'user', 'maxLevel'));
    }

    public function show2faForm()
    {
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . gs('site_name'), $secret);
        $pageTitle = '2FA Security';
        return view('Template::user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'key'  => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts  = Status::ENABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = Status::DISABLE;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions()
    {
        $pageTitle = 'Transactions';
        $remarks   = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function userData()
    {
        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.seo.home');
        }

        $pageTitle  = 'Profile';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.user_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }
    

    public function userDataSubmit(Request $request)
    {

        $user = auth()->user();

        if ($user->profile_complete == Status::YES) {
            return to_route('user.web.home');
        }

        $countryData  = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        
        $user->firstname    = $request->firstname;
        $user->lastname     = $request->lastname;
        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country = @$request->country;
        $user->dial_code    = $request->mobile_code;

        $user->profile_complete = Status::YES;
        $user->save();

        notify($user, 'NEW_USER');

        $notify[] = ['success', 'Profile Updated successfully'];
        return to_route('user.web.home')->withNotify($notify);
    }

    public function updateAdd()
    {
        $user = auth()->user();
    	if (!blank($user->address)) {
		return redirect()->route('user.seo.home'); // or wherever you want to redirect
    	}

        $pageTitle  = 'Update Profile';
        $info       = json_decode(json_encode(getIpInfo()), true);
        $mobileCode = @implode(',', $info['code']);
        $countries  = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view('Template::user.profile_data', compact('pageTitle', 'user', 'countries', 'mobileCode'));
    }

    public function updateAddSubmit(Request $request)
    {

        $user = auth()->user();
    	if (!blank($user->address)) {
		return redirect()->route('user.seo.home'); // or wherever you want to redirect
    	}

        $countryData  = (array) json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $countryCodes = implode(',', array_keys($countryData));
        $mobileCodes  = implode(',', array_column($countryData, 'dial_code'));
        $countries    = implode(',', array_column($countryData, 'country'));

        $request->validate([
            'country_code' => 'required|in:' . $countryCodes,
            'country'      => 'required|in:' . $countries,
            'mobile_code'  => 'required|in:' . $mobileCodes,
            'mobile'       => ['required', 'regex:/^([0-9]*)$/', Rule::unique('users')->where('dial_code', $request->mobile_code)],
        ]);

        $user->country_code = $request->country_code;
        $user->mobile       = $request->mobile;
        
        $user->firstname    = $request->firstname;
        $user->lastname     = $request->lastname;
        $user->address      = $request->address;
        $user->city         = $request->city;
        $user->state        = $request->state;
        $user->zip          = $request->zip;
        $user->country 	    = @$request->country;
        $user->dial_code    = $request->mobile_code;
        $user->org          = $request->org;
        $user->vat          = $request->vat;

        $user->save();

        $notify[] = ['success', 'Profile Updated successfully'];
        return back()->with('notify', $notify);
    }


    public function attachmentDownload($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title     = slug(gs('site_name')) . '- attachments.' . $extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error', 'File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function botAck(Request $request)
    {
        $user = auth()->user();

        // Validate the acceptance checkbox
        $request->validate([
            'acceptTerms' => 'required|accepted',
        ]);

        // Update bot_ack to 1 (accepted)
        $user->bot_ack = 1;
        $user->bot_plan = 121;
        $user->bot_status = 1;
        $user->bot_credit = 1;
        $user->bot_used = 0;
        $user->bot_exp = Carbon::now()->addDays(3);
        $user->save();

        $notify[] = ['success', 'Trial Activated Successfully'];
        return to_route('user.bot.home')->withNotify($notify);
    }
}
