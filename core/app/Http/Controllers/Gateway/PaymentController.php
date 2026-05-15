<?php

namespace App\Http\Controllers\Gateway;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\Service;
use App\Models\Category;
use App\Models\GatewayCurrency;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use App\Lib\ReferralComission;
use Illuminate\Support\Carbon;
use App\Models\Coupon;

class PaymentController extends Controller
{

    public function seoBuy()
    {
        session()->forget('coupon');
        $user = auth()->user();
	if (blank($user->address)) {
	    return to_route('user.profile.setting');
	}
        $widget['trial'] = $user->mem_type;
        $widget['time'] = \Carbon\Carbon::now();
        $userCountry = auth()->user()->country ?? session('country');

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })
            ->with('method')
            ->when($userCountry === 'India', function ($query) {
                return $query->where('currency', 'INR'); // Show INR only for Indian users
            })
            ->when($userCountry !== 'India', function ($query) {
                return $query->where('currency', '!=', 'INR'); // Exclude INR for non-Indian users
            })
            ->orderBy('id')
            ->get();

        $plan = Service::where('category_id', 17)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();
        $plans = Service::where('category_id', 17)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();

        $errorMessage = "";
        $coupon = Coupon::where('status', 1)->get();
        $pageTitle = 'Purchase - SEO Click Credits';
        return view('Template::user.payment.seo_buy', compact('gatewayCurrency',  'plan', 'plans', 'user', 'coupon', 'pageTitle', 'widget'));

    }

        public function seoTrialBuy()
    {
        session()->forget('coupon');
        $user = auth()->user();
	if (blank($user->address)) {
	    return to_route('user.profile.setting');
	}
        // Check if user->mem_type is NOT 0, then redirect
        if ($user->mem_type != 0) {
            return redirect()->route('user.seo.buy'); // Redirect if condition is met
        }

        $widget['trial'] = $user->mem_type;
        $widget['time'] = \Carbon\Carbon::now();
        $userCountry = auth()->user()->country ?? session('country');
        // $userCountry = session('country') ?? optional(json_decode($user->address))->country;

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })
            ->with('method')
            ->when($userCountry === 'India', function ($query) {
                return $query->where('currency', 'INR'); // Show INR only for Indian users
            })
            ->when($userCountry !== 'India', function ($query) {
                return $query->where('currency', '!=', 'INR'); // Exclude INR for non-Indian users
            })
            ->orderBy('id')
            ->get();

        $plan = Service::where('category_id', 17)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();
        $plans = Service::where('category_id', 17)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();

        $errorMessage = "";
        $coupon = Coupon::where('status', 1)->get();
        $pageTitle = 'Purchase - SEO Click Credits';
        return view('Template::user.payment.seo_trial', compact('gatewayCurrency',  'plan', 'plans', 'user', 'coupon', 'pageTitle', 'widget'));

    }

    public function webBuy()
    {
        session()->forget('coupon');
        $user = auth()->user();
	if (blank($user->address)) {
	    return to_route('user.profile.setting');
	}
        $widget['nano'] = $user->traffic_nano;
        $widget['nano_exp'] = $user->traffic_exp;
        $widget['time'] = \Carbon\Carbon::now();
        $userCountry = auth()->user()->country ?? session('country');
        //$userCountry = session('country') ?? optional(json_decode($user->address))->country;

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })
            ->with('method')
            ->when($userCountry === 'India', function ($query) {
                return $query->where('currency', 'INR'); // Show INR only for Indian users
            })
            ->when($userCountry !== 'India', function ($query) {
                return $query->where('currency', '!=', 'INR'); // Exclude INR for non-Indian users
            })
            ->orderBy('id')
            ->get();

        $plan = Service::where('category_id', 17)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();
        $plans = Service::where('category_id', 17)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();

        $coupon = Coupon::where('status', 1)->get();
        $pageTitle = 'Purchase - Website Traffic Credits';
        return view('Template::user.payment.traffic_buy', compact('gatewayCurrency',  'plan', 'plans', 'user', 'coupon', 'pageTitle', 'widget'));
    }

    public function realisticBuy()
    {
        session()->forget('coupon');
        $user = auth()->user();
	if (blank($user->address)) {
	    return to_route('user.profile.setting');
	}
        $widget['nano'] = $user->traffic_r_nano;
        $widget['nano_exp'] = $user->traffic_r_exp;
        $widget['time'] = \Carbon\Carbon::now();
        //$userCountry = session('country') ?? optional(json_decode($user->address))->country;
        $userCountry = auth()->user()->country ?? session('country');

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })
            ->with('method')
            ->when($userCountry === 'India', function ($query) {
                return $query->where('currency', 'INR'); // Show INR only for Indian users
            })
            ->when($userCountry !== 'India', function ($query) {
                return $query->where('currency', '!=', 'INR'); // Exclude INR for non-Indian users
            })
            ->orderBy('id')
            ->get();

        $plan = Service::where('category_id', 20)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();
        $plans = Service::where('category_id', 20)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();

        $coupon = Coupon::where('status', 1)->get();

        $pageTitle = 'Purchase - Realistic Website Traffic Credits';
        return view('Template::user.payment.traffic_r_buy', compact('gatewayCurrency',  'plan', 'plans', 'user', 'coupon', 'pageTitle', 'widget'));
    }

    public function botBuy()
    {
        session()->forget('coupon');
        $user = auth()->user();
	if (blank($user->address)) {
	    return to_route('user.profile.setting');
	}
        $widget['nano'] = $user->traffic_r_nano;
        $widget['nano_exp'] = $user->traffic_r_exp;
        $widget['time'] = \Carbon\Carbon::now();
        //$userCountry = session('country') ?? optional(json_decode($user->address))->country;
        $userCountry = auth()->user()->country ?? session('country');

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })
            ->with('method')
            ->when($userCountry === 'India', function ($query) {
                return $query->where('currency', 'INR'); // Show INR only for Indian users
            })
            ->when($userCountry !== 'India', function ($query) {
                return $query->where('currency', '!=', 'INR'); // Exclude INR for non-Indian users
            })
            ->orderBy('id')
            ->get();

        $plan = Service::where('category_id', 21)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();
        $plans = Service::where('category_id', 21)->where('original_price', '>', 0)->where('status', Status::ENABLE)->get();

        $coupon = Coupon::where('status', 1)->get();

        $pageTitle = 'Subscribe to Traffic Bot Service';
        return view('Template::user.payment.bot_buy', compact('gatewayCurrency',  'plan', 'plans', 'user', 'coupon', 'pageTitle', 'widget'));
    }

    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->with('method')->orderby('name')->get();
        $pageTitle = 'Deposit Methods';
        return view('Template::user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }
    public function depositInsert2(Request $request)
    {
        dd($request->all());
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'final_amount' => 'required|numeric|gt:0',
            'gateway' => 'required',
            'plans' => 'required',            
            'currency' => 'required',
            'quantity' => 'required|numeric|gt:0',
        ]);


        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        $charge = $gate->fixed_charge + ($request->amount * $gate->percent_charge / 100);
        $vat = $request->vat_fee;
        $discPrice = $request->price * ((100 - $request->discount_percentage)/100);
        $total = ($discPrice + $charge + $vat);
        //$finalAmount = $payable * $gate->rate;
        $finalAmount = $request->final_amount;

        

        $data = new Deposit();
        $data->user_id = $user->id;
	    $data->inv_name = trim("{$user->firstname} {$user->lastname}");
        $data->vat_num = $user->vat;
        $data->company = $user->org;
        $data->address = json_encode([
            'country' => $user->country,
            'address' => $user->address,
            'state'   => $user->state,
            'zip'     => $user->zip,
            'city'    => $user->city,
        ]);
        $data->method_code = $gate->method_code;
        $data->method_currency = strtoupper($gate->currency);
        $data->amount = $discPrice;
        $data->price = $request->price;
        $data->plans = $request->plans;
        $data->discount_amount = $request->discount_amount;
        $data->discount_percentage = $request->discount_percentage;
        $data->credits = $request->credits;
        $plans = [
            '0' => ['SEO Membership Pack - TRIAL', 50],
            '1' => ['SEO Membership Pack - STARTER', null],
            '2' => ['SEO Membership Pack - LITE', 156],
            '3' => ['SEO Membership Pack - BASIC', 390],
            '4' => ['SEO Membership Pack - BRONZE', 1030],
            '5' => ['SEO Membership Pack - SILVER', 2080],
            '6' => ['SEO Membership Pack - GOLD', 4368],
            '7' => ['SEO Membership Pack - PLATINUM', 9360],
            '8' => ['SEO Membership Pack - DIAMOND', 15600],
            '9' => ['SEO Membership Pack - $150', 564],
            '10' => ['SEO Membership Pack - $250', 1128],
            '11' => ['SEO Membership Pack - $500', 2822],
            '12' => ['SEO Membership Pack - $1000', 7058],
            '13' => ['SEO Membership Pack - $2500', 19764],
            '14' => ['SEO Membership Pack - $5000', 42354],
            '15' => ['Website Traffic - MINI', $request->quantity],
            '16' => ['Website Traffic - SMALL', $request->quantity],
            '17' => ['Website Traffic - MEDIUM', $request->quantity],
            '18' => ['Website Traffic - LARGE', $request->quantity],
            '19' => ['Website Traffic - ULTIMATE', $request->quantity],
            '20' => ['Unblocked Traffic - TRIAL', $request->quantity],
            '21' => ['Unblocked Traffic - 25K', $request->quantity],
            '22' => ['Unblocked Traffic - 50K', $request->quantity],
            '23' => ['Unblocked Traffic - 100K', $request->quantity],
            '24' => ['Unblocked Traffic - 250K', $request->quantity],
            '25' => ['Unblocked Traffic - 500K', $request->quantity],
            '26' => ['Unblocked Traffic - 1000K', $request->quantity],
            '27' => ['Premium Traffic - TRIAL', $request->quantity],
            '28' => ['Premium Traffic - 10K', $request->quantity],
            '29' => ['Premium Traffic - 25K', $request->quantity],
            '30' => ['Premium Traffic - 100K', $request->quantity],
            '31' => ['Premium Traffic - 250K', $request->quantity],
            '32' => ['Realistic Traffic - MINI', $request->quantity],
            '33' => ['Realistic Traffic - SMALL', $request->quantity],
            '34' => ['Realistic Traffic - MEDIUM', $request->quantity],
            '35' => ['Realistic Traffic - LARGE', $request->quantity],
            '36' => ['Realistic Traffic - ULTIMATE', $request->quantity],
            '41' => ['Traffic Bot - LITE', $request->quantity],
            '42' => ['Traffic Bot - BASIC', $request->quantity],
            '43' => ['Traffic Bot - BRONZE', $request->quantity],
            '44' => ['Traffic Bot - SILVER', $request->quantity],
            '45' => ['Traffic Bot - GOLD', $request->quantity],
            '46' => ['Traffic Bot - PLATINUM', $request->quantity],
            '47' => ['Traffic Bot - DIAMOND', $request->quantity],
        ];

        // Assign values if the plan exists in the array
        if (isset($plans[$request->plans])) {
            [$data->plan_name, $data->credits] = $plans[$request->plans];
        }
        
        $data->charge = $request->processing_fee;
        $data->vat  = $vat;
        $data->rate = $gate->rate;
        $data->final_amo = $finalAmount;
        $data->total_price = $total;
        $data->btc_amo = 0;
        $data->btc_wallet = "";
        $data->trx = getTrx();

        // Tag SparkProxy ref and domain if this payment originated from SparkProxy
        if (session()->has('sparkproxy_payment')) {
            $spSession = session('sparkproxy_payment');
            if (!empty($spSession['ref'])) {
                $data->sparkproxy_ref = $spSession['ref'];
            }
            if (!empty($spSession['domain'])) {
                $data->domain = $spSession['domain'];
            }
        }

        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.billing.confirm');
    }

    /**
     * Deposit insert for payments originating from SparkProxy.
     *
     * Amount is taken from the signed session token (not user input) so the
     * user can only choose which gateway to pay through — they cannot alter
     * the amount. After payment confirms, the webhook fires back to SparkProxy
     * which activates the plan on that side.
     */
    public function depositInsertSparkProxy(Request $request)
    {
        $request->validate([
            'gateway'  => 'required|integer',
            'currency' => 'required|string',
        ]);

        $spSession = session('sparkproxy_payment');
        if (empty($spSession['ref']) || empty($spSession['amount'])) {
            $notify[] = ['error', 'Payment session expired. Please return to SparkProxy and try again.'];
            return back()->withNotify($notify);
        }
        // Note: token expiry is intentionally NOT re-checked here.
        // It was validated at page load (SparkProxyPaymentController) to prevent link replay.
        // Once the user has an authenticated session, they should be able to complete payment freely.

        $user = auth()->user();
        $gate = GatewayCurrency::whereHas('method', function ($q) {
            $q->where('status', Status::ENABLE);
        })->where('method_code', $request->gateway)
          ->where('currency', $request->currency)
          ->first();

        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        // Authoritative base amount from the signed token (in site currency)
        $baseAmount = (float) $spSession['amount'];

        // Calculate gateway charges on top of the base amount
        $charge      = $gate->fixed_charge + ($baseAmount * $gate->percent_charge / 100);
        $vatCharge   = ($baseAmount + $charge) * ($gate->vat_charge ?? 0) / 100;
        $finalAmount = ($baseAmount + $charge + $vatCharge) * $gate->rate;

        if ($finalAmount < $gate->min_amount || $finalAmount > $gate->max_amount) {
            $notify[] = ['error', "Amount must be between {$gate->min_amount} and {$gate->max_amount} {$gate->currency}"];
            return back()->withNotify($notify);
        }

        $data                      = new Deposit();
        $data->user_id             = $user->id;
        $data->inv_name            = trim("{$user->firstname} {$user->lastname}") ?: ($spSession['sp_user_email'] ?? $user->email);
        $data->method_code         = $gate->method_code;
        $data->method_currency     = strtoupper($gate->currency);
        $data->amount              = $baseAmount;
        $data->price               = $baseAmount;
        $data->charge              = $charge;
        $data->vat                 = $vatCharge;
        $data->discount_amount     = 0;
        $data->discount_percentage = 0;
        $data->rate                = $gate->rate;
        $data->final_amo           = $finalAmount;
        $data->total_price         = $baseAmount + $charge + $vatCharge;
        $data->btc_amo             = 0;
        $data->btc_wallet          = '';
        $data->trx                 = getTrx();
        $data->plans               = 'sparkproxy';
        $data->plan_name           = $spSession['plan_name'] ?? 'SparkProxy Plan';
        $data->credits             = 0;
        $data->sparkproxy_ref      = $spSession['ref'];
        $data->domain              = 'sparkproxy';
        $data->sp_user_email       = $spSession['sp_user_email'] ?? null;
        $data->webhook_url         = $spSession['webhook_url']   ?? null;
        // Reuse existing billing columns for SparkProxy user's details
        $data->company             = $spSession['org']     ?? null;
        $data->vat_num             = $spSession['vat']     ?? null;
        $data->address             = json_encode([
            'country' => $spSession['country']      ?? '',
            'address' => $spSession['address']      ?? '',
            'state'   => $spSession['state']        ?? '',
            'zip'     => $spSession['zip']          ?? '',
            'city'    => $spSession['city']         ?? '',
        ]);

        $data->save();
        session()->put('Track', $data->trx);
        return to_route('user.billing.confirm');
    }


    public function appDepositConfirm($hash)
    {
        try {
            $id = decrypt($hash);
        } catch (\Exception $ex) {
            abort(404);
        }
        $data = Deposit::where('id', $id)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->firstOrFail();
        $user = User::findOrFail($data->user_id);
        auth()->login($user);
        session()->put('Track', $data->trx);
        return to_route('user.billing.confirm');
    }


    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.billing.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return back()->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for Stripe V3
        if (@$data->session) {
            $deposit->btc_wallet = $data->session->id;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view("Template::$data->view", compact('data', 'pageTitle', 'deposit'));
    }


    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == Status::PAYMENT_INITIATE || $deposit->status == Status::PAYMENT_PENDING) {
            $deposit->status = Status::PAYMENT_SUCCESS;
            $deposit->save();
            }
            PaymentController::planUpdate($deposit);

            // Fire SparkProxy webhook if this deposit originated from SparkProxy
            if (!empty($deposit->sparkproxy_ref)) {
                \App\Services\SparkProxyWebhookService::dispatch($deposit);
            }
    }

    public static function planUpdate($deposit)
    {

            $user = User::find($deposit->user_id);
            if ($deposit->plans == '0') {
                $user->mem_type = 1;
                $user->trial_exp = 0;
                $user->mem_alert = 0;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(7);
                $user->mem_credit += $deposit->credits;
                notify($user, 'TRIAL_ACTIVE');
            } elseif ($deposit->plans == '1') {
                $user->mem_type = 2;
                $user->mem_status = 1;
                $user->trial_exp = 1;
                $user->mem_alert = 0;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(30);
                $user->mem_credit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '2') {
                $user->mem_type = 3;
                $user->mem_status = 1;
                $user->trial_exp = 1;
                $user->mem_alert = 0;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(30);
                $user->mem_credit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '3') {
                $user->mem_type = 4;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->trial_exp = 1;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(30);
                $user->mem_credit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '4') {
                $user->mem_type = 5;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->trial_exp = 1;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(30);
                $user->mem_credit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '5') {
                $user->mem_type = 6;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->trial_exp = 1;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(30);
                $user->mem_credit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '6') {
                $user->mem_type = 7;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->trial_exp = 1;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(30);
                $user->mem_credit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '7') {
                $user->mem_type = 8;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->trial_exp = 1;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(30);
                $user->mem_credit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '8') {
                $user->mem_type = 9;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->trial_exp = 1;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(30);
                $user->mem_credit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '9') {
                $user->mem_type = 10;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(365);
                $user->seocredit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '10') {
                $user->mem_type = 11;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->trial_exp = 1;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(365);
                $user->seocredit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '11') {
                $user->mem_type = 12;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(365);
                $user->seocredit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '12') {
                $user->mem_type = 13;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(365);
                $user->seocredit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '13') {
                $user->mem_type = 14;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(365);
                $user->seocredit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '14') {
                $user->mem_type = 15;
                $user->mem_status = 1;
                $user->mem_alert = 0;
                $user->mem_exp = \Carbon\Carbon::now()->addDays(365);
                $user->seocredit += $deposit->credits;
                notify($user, 'MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addDays(30),
                    'credits' => $deposit->credits,
                ]);
            } elseif ($deposit->plans == '15') {
                $user->traffic_mini += $deposit->credits;
            } elseif ($deposit->plans == '16') {
                $user->traffic_small += $deposit->credits;
            } elseif ($deposit->plans == '17') {
                $user->traffic_medium += $deposit->credits;
            } elseif ($deposit->plans == '18') {
                $user->traffic_large += $deposit->credits;
            } elseif ($deposit->plans == '19') {
                $user->traffic_ultimate += $deposit->credits;
            } elseif ($deposit->plans == '20') {
                $user->ad_credit += $deposit->credits;
                $user->ad_trial = 1;
            } elseif ($deposit->plans == '21') {
                $user->ad_credit += $deposit->credits;
            } elseif ($deposit->plans == '22') {
                $user->ad_credit += $deposit->credits;
            } elseif ($deposit->plans == '23') {
                $user->ad_credit += $deposit->credits;
            } elseif ($deposit->plans == '24') {
                $user->ad_credit += $deposit->credits;
            } elseif ($deposit->plans == '25') {
                $user->ad_credit += $deposit->credits;
            } elseif ($deposit->plans == '26') {
                $user->ad_credit += $deposit->credits;
            } elseif ($deposit->plans == '27') {
                $user->premium_credit += $deposit->credits;
                $user->premium_trial = 1;
            } elseif ($deposit->plans == '28') {
                $user->premium_credit += $deposit->credits;
            } elseif ($deposit->plans == '29') {
                $user->premium_credit += $deposit->credits;
            } elseif ($deposit->plans == '30') {
                $user->premium_credit += $deposit->credits;
            } elseif ($deposit->plans == '31') {
                $user->premium_credit += $deposit->credits;
            } elseif ($deposit->plans == '32') {
                $user->traffic_r_mini += $deposit->credits;
            } elseif ($deposit->plans == '33') {
                $user->traffic_r_small += $deposit->credits;
            } elseif ($deposit->plans == '34') {
                $user->traffic_r_medium += $deposit->credits;
            } elseif ($deposit->plans == '35') {
                $user->traffic_r_large += $deposit->credits;
            } elseif ($deposit->plans == '36') {
                $user->traffic_r_ultimate += $deposit->credits;
            } elseif ($deposit->plans == '41') {
                $user->bot_plan = 122;
                $user->bot_credit = 10;
                $user->bot_status = 1;
                $user->bot_alert = 0;
                $user->bot_exp = \Carbon\Carbon::now()->addMonths($deposit->credits);
                notify($user, 'BOT_MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addMonths($deposit->credits),
                    'credits' => 10,
                ]);
            } elseif ($deposit->plans == '42') {
                $user->bot_plan = 123;
                $user->bot_credit = 25;
                $user->bot_status = 1;
                $user->bot_alert = 0;
                $user->bot_exp = \Carbon\Carbon::now()->addMonths($deposit->credits);
                notify($user, 'BOT_MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addMonths($deposit->credits),
                    'credits' => 25,
                ]);
            } elseif ($deposit->plans == '43') {
                $user->bot_plan = 124;
                $user->bot_credit = 50;
                $user->bot_status = 1;
                $user->bot_alert = 0;
                $user->bot_exp = \Carbon\Carbon::now()->addMonths($deposit->credits);
                notify($user, 'BOT_MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addMonths($deposit->credits),
                    'credits' => 50,
                ]);
            } elseif ($deposit->plans == '44') {
                $user->bot_plan = 125;
                $user->bot_credit = 100;
                $user->bot_status = 1;
                $user->bot_alert = 0;
                $user->bot_exp = \Carbon\Carbon::now()->addMonths($deposit->credits);
                notify($user, 'BOT_MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addMonths($deposit->credits),
                    'credits' => 100,
                ]);
            } elseif ($deposit->plans == '45') {
                $user->bot_plan = 126;
                $user->bot_credit = 250;
                $user->bot_status = 1;
                $user->bot_alert = 0;
                $user->bot_exp = \Carbon\Carbon::now()->addMonths($deposit->credits);
                notify($user, 'BOT_MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addMonths($deposit->credits),
                    'credits' => 250,
                ]);
            } elseif ($deposit->plans == '46') {
                $user->bot_plan = 127;
                $user->bot_credit = 500;
                $user->bot_status = 1;
                $user->bot_alert = 0;
                $user->bot_exp = \Carbon\Carbon::now()->addMonths($deposit->credits);
                notify($user, 'BOT_MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addMonths($deposit->credits),
                    'credits' => 500,
                ]);
            } elseif ($deposit->plans == '47') {
                $user->bot_plan = 128;
                $user->bot_credit = 1000;
                $user->bot_status = 1;
                $user->bot_alert = 0;
                $user->bot_exp = \Carbon\Carbon::now()->addMonths($deposit->credits);
                notify($user, 'BOT_MEM_ACTIVE', [
                    'plan' => $deposit->plan_name,
                    'exp' => \Carbon\Carbon::now()->addMonths($deposit->credits),
                    'credits' => 1000,
                ]);
            }

            $user->save();

            $methodName = $deposit->methodName();

            $transaction = new Transaction();
            $transaction->user_id = $deposit->user_id;
            $transaction->credits = $deposit->credits;
            $transaction->trx_type = '+';
            $transaction->details = $deposit->plan_name . ' Credits Purchased via ' . $deposit->gatewayCurrency()->name;
            $transaction->trx = $deposit->trx;
            $transaction->amount = $deposit->amount;
            $transaction->remark = 'PURCHASED';
            $transaction->save();

            $adminNotification = new AdminNotification();
            $adminNotification->user_id = $user->id;
            $adminNotification->title = 'Purchase successful via ' . $deposit->gatewayCurrency()->name;
            $adminNotification->click_url = urlPath('admin.deposit.successful');
            $adminNotification->save();

            // if (!$isManual) {
            //     $adminNotification = new AdminNotification();
            //     $adminNotification->user_id = $user->id;
            //     $adminNotification->title = 'Deposit successful via ' . $methodName;
            //     $adminNotification->click_url = urlPath('admin.deposit.successful');
            //     $adminNotification->save();
            // }

            // notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'PAYMENT_COMPLETE', [
            //     'method_name' => $deposit->gatewayCurrency()->name,
            //     'method_currency' => $deposit->method_currency,
            //     'method_amount' => showAmount($deposit->final_amo),
            //     'amount' => showAmount($deposit->amount),
            //     'charge' => showAmount($deposit->charge),
            //     'rate' => showAmount($deposit->rate),
            //     'trx' => $deposit->trx,
            //     'plan' => $deposit->plan_name,
            //     'date' => $deposit->created_at->todatestring(),
            //     'firstname' => $user->firstname . ' ' . $user->lastname,
            //     'email' => ($user->email),
            //     'address' => ($user->address->address),
            //     'city' => ($user->address->city),
            //     'state' => ($user->address->state),
            //     'country' => ($user->address->country),
            //     'zip' => ($user->address->zip),
            //     'mob' => ($user->mobile)
            // ]);

            $general = gs();
            if ($general->deposit_commission) {
                ReferralComission::levelCommission($user, $deposit->amount, 'deposit_commission', $deposit->trx, $general);
            }
        }
    

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        if ($data->method_code > 999) {
            $pageTitle = 'Confirm Deposit';
            $method = $data->gatewayCurrency();
            $gateway = $method->method;
            return view('Template::user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data = Deposit::with('gateway')->where('status', Status::PAYMENT_INITIATE)->where('trx', $track)->first();
        abort_if(!$data, 404);
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway = $gatewayCurrency->method;
        $formData = $gateway->form->form_data;

        $formProcessor = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);


        $data->detail = $userData;
        $data->status = Status::PAYMENT_PENDING;
        $data->save();


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $data->user->id;
        $adminNotification->title = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name' => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount' => showAmount($data->final_amount, currencyFormat: false),
            'amount' => showAmount($data->amount, currencyFormat: false),
            'charge' => showAmount($data->charge, currencyFormat: false),
            'rate' => showAmount($data->rate, currencyFormat: false),
            'trx' => $data->trx
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.billing.history')->withNotify($notify);
    }

    public function applyCoupon(Request $request)
    {
        // Validate required fields
        $request->validate([
            'coupon_code' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|integer'
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)->first();
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid coupon code.']);
        }

        // Get the category from the database
        $category = Category::find($coupon->category_id);

        // If category is not found, return error
        if (!$category) {
            return response()->json(['success' => false, 'message' => 'Coupon does not exist.']);
        }

        // Check if the coupon is valid for the given category
        if ($coupon->category_id != $request->category_id) {
            return response()->json([
                'success' => false,
                'message' => "This coupon is only valid for  " . $category->name
            ]);
        }

        $originalPrice = $request->price;
        $discountAmount = $originalPrice * $coupon->discount / 100;
        $newPrice = $originalPrice - $discountAmount;
        $roundedNewPrice = round($newPrice, 2); // Round to two decimal places
        $roundedDiscountAmount = round($discountAmount, 2); // Round the discount amount to two decimal places

        session(['coupon' => [
            'code' => $coupon->code,
            'discount' => $coupon->discount,
            'discount_amount' => $roundedDiscountAmount,
            'category_id' => $coupon->category_id
        ]]);

        return response()->json([
            'success' => true,
            'new_price' => $roundedNewPrice,
            'discount_percentage' => (int) $coupon->discount,
            'discount_amount' => $roundedDiscountAmount,
            'total_price' => $roundedNewPrice,
            'message' => 'Coupon applied successfully!'
        ]);
    }

    public function removeCoupon(Request $request)
    {
        // Reset or remove the applied coupon details from session or wherever it's stored
        session()->forget('coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully.'
        ]);
    }
}
