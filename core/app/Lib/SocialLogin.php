<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\User;
use App\Models\UserLogin;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Socialite;

class SocialLogin
{
    private $provider;
    private $fromApi;

    public function __construct($provider, $fromApi = false)
    {
        $this->provider = $provider;
        $this->fromApi = $fromApi;
        $this->configuration();
    }

    public function redirectDriver()
    {
        return Socialite::driver($this->provider)->redirect();
    }

    private function configuration()
    {
        $provider      = $this->provider;
        $configuration = gs('socialite_credentials')->$provider;
        $provider    = $this->fromApi && $provider == 'linkedin' ? 'linkedin-openid' : $provider;

        Config::set('services.' . $provider, [
            'client_id'     => $configuration->client_id,
            'client_secret' => $configuration->client_secret,
            'redirect'      => route('user.social.login.callback', $provider),
        ]);
    }

    public function login()
    {
        $provider      = $this->provider;
        $provider    = $this->fromApi && $provider == 'linkedin' ? 'linkedin-openid' : $provider;
        $driver     = Socialite::driver($provider);
        if ($this->fromApi) {
            try {
                $user = (object)$driver->userFromToken(request()->token)->user;
            } catch (\Throwable $th) {
                throw new Exception('Something went wrong');
            }
        } else {
            $user = $driver->user();
        }
        if ($provider == 'linkedin-openid') {
            $user->id = $user->sub;
        }

        $userData = User::where('username', $user->id)->first();

        // If no user found with provider_id, fallback to email match
        if (!$userData && isset($user->email)) {
            $userData = User::where('email', $user->email)->first();

            if ($userData) {
                // Update the user with social login details
                $userData->login_by = $this->provider;
                $userData->username = $user->id;
                $userData->save();
            }
        }

        // If still not found, create new
        if (!$userData) {
            if (!gs('registration')) {
                throw new Exception('New account registration is currently disabled');
            }

            $userData = $this->createUser($user, $this->provider);
        }
        if ($this->fromApi) {
            $tokenResult = $userData->createToken('auth_token')->plainTextToken;
            $this->loginLog($userData);
            return [
                'user'         => $userData,
                'access_token' => $tokenResult,
                'token_type'   => 'Bearer',
            ];
        }
        Auth::login($userData);
        $this->loginLog($userData);
        $redirection = Intended::getRedirection();
        return $redirection ? $redirection : to_route('user.bot.home');
    }
	private function createUser($user, $provider)
	{
		$general = gs();
		$password = getTrx(8);

        $firstName = null;
        $lastName = null;

        $referBy = session()->get('reference');
        if ($referBy) {
            $referUser = User::where('username', $referBy)->first();
        } else {
            $referUser = null;
        }

		if (@$user->first_name) {
			$firstName = $user->first_name;
		}
		if (@$user->last_name) {
			$lastName = $user->last_name;
		}

		if (@$user->name) {
			$firstName = $user->name;
		}

		$newUser = new User();
		$newUser->username = $user->id;

		$newUser->email = $user->email;

		$newUser->password = Hash::make($password);
		$newUser->firstname = $firstName;
		$newUser->lastname = $lastName;
        $user->ref_by    = $referUser ? $referUser->id : 0;

		$newUser->status = Status::VERIFIED;
		//$newUser->kv = $general->kv ? Status::NO : Status::YES;
		$newUser->ev = 1;
		$newUser->sv = 1;
		$newUser->ts = Status::DISABLE;
		$newUser->tv = Status::VERIFIED;
		$newUser->login_by = $provider;

		$newUser->save();

		return $newUser;
	}

    private function loginLog($user)
    {
        //Login Log Create
        $ip = getRealIP();
        $exist = UserLogin::where('user_ip', $ip)->first();
        $userLogin = new UserLogin();

        //Check exist or not
        if ($exist) {
            $userLogin->longitude =  $exist->longitude;
            $userLogin->latitude =  $exist->latitude;
            $userLogin->city =  $exist->city;
            $userLogin->country_code = $exist->country_code;
            $userLogin->country =  $exist->country;
        } else {
            $info = json_decode(json_encode(getIpInfo()), true);
            $userLogin->longitude =  @implode(',', $info['long']);
            $userLogin->latitude =  @implode(',', $info['lat']);
            $userLogin->city =  @implode(',', $info['city']);
            $userLogin->country_code = @implode(',', $info['code']);
            $userLogin->country =  @implode(',', $info['country']);
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

        $userAgent = osBrowser();
        $userLogin->user_id = $user->id;
        $userLogin->user_ip =  $ip;

        $userLogin->browser = @$userAgent['browser'];
        $userLogin->os = @$userAgent['os_platform'];
        $userLogin->save();
    }
}
