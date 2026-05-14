<?php

use Illuminate\Support\Facades\Route;

Route::namespace('User\Auth')->name('user.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::controller('LoginController')->group(function () {
            Route::get('/login', 'showLoginForm')->name('login');
            Route::post('/login', 'login');
            Route::get('logout', 'logout')->middleware('auth')->withoutMiddleware('guest')->name('logout');
        });

        Route::controller('RegisterController')->group(function () {
            Route::get('signup', 'showRegistrationForm')->name('register');
            Route::post('signup', 'register');
            Route::post('check-user', 'checkUser')->name('checkUser')->withoutMiddleware('guest');
        });

        Route::controller('ForgotPasswordController')->prefix('password')->name('password.')->group(function () {
            Route::get('reset', 'showLinkRequestForm')->name('request');
            Route::post('email', 'sendResetCodeEmail')->name('email');
            Route::get('code-verify', 'codeVerify')->name('code.verify');
            Route::post('verify-code', 'verifyCode')->name('verify.code');
        });

        Route::controller('ResetPasswordController')->group(function () {
            Route::post('password/reset', 'reset')->name('password.update');
            Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
        });

        Route::controller('SocialiteController')->group(function () {
            Route::get('social-login/{provider}', 'socialLogin')->name('social.login');
            Route::get('social-login/callback/{provider}', 'callback')->name('social.login.callback');
        });
    });
});

// SparkProxy payment page — handles its own SSO login via token, must be outside auth middleware
Route::get('sparkproxy/pay', 'User\SparkProxyPaymentController@showPaymentPage')->name('user.sparkproxy.pay');
Route::post('sparkproxy/pay/insert', 'Gateway\PaymentController@depositInsertSparkProxy')
    ->middleware(['auth', 'throttle:30,1'])
    ->name('user.sparkproxy.pay.insert');

