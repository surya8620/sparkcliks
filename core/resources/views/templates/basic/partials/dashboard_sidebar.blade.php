<div class="sidebar-menu flex-between">
    <div class="sidebar-menu__inner">
        <span class="sidebar-menu__close d-lg-none d-block"><i class="fas fa-times"></i></span>
        <div class="sidebar-logo">
            <a class="sidebar-logo__link" href="{{ route('home') }}"><img src="{{ siteLogo() }}" alt="site logo"></a>
        </div>
        <ul class="sidebar-menu-list">
            <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.bot*', 3) }}">
                <a class="sidebar-menu-list__link" href="#">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="menu-icon fa-solid fa fa-robot" style="font-size: 14px; margin-right: 8px;"></i>
                        <span style="display: inline-flex; align-items: center; flex-wrap: wrap;">
                            @lang('Sparky Traffic Bot')
                            <span class="new-badge" style="
                                background: #c51e39;
                                color: white;
                                font-size: 9px;
                                font-weight: 700;
                                padding: 1px 4px;
                                border-radius: 6px;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                box-shadow: 0 1px 3px rgba(197, 30, 57, 0.4);
                                animation: pulse-new 2s ease-in-out infinite;
                                margin-left: 4px;
                                display: inline-block;
                                vertical-align: middle;
                            ">NEW</span>
                        </span>
                        <div class="promo-box" style="
                            background: linear-gradient(135deg, #ffe6ea, #ffcdd2);
                            border: 1px solid #f8bbd9;
                            border-radius: 8px;
                            padding: 4px 8px;
                            margin-top: 6px;
                            text-align: center;
                            box-shadow: 0 2px 4px rgba(197, 30, 57, 0.1);
                        ">
                            <small style="
                                color: #721c24;
                                font-size: 10px;
                                font-weight: 700;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                line-height: 1;
                            ">Up to 90% off</small>
                        </div>
                    </span>
                </a>
                <div class="sidebar-submenu {{ menuActive('user.bot*') }}">
                    <ul class="sidebar-submenu-list">
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.bot.home') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.bot.home') }}">
                                <i class="menu-icon fa-solid fa fa-desktop" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Dashboard')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.bot.new') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.bot.new') }}">
                                <i class="menu-icon fa-solid fa fa-plus" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' New Campaign')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive(['user.bot.history', 'user.bot.pending', 'user.bot.processing', 'user.bot.completed', 'user.bot.paused', 'user.bot.cancelled', 'user.bot.details']) }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.bot.history') }}">
                                <i class="menu-icon fa-solid fa fa-clock" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Campaign History')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.bot.buy') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.bot.buy') }}">
                                <i class="menu-icon fa-solid fab fa-opencart" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Buy Credits')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.seo*', 5) }}">
                <a class="sidebar-menu-list__link" href="#">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="fa-solid fab fa-google" style="font-size: 14px; margin-right: 8px;"></i>
                        <span style="display: inline-flex; align-items: center; flex-wrap: wrap;">
                            @lang('SEO Click Traffic')
                            <span class="promo-badge" style="
                                background: #28a745;
                                color: white;
                                font-size: 8px;
                                font-weight: 700;
                                padding: 1px 4px;
                                border-radius: 6px;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                box-shadow: 0 1px 3px rgba(40, 167, 69, 0.4);
                                animation: pulse-green 2s ease-in-out infinite;
                                margin-left: 4px;
                                display: inline-block;
                                vertical-align: middle;
                            ">BONUS</span>
                        </span>
                        <div class="promo-box" style="
                            background: linear-gradient(135deg, #e8f5e8, #d4edda);
                            border: 1px solid #c3e6cb;
                            border-radius: 8px;
                            padding: 4px 8px;
                            margin-top: 6px;
                            text-align: center;
                            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.1);
                        ">
                            <small style="
                                color: #155724;
                                font-size: 10px;
                                font-weight: 700;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                line-height: 1;
                            ">30% EXTRA Credits</small>
                        </div>
                    </span>
                </a>
                <div class="sidebar-submenu {{ menuActive('user.seo*') }}">
                    <ul class="sidebar-submenu-list">
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.seo.home') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.seo.home') }}">
                                <i class="menu-icon fa-solid fa fa-desktop" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Dashboard')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.seo.new') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.seo.new') }}">
                                <i class="menu-icon fa-solid fa fa-plus" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' New Campaign')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive(['user.seo.history', 'user.seo.pending', 'user.seo.processing', 'user.seo.completed', 'user.seo.denied', 'user.seo.cancelled']) }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.seo.history') }}">
                                <i class="menu-icon fa-solid fa fa-clock" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Campaign History')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.seo.clicks') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.seo.clicks') }}">
                                <i class="menu-icon fa-solid fa fa-mouse" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Click History')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.seo.buy') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.seo.buy') }}">
                                <i class="menu-icon fa-solid fab fa-opencart" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Buy Credits')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.realistic*', 3) }}">
                <a class="sidebar-menu-list__link" href="#">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="menu-icon fa-solid fa fa-user-secret" style="font-size: 14px; margin-right: 8px;"></i>
                        <span style="display: inline-flex; align-items: center; flex-wrap: wrap;">
                            @lang('Realistic Traffic')
                            <span class="promo-badge" style="
                                background: #17a2b8;
                                color: white;
                                font-size: 8px;
                                font-weight: 700;
                                padding: 1px 4px;
                                border-radius: 6px;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                box-shadow: 0 1px 3px rgba(23, 162, 184, 0.4);
                                animation: pulse-blue 2s ease-in-out infinite;
                                margin-left: 4px;
                                display: inline-block;
                                vertical-align: middle;
                            ">OFFER</span>
                        </span>
                        <div class="promo-box" style="
                            background: linear-gradient(135deg, #e1f7fe, #b3e5fc);
                            border: 1px solid #81d4fa;
                            border-radius: 8px;
                            padding: 4px 8px;
                            margin-top: 6px;
                            text-align: center;
                            box-shadow: 0 2px 4px rgba(23, 162, 184, 0.1);
                        ">
                            <small style="
                                color: #0c5460;
                                font-size: 10px;
                                font-weight: 700;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                line-height: 1;
                            ">Up to 60% off</small>
                        </div>
                    </span>
                </a>
                <div class="sidebar-submenu {{ menuActive('user.realistic*') }}">
                    <ul class="sidebar-submenu-list">
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.realistic.home') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.realistic.home') }}">
                                <i class="menu-icon fa-solid fa fa-desktop" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Dashboard')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.realistic.new') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.realistic.new') }}">
                                <i class="menu-icon fa-solid fa fa-plus" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' New Campaign')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive(['user.realistic.history', 'user.realistic.pending', 'user.realistic.processing', 'user.realistic.completed', 'user.realistic.paused', 'user.realistic.denied']) }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.realistic.history') }}">
                                <i class="menu-icon fa-solid fa fa-clock" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Campaign History')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.realistic.buy') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.realistic.buy') }}">
                                <i class="menu-icon fa-solid fab fa-opencart" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Buy Credits')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.web*', 3) }}">
                <a class="sidebar-menu-list__link" href="#">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="menu-icon fa-solid fa fa-chart-line" style="font-size: 14px; margin-right: 8px;"></i>
                        <span style="display: inline-flex; align-items: center; flex-wrap: wrap;">
                            @lang('Website Traffic')
                            <span class="promo-badge" style="
                                background: #6f42c1;
                                color: white;
                                font-size: 8px;
                                font-weight: 700;
                                padding: 1px 4px;
                                border-radius: 6px;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                box-shadow: 0 1px 3px rgba(111, 66, 193, 0.4);
                                animation: pulse-purple 2s ease-in-out infinite;
                                margin-left: 4px;
                                display: inline-block;
                                vertical-align: middle;
                            ">OFFER</span>
                        </span>
                        <div class="promo-box" style="
                            background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
                            border: 1px solid #c084fc;
                            border-radius: 8px;
                            padding: 4px 8px;
                            margin-top: 6px;
                            text-align: center;
                            box-shadow: 0 2px 4px rgba(111, 66, 193, 0.1);
                        ">
                            <small style="
                                color: #553c9a;
                                font-size: 10px;
                                font-weight: 700;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                line-height: 1;
                            ">Up to 40% off</small>
                        </div>
                    </span>
                </a>
                <div class="sidebar-submenu {{ menuActive('user.web*') }}">
                    <ul class="sidebar-submenu-list">
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.web.home') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.web.home') }}">
                                <i class="menu-icon fa-solid fa fa-desktop" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Dashboard')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.web.new') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.web.new') }}">
                                <i class="menu-icon fa-solid fa fa-plus" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' New Campaign')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive(['user.web.history', 'user.web.pending', 'user.web.processing', 'user.web.completed', 'user.web.paused', 'user.web.denied']) }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.web.history') }}">
                                <i class="menu-icon fa-solid fa fa-clock" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Campaign History')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.web.buy') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.web.buy') }}">
                                <i class="menu-icon fa-solid fab fa-opencart" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Buy Credits')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="sidebar-menu-list__item">
                 <a class="sidebar-menu-list__link" href="{{ route('user.sparkproxy.sso') }}">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="menu-icon fa-solid fa fa-server" style="font-size: 14px; margin-right: 8px;"></i>
                        <span style="display: inline-flex; align-items: center; flex-wrap: wrap;">
                            @lang('SparkProxy')
                            <span class="new-badge" style="
                                background: #c51e39;
                                color: white;
                                font-size: 9px;
                                font-weight: 700;
                                padding: 1px 4px;
                                border-radius: 6px;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                box-shadow: 0 1px 3px rgba(197, 30, 57, 0.4);
                                animation: pulse-new 2s ease-in-out infinite;
                                margin-left: 4px;
                                display: inline-block;
                                vertical-align: middle;
                            ">NEW</span>
                        </span>
                        <div class="promo-box" style="
                            background: linear-gradient(135deg, #ffe6ea, #ffcdd2);
                            border: 1px solid #f8bbd9;
                            border-radius: 8px;
                            padding: 4px 8px;
                            margin-top: 6px;
                            text-align: center;
                            box-shadow: 0 2px 4px rgba(197, 30, 57, 0.1);
                        ">
                            <small style="
                                color: #721c24;
                                font-size: 10px;
                                font-weight: 700;
                                text-transform: uppercase;
                                letter-spacing: 0.3px;
                                line-height: 1;
                            ">Unlimited Data</small>
                        </div>
                    </span>
                </a>
            </li>
            <li class="sidebar-menu-list__item has-dropdown {{ menuActive('user.billing*', 3) }}">
                <a class="sidebar-menu-list__link" href="#">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="menu-icon fa-sharp fa-solid fa fa-money-check" style="font-size: 14px; margin-right: 8px;"></i>
                        @lang('Billing')
                    </span>
                </a>
                <div class="sidebar-submenu {{ menuActive('user.billing*') }}">
                    <ul class="sidebar-submenu-list">
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.billing.history') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.billing.history') }}">
                                <i class="menu-icon fa-solid fa fa-list" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Payments')</span>
                            </a>
                        </li>
                        <li class="sidebar-submenu-list__item  {{ menuActive('user.billing.transactions') }}">
                            <a class="sidebar-submenu-list__link" href="{{ route('user.billing.transactions') }}">
                                <i class="menu-icon fa-solid fa fa-exchange" style="font-size: 14px;"></i>
                                <span class="text" style="font-size: 14px;">@lang(' Transactions')</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="sidebar-menu-list__item">
                <a class="sidebar-menu-list__link" href="{{ route('ticket.index') }}">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="menu-icon fa-sharp fa-solid fa fa-ticket" style="font-size: 14px; margin-right: 8px;"></i>
                        @lang(' Support Ticket')
                    </span>
                </a>
            </li>

            <li class="sidebar-menu-list__item">
                <a class="sidebar-menu-list__link" href="https://www.sparkcliks.com/delivery-policy/">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="menu-icon fa-sharp fa-solid fab fa-get-pocket" style="font-size: 14px; margin-right: 8px;"></i>
                        @lang('Delivery Policy')
                    </span>
                </a>
            </li>

            <li class="sidebar-menu-list__item">
                <a class="sidebar-menu-list__link" href="#">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px;">
                        <i class="menu-icon fa-sharp fa-solid fa fa-comments" style="font-size: 14px; margin-right: 8px;"></i>
                        @lang('Support')
                        <div class="support-hours" style="
                            background: #f8f9fa;
                            border: 1px solid #e9ecef;
                            border-radius: 6px;
                            padding: 4px 8px;
                            margin-top: 4px;
                            font-size: 11px;
                            color: #6c757d;
                            text-align: center;
                            line-height: 1.2;
                        ">4.30 AM - 4.30 PM UTC</div>
                    </span>
                </a>
            </li>

            <li class="sidebar-menu-list__item" style="margin-bottom: 20px;">
                <a class="sidebar-menu-list__link" href="{{ route('user.logout') }}">
                    <span class="icon">
                    </span>
                    <span class="text" style="font-size: 14px; color: #dc3545; font-weight: 600; padding-bottom: 10px; display: block;">
                        <i class="fa-solid fa fa-sign-out" style="font-size: 14px; margin-right: 8px; color: #dc3545;"></i>
                        @lang('Logout')
                    </span>
                </a>
            </li>
        </ul>
    </div>
