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
		$info = json_decode(json_encode(getIpInfo()), true);
		$click = new Clicks();
		$click->user_id = $order->user_id;
		$click->order_id = $order->id;
		$click->link = $order->link;
		$click->keyword = $order->keyword;
		$click->clicker_region = @implode(',', $info['country']);
		$click->clicker_country = @implode(',',$info['code']);
		$click->clicker_ip = $maskedIp;
		$click->save();
	}

    }


}
