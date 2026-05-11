<?php

namespace App\Http\Controllers\User\Auth;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Intended;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Rules\NotDisposableEmail;
use Illuminate\Support\Str;

class RegisterController extends Controller
{

    use RegistersUsers;

    public function __construct()
    {
        parent::__construct();
    }

    public function showRegistrationForm()
    {
        $pageTitle = "Sign Up!";

        Intended::identifyRoute();
        return view('Template::user.auth.register', compact('pageTitle'));
    }

    protected function validator(array $data)
    {

        $passwordValidation = Password::min(8);

        if (gs('secure_password')) {
            $passwordValidation = $passwordValidation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $agree = 'nullable';
        if (gs('agree')) {
            $agree = 'required';
        }

        $validate = Validator::make($data, [
            'email'     => ['required', 'string', 'email', 'unique:users', new NotDisposableEmail()],
            'password'  => ['required', 'confirmed', $passwordValidation],
            'captcha'   => 'sometimes|required',
            'agree'     => $agree
        ]);

        return $validate;
    }

    public function register(Request $request)
    {
        if (!gs('registration')) {
            $notify[] = ['error', 'Registration not allowed'];
            return back()->withNotify($notify);
        }
        $this->validator($request->all())->validate();

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    protected function create(array $data)
    {
        $referBy = session()->get('reference');
        if ($referBy) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }
		$baseUsername = explode('@', $data['email'])[0];
		$username = $baseUsername;
		$counter = 1;

		while (User::where('username', $username)->exists()) {
			$username = $baseUsername . $counter;
			$counter++;
		}
        //User Create
        $user            = new User();
		$user->username = $username;
        $user->email     = strtolower($data['email']);
        $user->password  = Hash::make($data['password']);
        $user->ref_by    = $referUser ? $referUser->id : 0;
        $user->ev        = gs('ev') ? Status::NO : Status::YES;
        $user->sv        = gs('sv') ? Status::NO : Status::YES;
        $user->ts        = Status::DISABLE;
        $user->tv        = Status::ENABLE;

        $ip        = getRealIP();
    	$registrationCount = UserLogin::where('user_ip', $ip)->count();
    	$maxRegistrations = 2; // Change to your desired limit

    	if ($registrationCount >= $maxRegistrations) {
        	$user->status = Status::BAN; // Or your ban status value
        	$user->ban_reason = 'Access has been temporarily blocked. If you believe this is a mistake, please get in touch with our support to review and unblock your account.';
    	}

        $user->save();

        //Login Log Create

        $exist     = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        if ($exist) {
            $userLogin->longitude    = $exist->longitude;
            $userLogin->latitude     = $exist->latitude;
            $userLogin->city         = $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country      = $exist->country;
        } else {
            $info                    = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude    = @implode(',', $info['long']);
            $userLogin->latitude     = @implode(',', $info['lat']);
            $userLogin->city         = @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country      = @implode(',', $info['country']);
        }
        // Check User-Agent for India
        $userAgentString = request()->header('User-Agent');
        $isFromIndia = false;

        if (strpos($userAgentString, 'IN') !== false || strpos($userAgentString, 'India') !== false) {
            $isFromIndia = true;
        }

        // Determine the country to store in session
        $country = $user->address['country'] ?? $userLogin->country ?? null;
        if ($isFromIndia) {
            $country = 'India';
        }

        // Store country in session
        session(['country' => $country]);

        $userAgent          = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip = $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os      = @$userAgent['os_platform'];
        $userLogin->save();

        return $user;
    }

    public function checkUser(Request $request)
    {
        $exist['data'] = false;
        $exist['type'] = null;


        if ($request->email) {
            $exist['data']  = User::where('email', $request->email)->exists();
            $exist['type']  = 'email';
            $exist['field'] = 'Email';
        }
        if ($request->mobile) {
            $exist['data']  = User::where('mobile', $request->mobile)->where('dial_code', $request->mobile_code)->exists();
            $exist['type']  = 'mobile';
            $exist['field'] = 'Mobile';
        }
        if ($request->username) {
            $exist['data']  = User::where('username', $request->username)->exists();
            $exist['type']  = 'username';
            $exist['field'] = 'Username';
        }
        return response($exist);
    }

    public function registered()
    {
        return to_route('user.web.home');
    }
}
