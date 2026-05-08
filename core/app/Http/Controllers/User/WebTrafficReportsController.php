<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\WebTrafficReports;
use App\Models\Clicks;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;


class WebTrafficReportsController extends Controller
{

    public function hit(Request $request, $id)
    {
	$order = Order::findOrFail($id);
	$order->start_counter = $order->start_counter+1;
	$order->attempt = $order->attempt+1;
	$order->remain = $order->remain-1;
	$order->save();

	$ip = $_SERVER["REMOTE_ADDR"];
	$parts = explode('.', $ip);
	$parts[1] = 'xxx';
	$parts[2] = 'xxx';
	$maskedIp = implode('.', $parts);

	$data = new WebTrafficReports();
	$data->user_id = $order->user_id;
	$data->order_id = $order->id;
	$data->category_id = $order->category_id;
	$data->ip = $ip;
	$data->counter = $order->start_counter;
	$data->save();

	if ($order->category_id == 11 || $order->category_id == 12)
	{
			$cl_ip = $_SERVER["REMOTE_ADDR"];
			$proxy = "167.235.177.27:10005";
			$userAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36";
			$url = "https://ipapi.co/$ip/xml/";
			$context = stream_context_create([
				'http' => [
					'proxy' => "tcp://$proxy",
					'request_fulluri' => true,
					'header' => "User-Agent: $userAgent",
				],
			]);

			$xmlString = @file_get_contents($url, false, $context);
		
			$data2 = [];
			
			if ($xmlString !== false) {
				$xml = simplexml_load_string($xmlString);
			
				$data2['country'] = (string) $xml->region;
				$data2['code'] = (string) $xml->country_code;
			}
			
			$data2['ip'] = $cl_ip;
			$data2['time'] = date('Y-m-d h:i:s A');

			$click = new Clicks();
			$click->user_id = $order->user_id;
			$click->order_id = $order->id;
			$click->link = $order->link;
			$click->keyword = $order->keyword;
			$click->clicker_region = $data2['country'];
			$click->clicker_country = $data2['code'];
			$click->clicker_ip = $maskedIp;
			$click->save();	}

    }

}
