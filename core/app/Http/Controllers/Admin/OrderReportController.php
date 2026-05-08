<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderReportController extends Controller
{
    public function index()
    {
        $pageTitle = 'User Statistics';
        
        // Get banned users with latest ban date
        $bannedUsers = User::banned()
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        
        // Get country-wise user distribution (top 10 countries for pie chart)
        $countryStats = User::select('country', DB::raw('COUNT(*) as user_count'))
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('user_count', 'desc')
            ->limit(10)
            ->get();
        
        // Get ALL countries for the dropdown filter (sorted alphabetically)
        $allCountries = User::select('country', DB::raw('COUNT(*) as user_count'))
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('country', 'asc')
            ->get();

        return view('admin.statistics.index', compact('pageTitle', 'bannedUsers', 'countryStats', 'allCountries'));
    }

    public function userStatistics(Request $request)
    {
        $chartData = [];
        $statusData = [];
        $time = $request->time ?? 'this_year';
        $startDate = null;
        $endDate = null;

        // Handle custom date range
        if ($time == 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($time == 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($time == 'yesterday') {
            $startDate = Carbon::yesterday()->startOfDay();
            $endDate = Carbon::yesterday()->endOfDay();
        } elseif ($time == 'last_7_days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'last_15_days') {
            $startDate = Carbon::now()->subDays(14)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'last_30_days') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($time == 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        } elseif ($time == 'this_year' || $time == 'all') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        // Generate chart data based on date range
        if (in_array($time, ['today', 'yesterday'])) {
            // Hourly breakdown for single day
            for ($hour = 0; $hour < 24; $hour++) {
                $hourStart = $startDate->copy()->hour($hour);
                $hourEnd = $hourStart->copy()->endOfHour();
                
                $statusData['active'] = User::active()
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->count();
                    
                $statusData['banned'] = User::banned()
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->count();
                    
                $statusData['email_unverified'] = User::emailUnverified()
                    ->whereBetween('created_at', [$hourStart, $hourEnd])
                    ->count();
                    
                $chartData[$hour . ':00'] = $statusData;
            }
        } elseif (in_array($time, ['last_7_days', 'last_15_days', 'last_30_days', 'custom'])) {
            // Daily breakdown
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dayStart = $currentDate->copy()->startOfDay();
                $dayEnd = $currentDate->copy()->endOfDay();
                
                $statusData['active'] = User::active()
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();
                    
                $statusData['banned'] = User::banned()
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();
                    
                $statusData['email_unverified'] = User::emailUnverified()
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->count();
                    
                $chartData[$currentDate->format('M d')] = $statusData;
                $currentDate->addDay();
            }
        } elseif (in_array($time, ['this_month', 'last_month'])) {
            // Daily breakdown for month
            foreach (getDaysOfMonth($startDate->month, $startDate->year) as $day) {
                $statusData['active'] = User::active()
                    ->whereYear('created_at', $startDate->year)
                    ->whereMonth('created_at', $startDate->month)
                    ->whereDay('created_at', $day)
                    ->count();
                    
                $statusData['banned'] = User::banned()
                    ->whereYear('created_at', $startDate->year)
                    ->whereMonth('created_at', $startDate->month)
                    ->whereDay('created_at', $day)
                    ->count();
                    
                $statusData['email_unverified'] = User::emailUnverified()
                    ->whereYear('created_at', $startDate->year)
                    ->whereMonth('created_at', $startDate->month)
                    ->whereDay('created_at', $day)
                    ->count();
                    
                $chartData[$day] = $statusData;
            }
        } elseif ($time == 'this_year' || $time == 'all') {
            // Monthly breakdown for year
            foreach ($this->months() as $month) {
                $parsedMonth = Carbon::parse("1 $month");
                
                $statusData['active'] = User::active()
                    ->whereYear('created_at', now())
                    ->whereMonth('created_at', $parsedMonth)
                    ->count();
                    
                $statusData['banned'] = User::banned()
                    ->whereYear('created_at', now())
                    ->whereMonth('created_at', $parsedMonth)
                    ->count();
                    
                $statusData['email_unverified'] = User::emailUnverified()
                    ->whereYear('created_at', now())
                    ->whereMonth('created_at', $parsedMonth)
                    ->count();
                    
                $chartData[$month] = $statusData;
            }
        }

        return response()->json([
            'chart_data' => $chartData,
            'statuses' => ['active', 'banned', 'email_unverified']
        ]);
    }

    private function months()
    {
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[] = Carbon::create()->month($i)->format('F');
        }
        return $months;
    }

    public function countrySignupStatistics(Request $request)
    {
        $time = $request->time ?? 'all';
        $startDate = null;
        $endDate = null;

        // Handle different time filters
        if ($time == 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($time == 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($time == 'yesterday') {
            $startDate = Carbon::yesterday();
            $endDate = Carbon::yesterday()->endOfDay();
        } elseif ($time == 'last_7_days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'last_15_days') {
            $startDate = Carbon::now()->subDays(14)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'last_30_days') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($time == 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        } elseif ($time == 'this_year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }
        // For 'all' time, we don't set start/end dates (will fetch all records)

        // Get top 9 countries
        $topCountries = User::select('country', DB::raw('COUNT(*) as user_count'))
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('user_count', 'desc')
            ->limit(9)
            ->get();

        // Get total users
        $totalUsersQuery = User::query()
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereNotNull('country')
            ->where('country', '!=', '');
        
        $totalUsers = $totalUsersQuery->count();
        $topCountriesTotal = $topCountries->sum('user_count');
        
        // Calculate "Others" if there are more countries
        $countryData = $topCountries->toArray();
        if ($totalUsers > $topCountriesTotal) {
            $countryData[] = [
                'country' => 'Others',
                'user_count' => $totalUsers - $topCountriesTotal
            ];
        }
        
        // Get ALL countries for detailed list
        $allCountriesDetailed = User::select('country', DB::raw('COUNT(*) as user_count'))
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('user_count', 'desc')
            ->get();

        return [
            'country_data' => $countryData,
            'total_users' => $totalUsers,
            'all_countries' => $allCountriesDetailed
        ];
    }

    public function userSignupByCountry(Request $request)
    {
        $country = $request->country ?? 'all';
        $time = $request->time ?? 'this_year';
        $startDate = null;
        $endDate = null;

        // Handle different time filters
        if ($time == 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($time == 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($time == 'yesterday') {
            $startDate = Carbon::yesterday();
            $endDate = Carbon::yesterday()->endOfDay();
        } elseif ($time == 'last_7_days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'last_15_days') {
            $startDate = Carbon::now()->subDays(14)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'last_30_days') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($time == 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        } elseif ($time == 'this_year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }
        
        $months = $this->months();
        $signupData = [];

        if ($time == 'this_year' && !$request->has('start_date')) {
            // Monthly breakdown for current year
            foreach ($months as $month) {
                $parsedMonth = Carbon::parse("1 $month");
                
                $query = User::whereYear('created_at', now())
                    ->whereMonth('created_at', $parsedMonth);
                
                if ($country !== 'all') {
                    $query->where('country', $country);
                }
                
                $signupData[$month] = $query->count();
            }
        } else {
            // Daily or custom range breakdown
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dayStart = $currentDate->copy()->startOfDay();
                $dayEnd = $currentDate->copy()->endOfDay();
                
                $query = User::whereBetween('created_at', [$dayStart, $dayEnd]);
                
                if ($country !== 'all') {
                    $query->where('country', $country);
                }
                
                $signupData[$currentDate->format('M d')] = $query->count();
                $currentDate->addDay();
            }
        }

        return response()->json($signupData);
    }

    public function bannedUsersStatistics(Request $request)
    {
        $time = $request->time ?? 'all';
        $startDate = null;
        $endDate = null;

        // Handle different time filters
        if ($time == 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
        } elseif ($time == 'today') {
            $startDate = Carbon::today();
            $endDate = Carbon::today()->endOfDay();
        } elseif ($time == 'yesterday') {
            $startDate = Carbon::yesterday();
            $endDate = Carbon::yesterday()->endOfDay();
        } elseif ($time == 'last_7_days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'last_15_days') {
            $startDate = Carbon::now()->subDays(14)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'last_30_days') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
        } elseif ($time == 'this_month') {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        } elseif ($time == 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth();
            $endDate = Carbon::now()->subMonth()->endOfMonth();
        } elseif ($time == 'this_year') {
            $startDate = Carbon::now()->startOfYear();
            $endDate = Carbon::now()->endOfYear();
        }

        // Get top 9 countries with banned users
        $topCountries = User::select('country', DB::raw('COUNT(*) as user_count'))
            ->banned()
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('updated_at', [$startDate, $endDate]);
            })
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('user_count', 'desc')
            ->limit(9)
            ->get();

        // Get total banned users
        $totalBannedQuery = User::banned()
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('updated_at', [$startDate, $endDate]);
            })
            ->whereNotNull('country')
            ->where('country', '!=', '');
        
        $totalUsers = $totalBannedQuery->count();
        $topCountriesTotal = $topCountries->sum('user_count');
        
        // Calculate "Others" if there are more countries
        $countryData = $topCountries->toArray();
        if ($totalUsers > $topCountriesTotal) {
            $countryData[] = [
                'country' => 'Others',
                'user_count' => $totalUsers - $topCountriesTotal
            ];
        }
        
        // Get ALL banned user countries for detailed list
        $allCountriesDetailed = User::select('country', DB::raw('COUNT(*) as user_count'))
            ->banned()
            ->when(isset($startDate) && isset($endDate), function ($query) use ($startDate, $endDate) {
                return $query->whereBetween('updated_at', [$startDate, $endDate]);
            })
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->groupBy('country')
            ->orderBy('user_count', 'desc')
            ->get();

        return [
            'country_data' => $countryData,
            'total_users' => $totalUsers,
            'all_countries' => $allCountriesDetailed
        ];
    }

    public function userReportStatistics(Request $request)
    {
        // Smart grouping based on date range length and type
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $diffInDays = $startDate->diffInDays($endDate);
        
        // Check if this is "This Year" or "Last Year" range (start of year to end of year)
        $isThisYear = $startDate->isStartOfYear() && $endDate->isEndOfYear() && $startDate->year == $endDate->year;
        $isLastYear = $startDate->isStartOfYear() && $endDate->isEndOfYear() && $startDate->year == ($endDate->year);
        
        // Check if this is "All Time" (very long range, e.g., from 2020 to now)
        $isAllTime = $diffInDays > 365;
        
        if ($isAllTime) {
            // Yearly grouping for "All Time" (multiple years)
            $format = '%Y';
            $dates = $this->getAllYears($request->start_date, $request->end_date);
        } elseif ($isThisYear || $isLastYear || $diffInDays > 180) {
            // Monthly grouping for "This Year", "Last Year", or 180+ days
            $format = '%M-%Y';
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        } elseif ($diffInDays <= 30) {
            // Daily grouping for 0-30 days
            $format = '%d-%M-%Y';
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } elseif ($diffInDays <= 90) {
            // Weekly grouping for 31-90 days
            $format = 'W%u-%Y'; // Week number - Year
            $dates = $this->getAllWeeks($request->start_date, $request->end_date);
        } else {
            // Monthly grouping for 91-180 days
            $format = '%M-%Y';
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        // Active users (verified email and mobile)
        $activeUsers = User::where('status', Status::USER_ACTIVE)
            ->where('ev', Status::VERIFIED)
            ->where('sv', Status::VERIFIED)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->groupBy('created_on')
            ->get();
        
        // Banned users
        $bannedUsers = User::where('status', 0)
            ->whereDate('updated_at', '>=', $request->start_date)
            ->whereDate('updated_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(updated_at, '{$format}') as created_on")
            ->groupBy('created_on')
            ->get();

        // Unverified users (email unverified)
        $unverifiedUsers = User::where('status', Status::USER_ACTIVE)
            ->where('ev', Status::UNVERIFIED)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('COUNT(*) AS count')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->groupBy('created_on')
            ->get();

        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'activeUser' => getAmount($activeUsers->where('created_on', $date)->first()?->count ?? 0),
                'bannedUser' => getAmount($bannedUsers->where('created_on', $date)->first()?->count ?? 0),
                'unverifiedUser' => getAmount($unverifiedUsers->where('created_on', $date)->first()?->count ?? 0)
            ];
        }

        $data = collect($data);

        $report['created_on'] = $data->pluck('created_on');
        $report['data'] = [
            [
                'name' => 'Active',
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

    private function getAllDates($startDate, $endDate)
    {
        $dates = [];
        $currentDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-F-Y');
            $currentDate->modify('+1 day');
        }

        return $dates;
    }

    private function getAllWeeks($startDate, $endDate)
    {
        $weeks = [];
        $currentDate = Carbon::parse($startDate)->startOfWeek();
        $endDate = Carbon::parse($endDate);

        while ($currentDate <= $endDate) {
            $weekLabel = 'W' . $currentDate->format('W-Y'); // e.g., "W08-2026"
            if (!in_array($weekLabel, $weeks)) {
                $weeks[] = $weekLabel;
            }
            $currentDate->addWeek();
        }

        return $weeks;
    }

    private function getAllMonths($startDate, $endDate)
    {
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

    private function getAllYears($startDate, $endDate)
    {
        if ($endDate > now()) {
            $endDate = now()->format('Y-m-d');
        }

        $startDate = new \DateTime($startDate);
        $endDate = new \DateTime($endDate);

        $years = [];

        while ($startDate <= $endDate) {
            $year = $startDate->format('Y');
            if (!in_array($year, $years)) {
                $years[] = $year;
            }
            $startDate->modify('+1 year');
        }

        return $years;
    }

    public function countryReportStatistics(Request $request)
    {
        // Smart grouping based on date range length
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $diffInDays = $startDate->diffInDays($endDate);
        
        // Get country limit from request (0 = all countries)
        $countryLimit = $request->input('limit', 10);
        
        // Check if specific countries are requested
        $specificCountries = $request->input('countries', []);
        
        // Check if this is "This Year" or "Last Year" range
        $isThisYear = $startDate->isStartOfYear() && $endDate->isEndOfYear() && $startDate->year == $endDate->year;
        $isLastYear = $startDate->isStartOfYear() && $endDate->isEndOfYear() && $startDate->year == ($endDate->year);
        
        // Check if this is "All Time" (very long range)
        $isAllTime = $diffInDays > 365;
        
        if ($isAllTime) {
            // Yearly grouping for "All Time"
            $format = '%Y';
            $dates = $this->getAllYears($request->start_date, $request->end_date);
        } elseif ($isThisYear || $isLastYear || $diffInDays > 180) {
            // Monthly grouping for "This Year", "Last Year", or 180+ days
            $format = '%M-%Y';
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        } elseif ($diffInDays <= 30) {
            // Daily grouping for 0-30 days
            $format = '%d-%M-%Y';
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } elseif ($diffInDays <= 90) {
            // Weekly grouping for 31-90 days
            $format = 'W%u-%Y';
            $dates = $this->getAllWeeks($request->start_date, $request->end_date);
        } else {
            // Monthly grouping for 91-180 days
            $format = '%M-%Y';
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        // Determine which countries to fetch
        if (!empty($specificCountries)) {
            // Use specific countries from request
            $topCountries = collect($specificCountries);
        } else {
            // Get top N countries or all countries by total user signups
            $topCountriesQuery = User::select('country')
                ->whereNotNull('country')
                ->where('country', '!=', '')
                ->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate)
                ->groupBy('country')
                ->orderByRaw('COUNT(*) DESC');
            
            // Apply limit only if not 0 (0 = all countries)
            if ($countryLimit > 0) {
                $topCountriesQuery->limit($countryLimit);
            }
            
            $topCountries = $topCountriesQuery->pluck('country');
        }

        // Build series data for each country
        $seriesData = [];
        
        foreach ($topCountries as $country) {
            $countryUsers = User::where('country', $country)
                ->whereDate('created_at', '>=', $request->start_date)
                ->whereDate('created_at', '<=', $request->end_date)
                ->selectRaw('COUNT(*) AS count')
                ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
                ->groupBy('created_on')
                ->get();

            $countryData = [];
            $countryTotal = 0;
            
            foreach ($dates as $date) {
                $value = getAmount($countryUsers->where('created_on', $date)->first()?->count ?? 0);
                $countryData[] = $value;
                $countryTotal += $value;
            }

            $seriesData[] = [
                'name' => $country,
                'data' => $countryData,
                'total' => $countryTotal
            ];
        }

        // Sort series data by total in descending order (highest to lowest)
        usort($seriesData, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });

        $report['dates'] = $dates;
        $report['data'] = $seriesData;
        $report['total_countries'] = $topCountries->count();

        return response()->json($report);
    }
}
