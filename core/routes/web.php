<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function(){
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});


//Cron Controller
// Individual routes (kept for backward compatibility - use processAllOrders instead)
Route::get('cron', 'CronController@placeOrderToApi')->name('cron');
// Route::get('updateOrder', 'CronController@updateOrderToApi')->name('update');
// Route::get('stopOrder', 'CronController@stopOrderToApi')->name('stop');
// Route::get('resumeOrder', 'CronController@resumeOrderToApi')->name('resume');
Route::get('done', 'CronController@completed')->name('completed');

// Unified Cron Route - Processes ALL operations automatically (add, update, stop, resume)
Route::get('processAllOrders', 'CronController@processOrderApiOperations')->name('processAllOrders');

Route::get('expiryCheck', 'CronController@expiryCheck')->name('expiryCheck');
Route::get('currency', 'CronController@updateCurrencyRates')->name('currency');
Route::get('hit/{id}', 'WebTrafficReportsController@hit')->name('hit');
Route::get('update', 'WebTrafficReportsController@countCheck')->name('countCheck');
Route::get('delete', 'WebTrafficReportsController@delete')->name('delete');
Route::get('expired', 'WebTrafficReportsController@expired')->name('expired');
Route::get('lb', 'WebTrafficReportsController@reportAPI')->name('reportAPI');
Route::get('all', 'WebTrafficReportsController@reportAPI2')->name('reportAPI2');
Route::get('st', 'WebTrafficReportsController@statusApi')->name('statusApi');
Route::get('ga/{id}', 'ApiController@checkGA')->name('ga');
Route::get('support', 'CronController@support')->name('support');
Route::get('tag', 'CronController@tagCheck')->name('tag');
Route::get('/time', function () {
    return response()->json(['time' => now()->format('d M Y H:i')]);
});
Route::get('inv', 'CronController@invNumber')->name('inv');

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::get('app/deposit/confirm/{hash}', 'Gateway\PaymentController@appDepositConfirm')->name('deposit.app.confirm');

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');

	// Help/Resources routes (matching Support Board URL rewrite patterns)
    Route::get('/resources', 'help')->name('help');
	
    //Route::get('policy/{slug}', 'policyPages')->name('policy.pages');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    //Route::get('blog/{slug}', 'blogDetails')->name('blog.details');
    //Route::get('services', 'services')->name('services');
    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('updating','maintenance')->withoutMiddleware('maintenance')->name('maintenance');
    Route::post('subscribe', 'subscribe')->name('subscribe');
    //Route::get('/api', 'apiDocumentation')->name('api.documentation');
    //Route::get('/blog', 'blog')->name('blog');
    Route::get('/{slug}', 'pages')->name('pages');
    Route::get('/', 'index')->name('home');
});
