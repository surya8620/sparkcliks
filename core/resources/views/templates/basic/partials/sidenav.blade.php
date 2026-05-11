<div class="sidebar bg--dark">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a class="sidebar__main-logo" href="{{ route('home') }}"><img
                    src="{{ getImage(getFilePath('logoIcon') . '/logo.png') }}" alt="@lang('image')"></a>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
            {{--Organic Traffic--}}
            <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('user.seo*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon fa-solid fab fa-google"></i>
                        <span class="menu-title" data-toggle="tooltip" data-placement="right" title="Offer: Get 30% additional Credits on Monthly Packs">@lang('SEO Clicks')
			<p class="menu-badge pill bg--danger ms-auto" style="text-align: center;">@lang('Extra 30% Credits')</p></span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('user.seo*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.seo.home') }}">
                                <a class="nav-link" href="{{ route('user.seo.home') }}">
                                    <i class="menu-icon fa-solid fa fa-desktop"></i>
                                    <span class="menu-title">@lang('Dashboard')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.seo.new') }}">
                                <a class="nav-link" href="{{ route('user.seo.new') }}">
                                    <i class="menu-icon fa-solid fa fa-plus"></i>
                                    <span class="menu-title">@lang('New Campaign')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.seo.history') }}">
                                <a class="nav-link" href="{{ route('user.seo.history') }}">
                                    <i class="menu-icon fa-solid fa fa-clock"></i>
                                    <span class="menu-title">@lang('Campaign History')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.seo.clicks') }}">
                                <a class="nav-link" href="{{ route('user.seo.clicks') }}">
                                    <i class="menu-icon fa-solid fa fa-mouse"></i>
                                    <span class="menu-title">@lang('Click History')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.seo.buy') }}">
                                <a class="nav-link" href="{{ route('user.seo.buy') }}">
                                    <i class="menu-icon fa-solid fab fa-opencart"></i>
                                    <span class="menu-title">@lang('Buy Credits')</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                {{--Bot--}}
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('user.realistic*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon fa-solid fa fa-user-secret"></i>
                        <span class="menu-title" data-toggle="tooltip" data-placement="right" title="Offer: Get Flat 50% Off">@lang('Realistic Traffic')
			        <p class="menu-badge pill bg--danger ms-auto" style="text-align: center;">@lang('Flat 50% Off')</p></span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('user.realistic*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.realistic.home') }}">
                                <a class="nav-link" href="{{ route('user.realistic.home') }}">
                                    <i class="menu-icon fa-solid fa fa-desktop"></i>
                                    <span class="menu-title">@lang('Dashboard')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.realistic.new') }}">
                                <a class="nav-link" href="{{ route('user.realistic.new') }}">
                                    <i class="menu-icon fa-solid fa fa-plus"></i>
                                    <span class="menu-title">@lang('New Campaign')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.realistic.history') }}">
                                <a class="nav-link" href="{{ route('user.realistic.history') }}">
                                    <i class="menu-icon fa-solid fa fa-clock"></i>
                                    <span class="menu-title">@lang('Campaign History')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.realistic.buy') }}">
                                <a class="nav-link" href="{{ route('user.realistic.buy') }}">
                                    <i class="menu-icon fa-solid fab fa-opencart"></i>
                                    <span class="menu-title">@lang('Buy Credits')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                {{--Realistic Traffic--}}
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('user.realistic*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon fa-solid fa fa-user-secret"></i>
                        <span class="menu-title" data-toggle="tooltip" data-placement="right" title="Offer: Get Flat 50% Off">@lang('Realistic Traffic')
			        <p class="menu-badge pill bg--danger ms-auto" style="text-align: center;">@lang('Flat 50% Off')</p></span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('user.realistic*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.realistic.home') }}">
                                <a class="nav-link" href="{{ route('user.realistic.home') }}">
                                    <i class="menu-icon fa-solid fa fa-desktop"></i>
                                    <span class="menu-title">@lang('Dashboard')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.realistic.new') }}">
                                <a class="nav-link" href="{{ route('user.realistic.new') }}">
                                    <i class="menu-icon fa-solid fa fa-plus"></i>
                                    <span class="menu-title">@lang('New Campaign')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.realistic.history') }}">
                                <a class="nav-link" href="{{ route('user.realistic.history') }}">
                                    <i class="menu-icon fa-solid fa fa-clock"></i>
                                    <span class="menu-title">@lang('Campaign History')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.realistic.buy') }}">
                                <a class="nav-link" href="{{ route('user.realistic.buy') }}">
                                    <i class="menu-icon fa-solid fab fa-opencart"></i>
                                    <span class="menu-title">@lang('Buy Credits')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                {{--GA Traffic--}}
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('user.web*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon fa-solid fa fa-chart-line"></i>
                        <span class="menu-title" data-toggle="tooltip" data-placement="right" title="Offer: Get Flat 30% Off">@lang('Website Traffic')
			        <p class="menu-badge pill bg--danger ms-auto" style="text-align: center;">@lang('Flat 30% Off')</p></span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('user.web*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.web.home') }}">
                                <a class="nav-link" href="{{ route('user.web.home') }}">
                                    <i class="menu-icon fa-solid fa fa-desktop"></i>
                                    <span class="menu-title">@lang('Dashboard')</span>
                                </a>
                            </li>

                            <li class="sidebar-menu-item {{ menuActive('user.web.new') }}">
                                <a class="nav-link" href="{{ route('user.web.new') }}">
                                    <i class="menu-icon fa-solid fa fa-plus"></i>
                                    <span class="menu-title">@lang('New Campaign')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.web.history') }}">
                                <a class="nav-link" href="{{ route('user.web.history') }}">
                                    <i class="menu-icon fa-solid fa fa-clock"></i>
                                    <span class="menu-title">@lang('Campaign History')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.web.buy') }}">
                                <a class="nav-link" href="{{ route('user.web.buy') }}">
                                    <i class="menu-icon fa-solid fab fa-opencart"></i>
                                    <span class="menu-title">@lang('Buy Credits')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="nav-link" href="{{ route('user.sparkproxy.sso') }}">
                        <i class="menu-icon fa-solid fa fa-tasks"></i>
                        <span class="menu-title" data-toggle="tooltip" data-placement="right" title="Try for Free">@lang('SparkProxy')
						<p class="menu-badge pill bg--danger ms-auto" style="text-align: center;">@lang('Try Now')</p></span>
                    </a>
                </li>
                {{--Billing--}}
                <li class="sidebar-menu-item sidebar-dropdown">
                    <a class="{{ menuActive('user.billing*', 3) }}" href="javascript:void(0)">
                        <i class="menu-icon fa-sharp fa-solid fa fa-money-check"></i>
                        <span class="menu-title">@lang('Billing')</span>
                    </a>
                    <div class="sidebar-submenu {{ menuActive('user.billing*', 2) }}">
                        <ul>
                            <li class="sidebar-menu-item {{ menuActive('user.billing.history') }}">
                                <a class="nav-link" href="{{ route('user.billing.history') }}">
                                    <i class="menu-icon la la-list-alt"></i>
                                    <span class="menu-title">@lang('History')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{ menuActive('user.transactions') }}">
                                <a class="nav-link" href="{{ route('user.transactions') }}">
                                    <i class="menu-icon la la-exchange-alt"></i>
                                    <span class="menu-title">@lang('Transactions')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="sidebar-menu-item {{ menuActive('ticket*') }}">
                    <a class="nav-link" href="{{ route('ticket.index') }}">
                        <i class="menu-icon la-solid la la-ticket"></i>
                        <span class="menu-title">@lang('Support Ticket')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('user.twofactor') }}">
                    <a class="nav-link" href="{{ route('user.twofactor') }}">
                        <i class="menu-icon la la-lock"></i>
                        <span class="menu-title">@lang('2FA Security')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="nav-link" href="https://www.sparkcliks.com/delivery-policy/">
                        <i class="menu-icon fa-sharp fa-solid fab fa-get-pocket"></i>
                        <span class="menu-title">@lang('Delivey Policy')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="nav-link" href="#">
                        <i class="menu-icon fa-sharp fa-solid fa fa-comments"></i>
                        <span class="menu-title">@lang('Support')</span><br><br>
			<span class="menu-title">@lang('4.30 AM to 4.30 PM UTC')</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- sidebar end -->

@push('script')
    <script>
        if ($('li').hasClass('active')) {
            $('#sidebar__menuWrapper').animate({
                scrollTop: eval($(".active").offset().top - 320)
            }, 500);
        }
    </script>
@endpush