Route::middleware('auth')->name('user.')->group(function () {
    // SparkProxy SSO — redirect logged-in Sparkcliks user to SparkProxy with a signed token
    Route::get('sparkproxy', 'User\SsoController@redirectToSparkProxy')->name('sparkproxy.sso');

    Route::get('profile/update', 'User\UserController@userData')->name('data');
    Route::post('profile/update/submit', 'User\UserController@userDataSubmit')->name('data.submit');

    //authorization
    Route::middleware('registration.complete')->namespace('User')->controller('AuthorizationController')->group(function () {
        Route::get('authorization', 'authorizeForm')->name('authorization');
        Route::get('resend-verify/{type}', 'sendVerifyCode')->name('send.verify.code');
        Route::post('verify-email', 'emailVerification')->name('verify.email');
        Route::post('verify-mobile', 'mobileVerification')->name('verify.mobile');
        Route::post('verify-g2fa', 'g2faVerification')->name('2fa.verify');
    });

    Route::middleware(['check.status', 'registration.complete'])->group(function () {
        Route::namespace('User')->group(function () {
            Route::controller('UserController')->group(function () {
                Route::get('serach-console/', 'seoHome')->name('home');
                Route::get('serach-console/', 'seoHome');
                Route::get('serach-console/dashboard', 'seoHome')->name('seo.home');
                Route::get('website-traffic/', 'webHome');
                Route::get('website-traffic/dashboard', 'webHome')->name('web.home');
                Route::get('realistic-traffic/', 'realisticHome');
                Route::get('realistic-traffic/dashboard', 'realisticHome')->name('realistic.home');
                Route::get('download-attachments/{file_hash}', 'downloadAttachment')->name('download.attachment');
                Route::get('referrals', 'referrals')->name('referrals');
                Route::get('bot/', 'botHome');
                Route::get('bot/dashboard', 'botHome')->name('bot.home');
                Route::post('bot/ack', 'botAck')->name('bot.accept');

                //2FA
                Route::get('twofactor', 'show2faForm')->name('twofactor');
                Route::post('twofactor/enable', 'create2fa')->name('twofactor.enable');
                Route::post('twofactor/disable', 'disable2fa')->name('twofactor.disable');

                //Report
                Route::any('payments', 'depositHistory')->name('billing.history');
                Route::get('invoice/{id}', 'viewInvoice')->name('billing.invoice');
                Route::get('download/{id}', 'downloadInvoice')->name('billing.download');
                Route::get('transactions', 'transactions')->name('billing.transactions');

                //RealTime Data
                Route::get('bot/realtime', 'botRealtime')->name('bot.realtime');
                Route::get('website-traffic/realtime', 'webRealtime')->name('web.realtime');
                Route::get('realistic-traffic/realtime', 'realisticRealtime')->name('realistic.realtime');
                Route::get('website-traffic/realtime/{id}', 'webRealtimeCampaign')->name('web_campaign.realtime');
                Route::get('realistic-traffic/realtime/{id}', 'realisticRealtimeCampaign')->name('realistic_campaign.realtime');

                //Dashboard Data
                Route::get('website-traffic/chart', 'webChart')->name('web.chart');
                Route::get('bot/chart', 'botChart')->name('bot.chart');
                Route::get('realistic-traffic/chart', 'realisticChart')->name('realistic.chart');
                Route::get('website-traffic/chart/{id}', 'webChartCampaign')->name('web_campaign.chart');
                Route::get('realistic-traffic/chart/{id}', 'realisticChartCampaign')->name('realistic_campaign.chart');  
                Route::get('serach-console/chart/{id}', 'seoChartCampaign')->name('seo_campaign.chart');                       
                
                //Create
                Route::get('search-console/new-campaign', 'seoNew')->name('seo.new');
                Route::get('website-traffic/new-campaign', 'webNew')->name('web.new');
                Route::get('realistic-traffic/new-campaign', 'realisticNew')->name('realistic.new');
                Route::get('bot/new-campaign', 'botNew')->name('bot.new');
                Route::get('bot/logs/{id?}', 'botLogs')->name('bot.logs');
                Route::post('bot/logs/fetch', 'botLogsFetch')->name('bot.logs.fetch');
                Route::post('bot/logs/token', 'botLogsToken')->name('bot.logs.token');
                Route::post('bot/logs/download/{id}', 'botLogsDownload')->name('bot.logs.download');
                Route::get('premium-traffic/new-campaign', 'premiumNew')->name('premium.new');
                Route::get('unblocked-traffic/new-campaign', 'adNew')->name('ad.new');

                Route::get('order', 'order')->name('order');
                Route::post('add-device-token', 'addDeviceToken')->name('add.device.token');
    		Route::get('billing/update', 'updateAdd')->name('profile.data');
    		Route::post('billing/update/submit', 'updateAddSubmit')->name('profile.data.submit');

            });

            Route::controller('RefillController')->prefix('refill')->name('refill.')->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('store/{id}', 'store')->name('store');
            });

            //Profile setting
            Route::controller('ProfileController')->group(function () {
                Route::get('profile-setting', 'profile')->name('profile.setting');
                Route::post('profile-setting', 'submitProfile');
                Route::get('change-password', 'changePassword')->name('change.password');
                Route::post('change-password', 'submitPassword');
                Route::post('delete', 'deleteProfile')->name('profile.delete');
            });

            //order Controller
            Route::controller('OrderController')->name('orders.')->prefix('order')->group(function () {
                Route::get('history', 'history')->name('history');
                Route::get('pending', 'pending')->name('pending');
                Route::get('processing', 'processing')->name('processing');
                Route::get('completed', 'completed')->name('completed');
                Route::get('cancelled', 'cancelled')->name('cancelled');
                Route::get('refunded', 'refunded')->name('refunded');
                Route::get('/mass', 'massOrder')->name('mass');
                Route::post('/store-mass', 'massOrderStore')->name('mass.store');
                Route::get('overview/{id?}', 'orderOverview')->name('overview');
                Route::post('order/{serviceId?}', 'order')->name('create');
                Route::get('details/{id}', 'orderDetails')->name('details');
            });

            //SEO order Controller
            Route::controller('OrderController')->name('seo.')->prefix('serach-console')->group(
                function () {
                    Route::post('/{serviceId}', 'seoOrder')->name('create');
                    Route::post('campaign/cancel/{id}', 'seoCancel')->name('cancel');
                    Route::post('campaign/update/{id}', 'seoUpdate')->name('update');
                    Route::get('campaign/history', 'seoHistory')->name('history');
                    Route::get('campaign/error', 'seoPending')->name('pending');
                    Route::get('campaign/active', 'seoProcessing')->name('processing');
                    Route::get('campaign/completed', 'seoCompleted')->name('completed');
                    Route::get('campaign/denied', 'seoDenied')->name('denied');
                    Route::get('campaign/cancelled', 'seoCancelled')->name('cancelled');
                    Route::get('campaign/details/{id}', 'seoDetails')->name('details');
                    Route::get('click/history', 'seoClicks')->name('clicks');
                    Route::get('campaign/reports/{id}', 'seoReports')->name('reports');

                    //Route::get('/mass', 'massOrder')->name('mass');
                    //Route::post('/store-mass', 'massOrderStore')->name('mass.store');
                }
            );     
                        //Website Traffic order Controller
                        Route::controller('OrderController')->name('web.')->prefix('website-traffic')->group(
                             function () {
                                Route::post('/', 'webNano')->name('nano');
                                Route::post('/{serviceId}', 'webOrder')->name('create');
                                Route::get('campaign/history', 'webHistory')->name('history');
                                Route::get('campaign/error', 'webPending')->name('pending');
                                Route::post('campaign/update/{id}', 'webUpdate')->name('update');
                                Route::get('campaign/active', 'webProcessing')->name('processing');
                                Route::get('campaign/completed', 'webCompleted')->name('completed');
                                Route::get('campaign/paused', 'webPaused')->name('paused');
                                Route::get('campaign/details/{id}', 'webDetails')->name('details');
                                Route::post('campaign/renew/{id}', 'webRenew')->name('renew');
                                Route::post('campaign/resume/{id}', 'webResume')->name('resume');
                                Route::post('campaign/pause/{id}', 'webPause')->name('pause');
                                Route::get('campaign/denied', 'webDenied')->name('denied');
                                Route::get('campaign/reports/{id}', 'webReports')->name('reports');
                                //Route::get('/mass', 'massOrder')->name('mass');
                                //Route::post('/store-mass', 'massOrderStore')->name('mass.store');
                            }
                        );

                        //Realisitc Website Traffic order Controller
                        Route::controller('OrderController')->name('realistic.')->prefix('realistic-traffic')->group(
                             function () {
                                Route::post('/{serviceId}', 'realisticOrder')->name('create');
                                Route::get('campaign/history', 'realisticHistory')->name('history');
                                Route::get('campaign/error', 'realisticPending')->name('pending');
                                Route::post('campaign/update/{id}', 'realisticUpdate')->name('update');
                                Route::get('campaign/active', 'realisticProcessing')->name('processing');
                                Route::get('campaign/completed', 'realisticCompleted')->name('completed');
                                Route::get('campaign/paused', 'realisticPaused')->name('paused');
                                Route::get('campaign/details/{id}', 'realisticDetails')->name('details');
                                Route::post('campaign/renew/{id}', 'realisticRenew')->name('renew');
                                Route::post('campaign/resume/{id}', 'realisticResume')->name('resume');
                                Route::post('campaign/pause/{id}', 'realisticPause')->name('pause');
                                Route::get('campaign/denied', 'realisticDenied')->name('denied');
                                Route::get('campaign/reports/{id}', 'realisticReports')->name('reports');
                                Route::post('/', 'realisticNano')->name('nano');
                            }
                        );
                        //Bot order Controller
                        Route::controller('OrderController')->name('bot.')->prefix('bot')->group(
                             function () {
                                Route::post('/{serviceId}', 'botStore')->name('create');
                                Route::post('save', 'botStore')->name('store');
                                Route::get('campaign/history', 'botHistory')->name('history');
                                Route::get('campaign/error', 'botPending')->name('pending');
                                Route::post('campaign/update/{id}', 'botUpdate')->name('update');
                                Route::get('campaign/active', 'botProcessing')->name('processing');
                                Route::get('campaign/completed', 'botCompleted')->name('completed');
                                Route::get('campaign/paused', 'botPaused')->name('paused');
                                Route::get('campaign/details/{id}', 'botDetails')->name('details');
                                Route::post('campaign/renew/{id}', 'botRenew')->name('renew');
                                Route::post('campaign/resume/{id}', 'botResume')->name('resume');
                                Route::post('campaign/pause/{id}', 'botPause')->name('pause');
                                Route::post('campaign/cancel/{id}', 'botCancel')->name('cancel');
                                Route::get('campaign/reports/{id}', 'botReports')->name('reports');
                                Route::get('campaign/cancelled', 'botCancelled')->name('cancelled');
                                Route::post('/', 'botNano')->name('nano');
                            }
                        );                   

            //Dripfeed order Controller
            Route::controller('DripfeedController')->name('dripfeed.')->prefix('dripfeed')->group(function () {
                Route::get('/', 'history')->name('history');
                Route::get('pending', 'pending')->name('pending');
                Route::get('processing', 'processing')->name('processing');
                Route::get('completed', 'completed')->name('completed');
                Route::get('cancelled', 'cancelled')->name('cancelled');
                Route::get('refunded', 'refunded')->name('refunded');
                Route::get('overview/{id?}', 'dripfeedOverview')->name('overview');
                Route::post('order/{serviceId?}', 'dripfeed')->name('create');
                Route::get('details/{id}', 'dripfeedDetails')->name('details');
            });

            //Api
            Route::controller('ApiController')->name('api.')->prefix('api')->group(function () {
                Route::get('index', 'api')->name('index');
                Route::post('generate-new-key', 'generateApiKey')->name('generateKey');
            });

            Route::controller('FavoriteController')->name('favorite.')->prefix('favorite')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/add', 'add')->name('add');
            });
        });

        // Payment
        Route::prefix('buy')->name('billing.')->controller('Gateway\PaymentController')->group(function () {
            Route::any('/', 'deposit')->name('index');
            Route::post('insert', 'depositInsert')->name('insert');
            Route::get('confirm', 'depositConfirm')->name('confirm');
            Route::get('manual', 'manualDepositConfirm')->name('manual.confirm');
            Route::post('manual', 'manualDepositUpdate')->name('manual.update');
            Route::post('apply-coupon', 'applyCoupon')->name('apply.coupon');
            Route::post('remove-coupon', 'removeCoupon')->name('remove.coupon');
        });
    });
        // Payment
        Route::controller('Gateway\PaymentController')->prefix('serach-console')->name('seo.')->group(
            function () {
                Route::any('/buy', 'seoBuy')->name('buy');
                Route::any('/buy/trial', 'seoTrialBuy')->name('trial');
    
            }
        );
        // Payment
        Route::controller('Gateway\PaymentController')->prefix('website-traffic')->name('web.')->group(
            function () {
                Route::any('/buy', 'webBuy')->name('buy');
            }
        );
        Route::controller('Gateway\PaymentController')->prefix('realistic-traffic')->name('realistic.')->group(
            function () {
                Route::any('/buy', 'realisticBuy')->name('buy');
            }
        );
        Route::controller('Gateway\PaymentController')->prefix('bot')->name('bot.')->group(
            function () {
                Route::any('/buy', 'botBuy')->name('buy');
            }
        );
});
