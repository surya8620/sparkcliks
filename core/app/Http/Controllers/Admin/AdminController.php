<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
use App\Models\AdminNotification;
use App\Models\ApiProvider;
use App\Models\Order;
use App\Models\Deposit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\WebTrafficReports;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function dashboard()
    {
        $pageTitle = 'Dashboard';
        $providers =  ApiProvider::active()->orderBy('name')->whereHas('order')->get();

        $widget['total_users']             = User::count();
        $widget['verified_users']          = User::where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED)->count();
        $widget['profile_pending']         = User::where('status', Status::USER_ACTIVE)->where('profile_complete', Status::UNVERIFIED)->count();
        $widget['email_unverified_users']  = User::emailUnverified()->count();
        $widget['mobile_unverified_users'] = User::mobileUnverified()->count();
        $widget['total_order']             = Order::directOrder()->count();
        $widget['total_serp_order']        = Order::directOrder()->whereHas('category', function ($query) {$query->where('id', [11, 12]);})->count();
        $widget['total_wt_order']          = Order::directOrder()->whereHas('category', function ($query) {$query->where('id', [17]);})->count();
        $widget['total_rt_order']          = Order::directOrder()->whereHas('category', function ($query) {$query->where('id', [20]);})->count();
        $widget['total_tb_order']          = Order::directOrder()->whereHas('category', function ($query) {$query->where('id', [21]);})->count();
        $widget['pending_order']           = Order::directOrder()->pending()->count();
        $widget['processing_order']        = Order::directOrder()->processing()->count();
        $widget['completed_order']         = Order::directOrder()->completed()->count();
        $widget['cancelled_order']         = Order::directOrder()->cancelled()->count();
        $widget['refunded_order']          = Order::directOrder()->refunded()->count();


        $deposit['total_deposit_amount'] = Deposit::successful()->sum('amount');
        $deposit['total_deposit_amount_cy'] = Deposit::successful()->whereBetween('created_at', [ Carbon::now()->startOfYear(), Carbon::now()->endOfYear() ])->sum('amount');
        $deposit['total_deposit_amount_lm'] = Deposit::successful()->whereBetween('created_at', [ Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth() ])->sum('amount');
        $deposit['total_deposit_amount_cm'] = Deposit::successful()->whereBetween('created_at', [ Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth() ])->sum('amount');
        $deposit['total_deposit_pending'] = Deposit::pending()->count();
        $deposit['total_deposit_rejected'] = Deposit::rejected()->count();
        $deposit['total_deposit_charge'] = Deposit::successful()->sum('charge');


        return view('admin.dashboard', compact('pageTitle', 'widget','deposit'));
    }




    public function depositAndWithdrawReport(Request $request) {

        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $deposits = Deposit::successful()
        ->whereDate('created_at', '>=', $request->start_date)
        ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();


        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'deposits' => getAmount($deposits->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }

        $data = collect($data);

        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Payments',
                'data' => $data->pluck('deposits')
            ]
        ];

        return response()->json($report);
    }

    public function transactionReport(Request $request) {

        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $complete   = Deposit::where('status', 1)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $refund   = Deposit::where('status', 3)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $cancel   = Deposit::where('status', 2)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();



        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'completed' => getAmount($complete->where('created_on', $date)->first()?->amount ?? 0),
                'refunded' => getAmount($refund->where('created_on', $date)->first()?->amount ?? 0),
                'cancelled' => getAmount($cancel->where('created_on', $date)->first()?->amount ?? 0)
            ];
        }

        $data = collect($data);

        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Completed',
                'data' => $data->pluck('completed')
            ],
            [
                'name' => 'Refunded',
                'data' => $data->pluck('refunded')
            ],
            [
                'name' => 'Cancelled',
                'data' => $data->pluck('cancelled')
            ]
        ];

        return response()->json($report);
    }


    private function getAllDates($startDate, $endDate) {
        $dates = [];
        $currentDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-F-Y');
            $currentDate->modify('+1 day');
        }

        return $dates;
    }

    private function  getAllMonths($startDate, $endDate) {
        if ($endDate > now()) {
            $endDate = now()->format('Y-m-d');
        }

        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $months = [];

        while ($startDate <= $endDate) {
            $months[] = $startDate->format('F-Y');
            $startDate->modify('+1 month');
        }

        return $months;
    }


    public function profile()
    {
        $pageTitle = 'Profile';
        $admin = auth('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);
        $user = auth('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image;
                $user->image = fileUploader($request->image, getFilePath('adminProfile'), getFileSize('adminProfile'), $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload your image'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Profile updated successfully'];
        return to_route('admin.profile')->withNotify($notify);
    }

    public function password()
    {
        $pageTitle = 'Password Setting';
        $admin = auth('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = auth('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password doesn\'t match!!'];
            return back()->withNotify($notify);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return to_route('admin.password')->withNotify($notify);
    }

    public function notifications(){
        $notifications = AdminNotification::orderBy('id','desc')->with('user')->paginate(getPaginate());
        $hasUnread = AdminNotification::where('is_read',Status::NO)->exists();
        $hasNotification = AdminNotification::exists();
        $pageTitle = 'Notifications';
        return view('admin.notifications',compact('pageTitle','notifications','hasUnread','hasNotification'));
    }


    public function notificationRead($id){
        $notification = AdminNotification::findOrFail($id);
        $notification->is_read = Status::YES;
        $notification->save();
        $url = $notification->click_url;
        if ($url == '#') {
            $url = url()->previous();
        }
        return redirect($url);
    }

    public function readAllNotification(){
        AdminNotification::where('is_read',Status::NO)->update([
            'is_read'=>Status::YES
        ]);
        $notify[] = ['success','Notifications read successfully'];
        return back()->withNotify($notify);
    }

    public function deleteAllNotification(){
        AdminNotification::truncate();
        $notify[] = ['success','Notifications deleted successfully'];
        return back()->withNotify($notify);
    }

    public function deleteSingleNotification($id){
        AdminNotification::where('id',$id)->delete();
        $notify[] = ['success','Notification deleted successfully'];
        return back()->withNotify($notify);
    }

    public function downloadAttachment($fileHash)
    {
        $filePath = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $title = slug(gs('site_name')).'- attachments.'.$extension;
        try {
            $mimetype = mime_content_type($filePath);
        } catch (\Exception $e) {
            $notify[] = ['error','File does not exists'];
            return back()->withNotify($notify);
        }
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function providerChart($id)
    {
        $currentMonth = Carbon::now()->format('F');

        $orders = Order::where('api_provider_id', $id)
            ->whereBetween('created_at', [now()->subYear(), now()])
            ->get(['id', 'api_provider_id', 'created_at', 'price']);

        $groupedOrders = $orders->groupBy(function ($order) {
            return $order->created_at->format('F');
        })->map(function ($orders) {
            return $orders->sum('price');
        });

        $lastTwelveMonths = collect(range(0, 11))->map(function ($i) use ($currentMonth) {
            $month = Carbon::now()->subMonths($i)->format('F');
            return ($month === $currentMonth) ? $currentMonth : $month;
        });

        $groupedOrders = $lastTwelveMonths->mapWithKeys(function ($month) use ($groupedOrders) {
            return [$month => $groupedOrders[$month] ?? 0];
        });

        $groupedOrders =  array_reverse($groupedOrders->toArray());
        return response()->json($groupedOrders);
    }

    public function userReport(Request $request) {

        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $totalUsers   = User::whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $activeUsers  = User::where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();
        
        $bannedUsers  = User::where('status', 0)
            ->whereDate('updated_at', '>=', $request->start_date)
            ->whereDate('updated_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(updated_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $unverifiedUsers  = User::where('status', Status::USER_ACTIVE)->where('ev', Status::UNVERIFIED)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'totalUser' => getAmount($totalUsers->where('created_on', $date)->first()?->count ?? 0),
                'activeUser' => getAmount($activeUsers->where('created_on', $date)->first()?->count ?? 0),
                'bannedUser' => getAmount($bannedUsers->where('created_on', $date)->first()?->count ?? 0),
                'unverifiedUser' => getAmount($unverifiedUsers->where('created_on', $date)->first()?->count ?? 0)
            ];
        }

        $data = collect($data);

        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Total',
                'data' => $data->pluck('totalUser')
            ],
            [
                'name' => 'New',
                'data' => $data->pluck('activeUser')
            ],            
            [
                'name' => 'Banned',
                'data' => $data->pluck('bannedUser')
            ],
            [
                'name' => 'Unverified',
                'data' => $data->pluck('unverifiedUser')
            ]
        ];

        return response()->json($report);
    }
    public function campaignReport(Request $request) {

        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format = $diffInDays > 30 ? '%M-%Y'  : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        // $totalUsers   = User::whereDate('created_at', '>=', $request->start_date)
        //     ->whereDate('created_at', '<=', $request->end_date)
        //     ->selectRaw('COUNT(*) AS count')
        //     ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
        //     ->latest()
        //     ->groupBy('created_on')
        //     ->get();

        $totals  = Order::whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $completeds  = Order::where('status', 2)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();
        

        $serps  = Order::whereHas('category', function ($query) {$query->where('id', [11, 12]);})
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();
        
        $wts  = Order::whereHas('category', function ($query) {$query->where('id', [17]);})
            ->whereDate('updated_at', '>=', $request->start_date)
            ->whereDate('updated_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(updated_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $rts  = Order::whereHas('category', function ($query) {$query->where('id', [20]);})
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $tbs  = Order::whereHas('category', function ($query) {$query->where('id', [21]);})
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'total' => getAmount($totals->where('created_on', $date)->first()?->count ?? 0),
                'completed' => getAmount($completeds->where('created_on', $date)->first()?->count ?? 0),
                'serp' => getAmount($serps->where('created_on', $date)->first()?->count ?? 0),
                'wt' => getAmount($wts->where('created_on', $date)->first()?->count ?? 0),
                'rt' => getAmount($rts->where('created_on', $date)->first()?->count ?? 0),
                'tb' => getAmount($tbs->where('created_on', $date)->first()?->count ?? 0)
            ];
        }

        $data = collect($data);

        $report['created_on']   = $data->pluck('created_on');
        $report['data']     = [
            [
                'name' => 'Total',
                'data' => $data->pluck('total')
            ],
            [
                'name' => 'Completed',
                'data' => $data->pluck('completed')
            ],
            [
                'name' => 'SERP',
                'data' => $data->pluck('serp')
            ],
            [
                'name' => 'Website Traffic',
                'data' => $data->pluck('wt')
            ],
            [
                'name' => 'Realistic Traffic',
                'data' => $data->pluck('rt')
            ],
            [
                'name' => 'Traffic Bot',
                'data' => $data->pluck('tb')
            ]
        ];

        return response()->json($report);
    }
        public function orderReports()
    {
    // Fetch order IDs
    $orderIds = Order::where('status', 1)->pluck('id');

    // Prepare response data
    $responseData = [];

    foreach ($orderIds as $orderId) {
        $orderTrafficData = [];
        $orderTrafficData['order_id'] = $orderId;

        // Check web traffic reports
        $webTrafficReports = WebTrafficReports::where('order_id', $orderId)->get();

        if ($webTrafficReports->isEmpty()) {
            $orderTrafficData['found'] = false;
        } else {
            $orderTrafficData['found'] = true;
            $orderTrafficData['traffic_count_5min'] = $webTrafficReports->where('created_at', '>=', now()->subMinutes(5))->count();
            $orderTrafficData['traffic_count_15min'] = $webTrafficReports->where('created_at', '>=', now()->subMinutes(15))->count();
            $orderTrafficData['traffic_count_30min'] = $webTrafficReports->where('created_at', '>=', now()->subMinutes(30))->count();
        }

        $responseData[] = $orderTrafficData;
    }
    }
        public function realtimeReports()
    {
        $currentTime = Carbon::now();

        // Calculate the time 24 hours ago
        $thirtyMinutesAgo = $currentTime->subMinutes(30);

        // Fetch data from the WebTrafficReports table for the past 24 hours
        $data = WebTrafficReports::selectRaw('DATE_FORMAT(created_at, "%H:%i") as created_minute, COUNT(*) as count')
                        ->where('created_at', '>=', $thirtyMinutesAgo)
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

    public function dailyReports()
    {
        // Calculate the time 24 hours ago
        $start = Carbon::now()->subDays(30)->startOfDay(); // Start of the 30 days ago
    
        // Querying data grouped by dates
        $data = WebTrafficReports::selectRaw('DATE(created_at) as created_date, COUNT(*) as count')
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

}
