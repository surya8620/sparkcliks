<div class="dashboard-header">
    <div class="dashboard-header__inner flex-between">
        <div class="dashboard-header__left">
            <h4 class="dashboard-header__grettings mb-0"></h4>
            <div class="topbar-logo">
            <a class="topbar-logo__link" href="{{route('home')}}"><img src="{{siteLogo()}}" alt="site logo"></a>
        </div>
        </div>
        <div class="dashboard-header__right flex-align">
            <div  class="btn btn--base btn--sm" style="font-size: 12px;">
                <i class="fas fa-clock"></i> <span id="current-time"></span> @lang('UTC')
            </div>
            <div class="user-info">
                <button class="user-info__button flex-align">
                    <span class="user-info__name mb-0" style="font-size: 14px;"> {{auth()->user()->firstname}} </span>
                </button>
                <ul class="user-info-dropdown">
                    <li class="user-info-dropdown__item"><a class="user-info-dropdown__link" href="{{ route('user.profile.setting') }}">
                            <span class="icon"><i class="las la-user"></i></span>
                            <span class="text">  @lang('Profile') </span>
                        </a></li>
                    <li class="user-info-dropdown__item"><a class="user-info-dropdown__link" href="{{ route('user.change.password') }}">
                            <span class="icon"><i class="las la-key"></i></span>
                            <span class="text">  @lang('Change Password') </span>
                        </a></li>
                    <li class="user-info-dropdown__item"><a class="user-info-dropdown__link" href="{{ route('user.twofactor') }}">
                            <span class="icon"><i class="las la-shield-alt"></i></span>
                            <span class="text">@lang('Two Factor')</span>
                        </a></li>
                    <li class="user-info-dropdown__item"><a class="user-info-dropdown__link" href="{{ route('user.logout') }}">
                            <span class="icon"><i class="las la-sign-out-alt"></i></span>
                            <span class="text">@lang('Logout')</span>
                        </a></li>
                </ul>
            </div>
            <div class="dashboard-body__bar d-lg-none d-block">
                <span class="dashboard-body__bar-icon"><i class="fas fa-bars" style="font-size: 18px;"></i></span>
            </div>
        </div>
    </div>
</div>
@push('script')
<script>
    // Function to update the clock
    function updateClock(serverTime) {
        var currentTimeElement = document.getElementById('current-time');

        // Parse the server time and create a Date object
        var serverTimeDate = new Date(
            Date.UTC(
                serverTime.year,
                serverTime.month - 1, // Months are 0-indexed in JavaScript
                serverTime.day,
                serverTime.hour,
                serverTime.minute,
                serverTime.seconds
            )
        );

        function padZero(num) {
            return (num < 10 ? '0' : '') + num;
        }

        var day = padZero(serverTimeDate.getUTCDate());
        var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
            "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        var monthIndex = serverTimeDate.getUTCMonth();
        var year = serverTimeDate.getUTCFullYear();

        var hours = padZero(serverTimeDate.getUTCHours());
        var minutes = padZero(serverTimeDate.getUTCMinutes());

        // Update the time displayed on the page
        currentTimeElement.textContent = `${day} ${monthNames[monthIndex]} ${year} ${hours}:${minutes}`;
    }

    // Function to fetch server time from timeapi.io
    function fetchServerTime() {
        fetch('https://timeapi.io/api/time/current/zone?timeZone=UTC')
            .then(response => response.json())
            .then(data => {
                // Pass the API response directly to updateClock
                updateClock(data);
            })
            .catch(error => console.error('Error fetching server time:', error));
    }

    // Update the clock every second
    setInterval(function () {
        var currentDate = new Date();
        if (currentDate.getUTCSeconds() === 59) {
            fetchServerTime();
        }
    }, 1000);

    // Initial call to fetch server time
    fetchServerTime();
</script>
@endpush