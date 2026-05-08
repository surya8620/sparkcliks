<!-- navbar-wrapper start -->
<nav class="navbar-wrapper bg--dark">
<div class="alert alert-warning" role="alert">
<p class="navbar-user__name"></p>
</div>&nbsp; &nbsp; 
    <div class="navbar__left">
	<button class="res-sidebar-open-btn me-3" type="button"><i class="las la-bars"></i></button>
    	<div id="time-widget">
    	    <p class="navbar-user__name">@lang('Universal Time, ') <span id="current-time"></span></p>
    	</div>&nbsp; &nbsp<br>
	<div class="alert alert-warning" role="alert">
		<p class="navbar-user__name"></p>
	</div>
    </div>
    <div class="navbar__right">
        <ul class="navbar__action-list">
            <li class="dropdown">
                <button class="" data-bs-toggle="dropdown" data-display="static" type="button" aria-haspopup="true" aria-expanded="false">
                    <span class="navbar-user">
                        <span class="navbar-user__thumb"><img
                                src="{{ getImage(getFilePath('userProfile') . '/' . auth()->user()->image, getFileSize('userProfile')) }}" alt="@lang('image')"></span>
                        <span class="navbar-user__info">
                            <span class="navbar-user__name">{{ auth()->user()->username }}</span>
                        </span>
                        <span class="icon"><i class="las la-chevron-circle-down"></i></span>
                    </span>
                </button>
                <div class="dropdown-menu dropdown-menu--sm box--shadow1 dropdown-menu-right border-0 p-0">
                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.profile.setting') }}">
                        <i class="dropdown-menu__icon las la-user-circle"></i>
                        <span class="dropdown-menu__caption">@lang('Profile')</span>
                    </a>

                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.change.password') }}">
                        <i class="dropdown-menu__icon las la-key"></i>
                        <span class="dropdown-menu__caption">@lang('Password')</span>
                    </a>

                    <a class="dropdown-menu__item d-flex align-items-center px-3 py-2" href="{{ route('user.logout') }}">
                        <i class="dropdown-menu__icon las la-sign-out-alt"></i>
                        <span class="dropdown-menu__caption">@lang('Logout')</span>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
&nbsp;

<!-- navbar-wrapper end -->
@push('script')
<script>
    // Function to update the clock
    function updateClock(serverTime) {
        var currentTimeElement = document.getElementById('current-time');
        var serverTime = new Date(serverTime);

        function padZero(num) {
            return (num < 10 ? '0' : '') + num;
        }

        var day = padZero(serverTime.getUTCDate());
        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];
        var monthIndex = serverTime.getUTCMonth();
        var year = serverTime.getUTCFullYear();

        var hours = padZero(serverTime.getUTCHours());
        var minutes = padZero(serverTime.getUTCMinutes());
        var seconds = padZero(serverTime.getUTCSeconds());

        // Update the time displayed on the page
        currentTimeElement.textContent = day + ' ' + monthNames[monthIndex] + ' ' + year + ' ' + hours + ':' + minutes;
    }

    // Function to fetch server time from World Time API every minute
    function fetchServerTime() {
        fetch('https://worldtimeapi.org/api/timezone/UTC')
            .then(response => response.json())
            .then(data => {
                updateClock(data.utc_datetime);
            })
            .catch(error => console.error('Error fetching server time:', error));
    }

    // Update the clock every second
    setInterval(function() {
        var currentDate = new Date();
        if (currentDate.getUTCSeconds() === 59) {
            fetchServerTime();
        }
    }, 1000);

    // Initial call to fetch server time
    fetchServerTime();
</script>>

@endpush