</div>

@push('script')
    <style>
        /* NEW badge animation */
        @keyframes pulse-new {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 2px 4px rgba(197, 30, 57, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 3px 8px rgba(197, 30, 57, 0.5);
            }
        }
        
        /* Green badge animation (BONUS) */
        @keyframes pulse-green {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 3px 8px rgba(40, 167, 69, 0.5);
            }
        }
        
        /* Blue badge animation (SALE) */
        @keyframes pulse-blue {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 2px 4px rgba(23, 162, 184, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 3px 8px rgba(23, 162, 184, 0.5);
            }
        }
        
        /* Orange badge animation (OFFER) */
        @keyframes pulse-orange {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 2px 4px rgba(253, 126, 20, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 3px 8px rgba(253, 126, 20, 0.5);
            }
        }
        
        /* Purple badge animation (OFFER) */
        @keyframes pulse-purple {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 2px 4px rgba(111, 66, 193, 0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 3px 8px rgba(111, 66, 193, 0.5);
            }
        }
        
        /* Discount text glow animation */
        @keyframes glow-green {
            0% {
                box-shadow: 0 1px 2px rgba(40, 167, 69, 0.2);
                transform: scale(1);
            }
            100% {
                box-shadow: 0 2px 8px rgba(40, 167, 69, 0.4);
                transform: scale(1.02);
            }
        }
        
        /* Blue glow animation */
        @keyframes glow-blue {
            0% {
                box-shadow: 0 1px 2px rgba(23, 162, 184, 0.2);
                transform: scale(1);
            }
            100% {
                box-shadow: 0 2px 8px rgba(23, 162, 184, 0.4);
                transform: scale(1.02);
            }
        }
        
        /* Orange glow animation */
        @keyframes glow-orange {
            0% {
                box-shadow: 0 1px 2px rgba(253, 126, 20, 0.2);
                transform: scale(1);
            }
            100% {
                box-shadow: 0 2px 8px rgba(253, 126, 20, 0.4);
                transform: scale(1.02);
            }
        }
        
        /* Hover effects for badges */
        .sidebar-menu-list__link:hover .new-badge {
            background: #a91729 !important;
            transform: scale(1.1);
            transition: all 0.2s ease;
        }
        
        .sidebar-menu-list__link:hover .promo-badge {
            transform: scale(1.1);
            transition: all 0.2s ease;
        }
        
        /* Hover effect for discount text */
        .sidebar-menu-list__link:hover .discount-text {
            transform: scale(1.05);
            transition: all 0.2s ease;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .new-badge, .promo-badge {
                font-size: 8px !important;
                padding: 1px 3px !important;
            }
        }
    </style>
    <script>
        (function($) {
            "use strict";
            //Scroll to paginate position
            var pathName = document.location.pathname;
            window.onbeforeunload = function() {
                var scrollPosition = $(document).scrollTop();
                sessionStorage.setItem("scrollPosition_" + pathName, scrollPosition.toString());
            }
            if (sessionStorage["scrollPosition_" + pathName]) {
                $(document).scrollTop(sessionStorage.getItem("scrollPosition_" + pathName));
            }
            $('#from-prevent-multiple-submits').on('submit', function() {
                $("#btn-save", this)
                    .html("Please wait...")
                    .attr('disabled', 'disabled');
                return true;
            })

        })(jQuery);
    </script>
@endpush
