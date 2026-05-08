@extends($activeTemplate.'layouts.master')
@section('content')
    @php
        // Redirect if user hasn't acknowledged bot guidelines
        if (auth()->user()->bot_ack == 0) {
            header('Location: ' . route('user.bot.home'));
            exit();
        }
    @endphp
    
    <div class="row g-1 g-lg-3 justify-content-center">
        @if($user->bot_credit > 0 && $user->status == 1)
        <div class="col-lg-12 col-md-12 mb-4"> 
            <div class="card">
                <div class="card-header border-0 pb-2">
                    <!-- Optimized Nav Pills Tabs with Mobile Support -->
                    <ul class="nav nav-pills nav-fill campaign-tabs-horizontal w-100" id="campaignTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active" id="details-tab" data-bs-toggle="pill" data-toggle="pill" href="#details-content" role="tab" aria-controls="details-content" aria-selected="true" data-tab-name="details">
                                <i class="las la-info-circle tab-icon"></i> 
                                <span class="tab-text">@lang('Details')</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="advanced-tab" data-bs-toggle="pill" data-toggle="pill" href="#advanced-content" role="tab" aria-controls="advanced-content" aria-selected="false" data-tab-name="advanced">
                                <i class="las la-cogs tab-icon"></i> 
                                <span class="tab-text">@lang('Advanced')</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link" id="proxy-tab" data-bs-toggle="pill" data-toggle="pill" href="#proxy-content" role="tab" aria-controls="proxy-content" aria-selected="false" data-tab-name="proxy">
                                <i class="las la-network-wired tab-icon"></i> 
                                <span class="tab-text">@lang('Proxy')</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            @if(in_array($order->status, [5, 6]))
                            <a class="nav-link disabled" href="javascript:void(0)" role="tab" tabindex="-1" aria-disabled="true" title="@lang('Logs not available for') {{ $order->status == 5 ? __('expired') : __('paused') }} @lang('campaigns')">
                                <i class="fa-solid fa-file-lines tab-icon"></i> 
                                <span class="tab-text">@lang('Logs')</span>
                            </a>
                            @else
                            <a class="nav-link" href="{{ route('user.bot.logs', $order->id) }}" role="tab" data-tab-name="logs">
                                <i class="fa-solid fa-file-lines tab-icon"></i> 
                                <span class="tab-text">@lang('Logs')</span>
                            </a>
                            @endif
                        </li>
                    </ul>
                </div>

                <div class="card-body">

                    <form id="projectForm" action="{{ route('user.bot.update', $order->id) }}" method="post">
                        @csrf
                        
                        <!-- Tab Content -->
                        <div class="tab-content" id="campaignTabContent">
                            <!-- Campaign Details Tab -->
                            <div class="tab-pane fade show active" id="details-content" role="tabpanel" aria-labelledby="details-tab">
                                <div class="row">
                                    <div class="form-group col-12 col-md-12">
                                        <label class="form--label">@lang('Campaign Name')
                                            <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Give your campaign a unique, descriptive name to easily identify it later. This helps organize multiple campaigns."></i>
                                        </label>
                                        <input class="form--control" name="name" value="{{ $order->name }}" placeholder="My Campaign" required>
                                    </div>
                            <div class="form-group col-12 col-md-8">
                                <label class="form--label">@lang('Enter URL')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Add the target websites you want to drive traffic to. Each URL should include http:// or https://. You can add multiple URLs (one per line) and the system will visit them according to your URL order setting."></i>
                                </label>
                                <textarea class="form--control" name="urls" rows="2" placeholder="Add the Initial URLs/websites (each per line)  https://example.com" required>{{ str_replace(',', "\n", $order->urls) }}</textarea>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('URL Order')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Choose how multiple URLs are visited: 'One by One' visits URLs in the exact order listed, 'Random' visits URLs in a random sequence for more natural traffic patterns."></i>
                                </label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleUrlOrder" class="btn {{ $order->url_order === 'one_by_one' ? 'btn--primary' : 'btn--base' }} me-2 w-100">{{ $order->url_order === 'one_by_one' ? 'One by One' : 'Random' }}</button>
                                    <input type="hidden" name="url_order" id="urlOrderInput" value="{{ $order->url_order }}">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="form--label">@lang('Devices')
                                        <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Select the device type for browser sessions: Desktop, Mobile, Mixed (combination), or Random (varies each session). This affects the user agent and viewport size."></i>
                                    </label>
                                    <select id="devices" class="form--control" name="devices">
                                        <option value="desktop" {{ $order->td === 'desktop' ? 'selected' : '' }}>Desktop</option>
                                        <option value="mobile" {{ $order->td === 'mobile' ? 'selected' : '' }}>Mobile</option>
                                        <option value="mixed" {{ $order->td === 'mixed' ? 'selected' : '' }}>Mixed</option>
                                        <option value="random" {{ $order->td === 'random' ? 'selected' : '' }}>Random</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label class="form--label col-sm-12">
                                @lang('Wait Time After Visiting Initial URL')
                                <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Time to wait on each page after it loads. This simulates real user behavior and helps avoid being detected as automated traffic. Longer delays appear more natural but slower overall."></i>
                            </label>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Min')<small class="text--base" style="font-size: 0.75rem;">@lang(' (in seconds)')</small></label>
                                <input type="number" name="min_delay" class="form--control" placeholder="Min Delay" min="0" step="1" value="{{ $order->min_delay ?? 10 }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Max')<small class="text--base" style="font-size: 0.75rem;">@lang(' (in seconds)')</small></label>
                                <input type="number" name="max_delay" class="form--control" placeholder="Max Delay" min="0" step="1" value="{{ $order->max_delay ?? 30 }}">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Referrer Extensions')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Enable to simulate traffic coming from other websites (referrer sites). This makes your traffic appear more organic by showing it came from external sources. Cannot be used with Search Extensions."></i>
                                </label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleReferral" class="btn {{ $order->ext_type === 'referrer' ? 'btn--base' : 'btn--primary' }} me-2 w-100">{{ $order->ext_type === 'referrer' ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="referrer_enabled" id="referrer_enabled" value="{{ $order->ext_type === 'referrer' ? 1 : 0 }}">
                                </div>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Search Extensions')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Simulate traffic from search engines using specific keywords. This makes traffic appear to come from Google, Bing, or Google Maps searches. Cannot be used with Referrer Extensions."></i>
                                </label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleSearch" class="btn {{ in_array($order->ext_type, ['google', 'google_maps', 'bing']) ? 'btn--base' : 'btn--primary' }} me-2 w-100" {{ in_array($order->ext_type, ['google', 'google_maps', 'bing']) || $order->ext_type === 'referrer' ? '' : 'disabled=""' }}>{{ in_array($order->ext_type, ['google', 'google_maps', 'bing']) ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="search_enabled" id="search_enabled" value="{{ in_array($order->ext_type, ['google', 'google_maps', 'bing']) ? 1 : 0 }}">
                                </div>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Search Engine')</label>
                                <select id="search_ext" class="form--control" name="search_engine" {{ in_array($order->ext_type, ['google', 'google_maps', 'bing']) ? '' : 'disabled=""' }}>
                                    <option value="google" {{ $order->ext_type === 'google' ? 'selected' : '' }}>Google - Coming Soon</option>
                                    <option value="google_maps" {{ $order->ext_type === 'google_maps' ? 'selected' : '' }}>Google Maps/My Business</option>
                                    <option value="bing" {{ $order->ext_type === 'bing' ? 'selected' : '' }}>Bing</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Referrer URLs')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="List of websites that will appear as traffic sources. Add one URL per line. These will be set as referrer headers, making it look like visitors came from these sites. Use popular, relevant websites for best results."></i>
                                </label>
                                <textarea id="referrer_urls" class="form--control" name="referrer_urls" rows="3" placeholder="Add the referrer URLs/websites (each per line)  https://example.com">{{ $order->ref ? str_replace(',', "\n", $order->ref) : '' }}</textarea>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Search Keywords')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Keywords that will simulate search queries leading to your site. Add one keyword per line. Use relevant terms that people might actually search for to find your content. Works best with keywords your site ranks for."></i>
                                </label>
                                <textarea id="search_keywords" class="form--control" name="search_keywords" rows="3" placeholder="Add the keywords (each per line)" {{ in_array($order->ext_type, ['google', 'google_maps', 'bing']) ? '' : 'disabled=""' }}>{{ $order->keywords ? str_replace(',', "\n", $order->keywords) : '' }}</textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">@lang('Script')
                                            <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Create a sequence of automated actions that browsers will perform on your target pages. Add widgets like URL navigation, waiting, scrolling, and clicking to simulate realistic user behavior. The order matters - browsers will execute actions from top to bottom."></i>
                                        </h4>
                                        <small class="text--base">@lang('Build your automation script by adding widgets.')</small>
                                    </div>
                                    <div class="card-body">
                                        <ol id="page_sections" class="simple_with_drop vertical sec-item">
                                        @php
                                            $scriptParts = $order->flow ? explode(';', $order->flow) : [];
                                            $sections = collect();
                                            
                                            foreach ($scriptParts as $index => $part) {
                                                if (empty(trim($part))) continue;
                                                
                                                $section = (object)[
                                                    'type' => '',
                                                    'settings' => []
                                                ];
                                                
                                                if (str_starts_with($part, 'url=')) {
                                                    $section->type = 'URL';
                                                    $urlValue = substr($part, 4);
                                                    // Remove surrounding quotes if they exist
                                                    $urlValue = trim($urlValue, "'\"");
                                                    $section->settings = ['url' => $urlValue];
                                                } elseif (str_starts_with($part, 'wait=')) {
                                                    $section->type = 'Wait';
                                                    $times = explode(':', substr($part, 5));
                                                    $section->settings = ['min_wait' => $times[0] ?? '', 'max_wait' => $times[1] ?? ''];
                                                } elseif (str_starts_with($part, 'click=')) {
                                                    $section->type = 'Click';
                                                    $clickData = explode(':', substr($part, 6));
                                                    $section->settings = ['click_type' => $clickData[0] ?? '', 'click_percentage' => $clickData[1] ?? ''];
                                                } elseif (str_starts_with($part, 'scroll=')) {
                                                    $section->type = 'Scroll';
                                                    $scrollData = explode(':', substr($part, 7));
                                                    $section->settings = ['scroll_type' => $scrollData[0] ?? '', 'scroll_count' => $scrollData[1] ?? '', 'scroll_percentage' => $scrollData[2] ?? '', 'scroll_delay' => $scrollData[3] ?? ''];
                                                } elseif ($part === 'refresh') {
                                                    $section->type = 'Refresh';
                                                } elseif (str_starts_with($part, 'nav=')) {
                                                    $section->type = substr($part, 4) === 'back' ? 'NavigateBack' : 'NavigateForward';
                                                } elseif ($part === 'loadpage' || str_starts_with($part, 'loadpage=')) {
                                                    $section->type = 'LoadPageFull';
                                                }
                                                
                                                if ($section->type) {
                                                    $sections->push($section);
                                                }
                                            }
                                        @endphp
                                        
                                        @forelse($sections as $index => $section)
                                            <li class="highlight icon-move" data-key="{{ $section->type }}">
                                                <div class="d-flex align-items-center mb-2">
                                                    <i class="sortable-icon la la-arrows-alt-v" title="Drag to reorder"></i>
                                                    <span class="d-inline-block me-auto text--base flex-grow-1">
                                                        @if($section->type === 'URL') @lang('Navigate to URL')
                                                        @elseif($section->type === 'Wait') @lang('Wait')
                                                        @elseif($section->type === 'Scroll') @lang('Scroll')
                                                        @elseif($section->type === 'Click') @lang('Click')
                                                        @elseif($section->type === 'Refresh') @lang('Refresh')
                                                        @elseif($section->type === 'LoadPageFull') @lang('Wait For Page Load')
                                                        @elseif($section->type === 'NavigateForward') @lang('Navigate Forward')
                                                        @elseif($section->type === 'NavigateBack') @lang('Navigate Back')
                                                        @else {{ $section->type }}
                                                        @endif
                                                    </span>
                                                    <button type="button" class="ms-auto d-inline-block remove-icon remove-icon-color btn-remove" style="border: none; background: none; padding: 0;" title="Delete widget">
                                                        <i class="la la-trash"></i>
                                                    </button>
                                                </div>
                                                <input type="hidden" name="secs[{{ $index }}]" value="{{ $section->type }}">
                                                <input type="hidden" name="order[{{ $index }}]" value="{{ $index }}">
                                                @if($section->type === 'URL')
                                                    <input type="url" name="url[{{ $index }}]" class="form-control mt-2" value="{{ $section->settings['url'] ?? '' }}" placeholder="https://example.com" pattern="https?://.+" title="Please enter a valid URL starting with http:// or https://" required>
                                                @elseif($section->type === 'Wait')
                                                    <div class="d-flex mt-2">
                                                        <input type="number" name="min_wait[{{ $index }}]" class="form-control me-2" value="{{ $section->settings['min_wait'] ?? '' }}" placeholder="Min (s)">
                                                        <input type="number" name="max_wait[{{ $index }}]" class="form-control" value="{{ $section->settings['max_wait'] ?? '' }}" placeholder="Max (s)">
                                                    </div>
                                                @elseif($section->type === 'Scroll')
                                                    <div class="scroll-widget-inputs mt-2">
                                                        <select name="scroll_type[{{ $index }}]" class="form-control scroll-input-item">
                                                            <option value="Random" {{ ($section->settings['scroll_type'] ?? '') === 'Random' ? 'selected' : '' }}>Random</option>
                                                            <option value="Up" {{ ($section->settings['scroll_type'] ?? '') === 'Up' ? 'selected' : '' }}>Up</option>
                                                            <option value="Down" {{ ($section->settings['scroll_type'] ?? '') === 'Down' ? 'selected' : '' }}>Down</option>
                                                        </select>
                                                        <input type="number" name="scroll_percentage[{{ $index }}]" class="form-control scroll-input-item" value="{{ $section->settings['scroll_percentage'] ?? '' }}" placeholder="Depth %" min="0" max="100">
                                                        <input type="number" name="scroll_count[{{ $index }}]" class="form-control scroll-input-item" value="{{ $section->settings['scroll_count'] ?? '' }}" placeholder="No of Times" min="1">
                                                        <input type="number" name="scroll_delay[{{ $index }}]" class="form-control scroll-input-item" value="{{ $section->settings['scroll_delay'] ?? '' }}" placeholder="Delay (s)" min="0" step="1">
                                                    </div>
                                                @elseif($section->type === 'Click')
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <select name="click_type[{{ $index }}]" class="form-control me-2">
                                                            <option value="Internal" {{ ($section->settings['click_type'] ?? '') === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ ($section->settings['click_type'] ?? '') === 'External' ? 'selected' : '' }}>External</option>
                                                            <option value="Random" {{ ($section->settings['click_type'] ?? '') === 'Random' ? 'selected' : '' }}>Random</option>
                                                        </select>
                                                        <input type="number" name="click_percentage[{{ $index }}]" class="form-control" value="{{ $section->settings['click_percentage'] ?? '' }}" placeholder=" Click-Through Rate" min="0" max="100">
                                                    </div>
                                                @endif
                                            </li>
                                        @empty
                                            <li class="empty-state">
                                                <div class="empty-state-content">
                                                    <i class="las la-robot empty-state-icon"></i>
                                                    <h5>@lang('Your script will appear here')</h5>
                                                    <p class="text-muted mb-3">@lang('Start building your automation by adding widgets below')</p>
                                                    <button type="button" class="btn btn--primary add-widget-btn" data-toggle="modal" data-target="#widgetModal">
                                                        <i class="las la-plus"></i> @lang('Add Widget')
                                                    </button>
                                                </div>
                                            </li>
                                        @endforelse
                                        </ol>
                                        <!-- Add Widget Button (shown when script has items) -->
                                        <div class="text-center mt-3" id="addWidgetSection" style="{{ $sections->count() > 0 ? '' : 'display: none;' }}">
                                            <button type="button" class="btn btn--primary add-widget-btn" data-toggle="modal" data-target="#widgetModal">
                                                <i class="las la-plus"></i> @lang('Add Widget')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Widget Selection Modal -->
                        <div class="modal fade" id="widgetModal" tabindex="-1" aria-labelledby="widgetModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="widgetModalLabel">@lang('Select a Widget')</h5>
                                        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close" onclick="$('#widgetModal').modal('hide');">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="widget-grid">
                                            <button type="button" class="btn btn-outline--base add-widget widget-btn" data-key="URL">
                                                <i class="las la-link"></i>
                                                <span>@lang('Navigate to URL')</span>
                                                <small class="widget-description">@lang('Navigate to a specific webpage')</small>
                                            </button>
                                            <button type="button" class="btn btn-outline--base add-widget widget-btn" data-key="Wait">
                                                <i class="las la-clock"></i>
                                                <span>@lang('Wait')</span>
                                                <small class="widget-description">@lang('Pause for a specified time')</small>
                                            </button>
                                            <button type="button" class="btn btn-outline--base add-widget widget-btn" data-key="Scroll">
                                                <i class="las la-arrows-alt-v"></i>
                                                <span>@lang('Scroll')</span>
                                                <small class="widget-description">@lang('Scroll up or down on page')</small>
                                            </button>
                                            <button type="button" class="btn btn-outline--base add-widget widget-btn" data-key="Click">
                                                <i class="las la-mouse-pointer"></i>
                                                <span>@lang('Click')</span>
                                                <small class="widget-description">@lang('Click on page elements')</small>
                                            </button>
                                            <button type="button" class="btn btn-outline--base add-widget widget-btn" data-key="Refresh">
                                                <i class="las la-redo-alt"></i>
                                                <span>@lang('Refresh')</span>
                                                <small class="widget-description">@lang('Reload the current page')</small>
                                            </button>
                                            <button type="button" class="btn btn-outline--base add-widget widget-btn" data-key="LoadPageFull">
                                                <i class="las la-spinner"></i>
                                                <span>@lang('Wait For Page Load')</span>
                                                <small class="widget-description">@lang('Wait until page fully loads')</small>
                                            </button>
                                            <button type="button" class="btn btn-outline--base add-widget widget-btn" data-key="NavigateForward">
                                                <i class="las la-arrow-right"></i>
                                                <span>@lang('Navigate Forward')</span>
                                                <small class="widget-description">@lang('Go to next page in history')</small>
                                            </button>
                                            <button type="button" class="btn btn-outline--base add-widget widget-btn" data-key="NavigateBack">
                                                <i class="las la-arrow-left"></i>
                                                <span>@lang('Navigate Back')</span>
                                                <small class="widget-description">@lang('Go to previous page in history')</small>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <label class="form--label col-sm-12">
                                @lang('Delay After Each Visit')
                                <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Time to wait after completing each browsing session before starting the next one. This creates natural intervals between sessions, making traffic patterns appear more organic and avoiding server overload. Random delays within this range help simulate real user behavior."></i>
                            </label>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Min')<small class="text--base" style="font-size: 0.75rem;">@lang(' (in seconds)')</small></label>
                                <input type="number" name="min_task_delay" class="form--control" placeholder="Min Delay" min="0" step="1" value="{{ $order->min_task_delay ?? 10 }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Max')<small class="text--base" style="font-size: 0.75rem;">@lang(' (in seconds)')</small></label>
                                <input type="number" name="max_task_delay" class="form--control" placeholder="Max Delay" min="0" step="1" value="{{ $order->max_task_delay ?? 30 }}">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                @php
                                    $availableCredit = auth()->user()->bot_credit - auth()->user()->bot_used;
                                    $currentCampaignCredit = $order->speed ?? 0;
                                    $maxAllowed = $availableCredit + $currentCampaignCredit;
                                @endphp
                                <label class="form--label">@lang('Active Browsers')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Number of simultaneous browser sessions. Each active browser consumes 1 credit from your account. You can increase this up to your available credits plus currently allocated credits for this campaign."></i>
                                    <br><small class="text--base" style="font-size: 0.75rem;">
                                    <span class="credit-available">@lang('Available: '){{ $availableCredit }}</span> / 
                                    <span class="credit-used">@lang('Used: '){{ auth()->user()->bot_used }}</span> / 
                                    <span class="credit-total">@lang('Total: '){{ auth()->user()->bot_credit }}</span>
                                </small></label>
                                <input type="number" name="active_users" class="form-control me-2 w-50" placeholder="Active Browsers" min="1" max="{{ $maxAllowed }}" value="{{ $currentCampaignCredit }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="form--label">@lang('Max Visits')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Maximum number of page visits per browser session. Set to 0 for unlimited visits. Higher values may increase traffic but also resource usage."></i>
                                    <br><small class="text--base" style="font-size: 0.75rem;">@lang('Set 0 for unlimited visits')</small></label>
                                <input type="number" name="max_visits" class="form-control me-2 w-50" placeholder="Max Visits" min="0" value="{{ $order->quantity ?? 100 }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="form--label">@lang('Time Out')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Maximum time (in seconds) to wait for a page to load before timing out. Increase for slow-loading pages, decrease for faster processing."></i>
                                    <br><small class="text--base" style="font-size: 0.75rem;">@lang(' Page load timeout in seconds')</small></label>
                                <input type="number" name="timeout" class="form-control me-2 w-50" placeholder="Max Timeout" min="0" value="{{ $order->time_out ?? 60 }}">
                            </div>
                        </div>
                            </div>
                            <!-- End Campaign Details Tab -->

                            <!-- Advanced Configuration Tab -->
                            <div class="tab-pane fade" id="advanced-content" role="tabpanel" aria-labelledby="advanced-tab">
                                <div class="row">
                                    <div class="form-group col-12">
                                        <label class="form--label">
                                            @lang('HTTP Accept-Language')
                                            <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Enter language codes in order of preference. Use comma-separated values (e.g., en-US, en, fr) or one per line. This controls the Accept-Language header sent by the browser."></i>
                                        </label>
                                        <textarea class="form--control" name="accept_language" rows="2" placeholder="e.g., en-US, en, fr or one per line">{{ str_replace(',', "\n", $order->lang ?? 'en-US') }}</textarea>
                                        <small class="text-muted" style="font-size: 0.75rem;">
                                            @lang('Language codes reference:') <a href="https://www.w3.org/International/questions/qa-choosing-language-tags" target="_blank" rel="noopener noreferrer" class="text--base">W3C Language Tags</a>
                                        </small>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <label class="form--label col-12 mb-2">@lang('Resource Loading Configuration')</label>
                            @php
                                $configData = json_decode($order->config ?? '{}', true);
                            @endphp
                            <div class="form-group col-12 col-md">
                                <label class="form--label">@lang('Images')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleImages" class="btn {{ ($configData['image'] ?? 'disabled') === 'enabled' ? 'btn--base' : 'btn--primary' }} w-100">{{ ($configData['image'] ?? 'disabled') === 'enabled' ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="load_images" id="load_images" value="{{ ($configData['image'] ?? 'disabled') === 'enabled' ? 1 : 0 }}">
                                </div>
                            </div>
                            <div class="form-group col-12 col-md">
                                <label class="form--label">@lang('Videos')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleVideos" class="btn {{ ($configData['video'] ?? 'disabled') === 'enabled' ? 'btn--base' : 'btn--primary' }} w-100">{{ ($configData['video'] ?? 'disabled') === 'enabled' ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="load_videos" id="load_videos" value="{{ ($configData['video'] ?? 'disabled') === 'enabled' ? 1 : 0 }}">
                                </div>
                            </div>
                            <div class="form-group col-12 col-md">
                                <label class="form--label">@lang('CSS')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleCSS" class="btn {{ ($configData['css'] ?? 'enabled') === 'enabled' ? 'btn--base' : 'btn--primary' }} w-100">{{ ($configData['css'] ?? 'enabled') === 'enabled' ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="load_css" id="load_css" value="{{ ($configData['css'] ?? 'enabled') === 'enabled' ? 1 : 0 }}">
                                </div>
                            </div>
                            <div class="form-group col-12 col-md">
                                <label class="form--label">@lang('Font')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleFonts" class="btn {{ ($configData['font'] ?? 'enabled') === 'enabled' ? 'btn--base' : 'btn--primary' }} w-100">{{ ($configData['font'] ?? 'enabled') === 'enabled' ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="load_fonts" id="load_fonts" value="{{ ($configData['font'] ?? 'enabled') === 'enabled' ? 1 : 0 }}">
                                </div>
                            </div>
                            <div class="form-group col-12 col-md">
                                <label class="form--label">@lang('Scripts (JS)')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleScripts" class="btn {{ ($configData['script'] ?? 'enabled') === 'enabled' ? 'btn--base' : 'btn--primary' }} w-100">{{ ($configData['script'] ?? 'enabled') === 'enabled' ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="load_scripts" id="load_scripts" value="{{ ($configData['script'] ?? 'enabled') === 'enabled' ? 1 : 0 }}">
                                </div>                                </div>
                            </div>
                            </div>
                            <!-- End Advanced Configuration Tab -->

                            <!-- Proxy Settings Tab -->
                            <div class="tab-pane fade" id="proxy-content" role="tabpanel" aria-labelledby="proxy-tab">
                                <div class="row">
                                    <label class="form--label col-12 mb-2">@lang('Proxy Settings')</label>
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Premium Rotating Proxy')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Use our premium rotating proxy service. These are shared data center proxies that automatically rotate for each session. Choose your preferred location or use worldwide for maximum availability."></i>
                                </label>
                                
                                <div class="d-flex align-items-center mb-2">
                                    <button type="button" id="toggleFreeProxy" class="btn {{ ($order->proxy_type ?? 'custom') === 'free' ? 'btn--base' : 'btn--primary' }} w-100">{{ ($order->proxy_type ?? 'custom') === 'free' ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="free_proxy_enabled" id="free_proxy_enabled" value="{{ ($order->proxy_type ?? 'custom') === 'free' ? 1 : 0 }}">
                                </div>
                                <select id="freeProxyInput" class="form--control" name="free_proxy_country">
                                    @include('partials.proxy-options-selected', ['order' => $order])
                                </select>
                                {{-- JavaScript fallback to set the correct value after page load --}}
                                @php
                                    $detectedProxy = config('proxies.default');
                                    $portProxyMap = config('proxies.port_map');
                                    $domain = config('proxies.domain');
                                    
                                    if (!empty($order->proxy) && str_contains($order->proxy, $domain)) {
                                        foreach ($portProxyMap as $port => $type) {
                                            if (str_contains($order->proxy, ':' . $port)) {
                                                $detectedProxy = $type;
                                                break;
                                            }
                                        }
                                    }
                                @endphp
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var select = document.querySelector('#freeProxyInput');
                                        if (select) {
                                            select.value = '{{ $detectedProxy }}';
                                            //console.log('Set proxy dropdown to: {{ $detectedProxy }}');
                                        }
                                    });
                                </script>
                                <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">@lang('Premium: High-speed data centre proxies')</small>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Custom Proxy')
                                    <i class="las la-info-circle info-icon" data-toggle="tooltip" data-placement="top" title="Enter custom proxies in IP:PORT or IP:PORT:USERNAME:PASSWORD format. You can separate multiple proxies with commas, spaces, or new lines. Example: 192.168.1.1:8080, 10.0.0.1:3128"></i>
                                </label>
                                <div class="d-flex align-items-center mb-2">
                                    <button type="button" id="toggleCustomProxy" class="btn {{ ($order->proxy_type ?? 'custom') === 'custom' ? 'btn--base' : 'btn--primary' }} w-100">{{ ($order->proxy_type ?? 'custom') === 'custom' ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="custom_proxy_enabled" id="custom_proxy_enabled" value="{{ ($order->proxy_type ?? 'custom') === 'custom' ? 1 : 0 }}">
                                </div>
                                <small class="form--label text--base d-block mb-1" style="font-size: 0.75rem;">@lang('IP:PORT or IP:PORT:USERNAME:PASSWORD')</small>
                                <textarea id="customProxyInput" class="form--control" name="custom_proxies" rows="3" placeholder="Enter proxies separated by commas, spaces, or new lines&#10;Examples:&#10;192.168.1.1:8080, 10.0.0.1:3128&#10;192.168.1.1:8080 10.0.0.1:3128&#10;192.168.1.1:8080:user:pass">{{ $order->proxy && ($order->proxy_type ?? 'custom') === 'custom' ? str_replace(',', "\n", $order->proxy) : '' }}</textarea>
                                <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">@lang('Note: We do not support IP Authentication')</small>
                            </div>
                        </div>
                            </div>
                            <!-- End Proxy Settings Tab -->
                        </div>
                        <!-- End Tab Content -->
                        
                                                <!-- Update Button - Accessible from all tabs -->
                        <hr>
                        @if(in_array($order->status, [2, 4, 5]))
                            {{-- Hide update button for completed (2), cancelled (4), or expired (5) campaigns --}}
                        @elseif($user->bot_status != 1)
                        <div class="d-flex justify-content-center align-items-center">
                            <button id="createButton" class="btn btn-outline--base w-50 h-45 mt-3" type="submit" disabled>@lang('Update')</button>
                        </div>
                        @else
                        <div class="d-flex justify-content-center align-items-center">
                            <button id="createButton" class="btn btn--base w-50 h-45 mt-3" type="submit">@lang('Update')</button>
                        </div>
                        @endif
                    </form>
                    <div id="proxyError" class="alert alert-danger d-none mt-2">@lang('Please enable at least one proxy option.')</div>
                </div>
            </div>
        </div>
            @elseif ($user->bot_status == 0)
                <div class="col-lg-12 col-md-3 mb-4">
                    <div class="card card-deposit text-center">
                        <div class="card-body card-body-deposit">
                            {{--<img class="card-img-top" src="#" alt="Card image cap">--}}
                            <h5 class="card-title font-weight-bold text--danger">No Active Subscription Pack</h5>
                            <p class="card-text text--danger">Please subscribe to create a new campaign.</p>
                        </div>
                        <div class="card-footer">
                            <div class="d-grid gap-3 col-6 mx-auto">
                                <a href="{{ route('user.bot.buy') }}"
                                    class="btn  btn--base btn-block custom-success deposi orderBtn font-weight-bold"
                                    data-original-title="@lang('Subscribe')">
                                    @lang('Subscribe')
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="col-lg-12 col-md-3 mb-4">
                    <div class="card card-deposit text-center">
                        <div class="card-body card-body-deposit">
                            {{--<img class="card-img-top" src="#" alt="Card image cap">--}}
                            <h5 class="card-title font-weight-bold text--danger">@lang('You\'re out of browsers!')<br> @lang('You\'ve reached your browser limit for this subscription.')</h5>
                        </div>
                    </div>
                </div>
            @endif
    </div>
    
    <!-- Toast Notification Container -->
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
@stop
@push('script-lib')
<script src="{{ asset('assets/templates/basic/js/jquery-ui.min.js') }}"></script>
@endpush

@push('script')
<script>
    (function($) {
        "use strict";
        
        // Initialize Bootstrap modal on page load
        $(document).ready(function() {
            // Initialize all modals on the page
            if (typeof $.fn.modal === 'function') {
                $('.modal').modal({backdrop: true, keyboard: true, show: false});
            }
            
            // Initialize Bootstrap tooltips
            if (typeof $.fn.tooltip === 'function') {
                $('[data-toggle="tooltip"]').tooltip({
                    container: 'body',
                    trigger: 'hover focus',
                    delay: { show: 500, hide: 100 }
                });
            }

            // Tab Navigation - Remember active tab
            const activeTab = localStorage.getItem('activeCampaignTab');
            if (activeTab) {
                const tabTrigger = document.querySelector('#campaignTabs a[href="' + activeTab + '"]');
                if (tabTrigger) {
                    // Try Bootstrap 5 first
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                        const tab = new bootstrap.Tab(tabTrigger);
                        tab.show();
                    } 
                    // Fallback to Bootstrap 4
                    else if ($.fn.tab) {
                        $(tabTrigger).tab('show');
                    }
                }
            }

            // Save active tab to localStorage (Bootstrap 4 & 5 compatible)
            $('#campaignTabs a[data-toggle="pill"], #campaignTabs a[data-bs-toggle="pill"]').on('shown.bs.tab', function (e) {
                localStorage.setItem('activeCampaignTab', $(e.target).attr('href'));
            });

            // Also handle click events for tab navigation
            $('#campaignTabs a[data-toggle="pill"], #campaignTabs a[data-bs-toggle="pill"]').on('click', function (e) {
                e.preventDefault();
                const target = $(this).attr('href');
                
                // Hide all tab panes
                $('.tab-pane').removeClass('show active');
                
                // Show the target tab pane
                $(target).addClass('show active');
                
                // Update nav link states
                $('#campaignTabs a').removeClass('active').attr('aria-selected', 'false');
                $(this).addClass('active').attr('aria-selected', 'true');
                
                // Save to localStorage
                localStorage.setItem('activeCampaignTab', target);
            });

            // Clear localStorage on form submission
            $('#projectForm').on('submit', function() {
                localStorage.removeItem('activeCampaignTab');
            });

            // Handle validation errors - switch to relevant tab
            $('.is-invalid').each(function() {
                const invalidField = $(this);
                const tabPane = invalidField.closest('.tab-pane');
                if (tabPane.length) {
                    const tabId = tabPane.attr('id');
                    $('#campaignTabs a[href="#' + tabId + '"]').tab('show');
                    return false; // Show only the first tab with errors
                }
            });
            
            // Fallback for tooltips if Bootstrap tooltip is not available
            if (!$.fn.tooltip) {
                $('[data-toggle="tooltip"]').each(function() {
                    const $this = $(this);
                    const title = $this.attr('title');
                    if (title) {
                        $this.attr('data-original-title', title);
                        $this.removeAttr('title');
                        
                        // Simple hover tooltip fallback
                        $this.hover(
                            function() {
                                const tooltip = $('<div class="simple-tooltip">' + title + '</div>');
                                $('body').append(tooltip);
                                const offset = $(this).offset();
                                tooltip.css({
                                    position: 'absolute',
                                    top: offset.top - 35,
                                    left: offset.left,
                                    background: '#333',
                                    color: '#fff',
                                    padding: '5px 10px',
                                    borderRadius: '4px',
                                    fontSize: '12px',
                                    zIndex: 1000,
                                    maxWidth: '200px',
                                    wordWrap: 'break-word'
                                }).fadeIn(200);
                            },
                            function() {
                                $('.simple-tooltip').fadeOut(200, function() {
                                    $(this).remove();
                                });
                            }
                        );
                    }
                });
            }
            
            // Min/Max validation for timing fields
            function validateMinMaxFields() {
                const timingPairs = [
                    { min: 'min_delay', max: 'max_delay', name: 'Wait Time After Visiting Initial URL' },
                    { min: 'min_task_delay', max: 'max_task_delay', name: 'Delay After Each Visit' }
                ];
                
                timingPairs.forEach(function(pair) {
                    const minInput = $(`input[name="${pair.min}"]`);
                    const maxInput = $(`input[name="${pair.max}"]`);
                    
                    function validatePair() {
                        const minVal = parseFloat(minInput.val()) || 0;
                        const maxVal = parseFloat(maxInput.val()) || 0;
                        
                        // Remove any existing validation errors for this pair
                        minInput.removeClass('is-invalid');
                        maxInput.removeClass('is-invalid');
                        $(`.${pair.min}-error, .${pair.max}-error`).remove();
                        
                        if (minVal > maxVal && maxVal > 0) {
                            minInput.addClass('is-invalid');
                            maxInput.addClass('is-invalid');
                            
                            const errorMsg = `<div class="invalid-feedback ${pair.min}-error ${pair.max}-error">Min value cannot be greater than max value for ${pair.name}</div>`;
                            
                            // Add error message after the max input
                            if (!maxInput.siblings('.invalid-feedback').length) {
                                maxInput.after(errorMsg);
                            }
                            
                            return false;
                        }
                        return true;
                    }
                    
                    // Validate on input change
                    minInput.on('input change', validatePair);
                    maxInput.on('input change', validatePair);
                    
                    // Auto-adjust max value if min becomes higher
                    minInput.on('change', function() {
                        const minVal = parseFloat($(this).val()) || 0;
                        const maxVal = parseFloat(maxInput.val()) || 0;
                        
                        if (minVal > maxVal && minVal > 0) {
                            maxInput.val(minVal);
                            showToast(`Max ${pair.name.toLowerCase()} adjusted to match min value`, 'warning');
                        }
                    });
                });
            }
            
            // Initialize min/max validation
            validateMinMaxFields();
            
            // Enhanced form submission validation
            $('#projectForm').on('submit', function(e) {
                const timingPairs = [
                    { min: 'min_delay', max: 'max_delay', name: 'Wait Time After Visiting Initial URL' },
                    { min: 'min_task_delay', max: 'max_task_delay', name: 'Delay After Each Visit' }
                ];
                
                let hasErrors = false;
                
                timingPairs.forEach(function(pair) {
                    const minVal = parseFloat($(`input[name="${pair.min}"]`).val()) || 0;
                    const maxVal = parseFloat($(`input[name="${pair.max}"]`).val()) || 0;
                    
                    if (minVal > maxVal && maxVal > 0) {
                        showToast(`Min value cannot be greater than max value.`, 'error');
                        hasErrors = true;
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Manual modal trigger for add-widget-btn
            $(document).on('click', '.add-widget-btn', function(e) {
                e.preventDefault();
                $('#widgetModal').modal('show');
            });
            
            // ====================================================================
            // CHANGE DETECTION SYSTEM - Enable Update Button Only On Changes
            // ====================================================================
            
            let originalFormState = null;
            let isFormChanged = false;
            
            // Function to serialize form state for comparison
            function getFormState() {
                const formData = {};
                
                // Get all input, select, and textarea values
                $('#projectForm').find('input, select, textarea').each(function() {
                    const $field = $(this);
                    const name = $field.attr('name');
                    const type = $field.attr('type');
                    
                    if (!name) return; // Skip fields without names
                    
                    if (type === 'checkbox' || type === 'radio') {
                        formData[name] = $field.is(':checked');
                    } else if ($field.is('select[multiple]')) {
                        formData[name] = $field.val() || [];
                    } else {
                        formData[name] = $field.val() || '';
                    }
                });
                
                // Include toggle button states (proxy, extensions, resources)
                formData['_toggle_free_proxy'] = $('#toggleFreeProxy').hasClass('btn--base');
                formData['_toggle_custom_proxy'] = $('#toggleCustomProxy').hasClass('btn--base');
                formData['_toggle_referral'] = $('#toggleReferral').hasClass('btn--base');
                formData['_toggle_search'] = $('#toggleSearch').hasClass('btn--base');
                formData['_toggle_images'] = $('#toggleImages').hasClass('btn--base');
                formData['_toggle_videos'] = $('#toggleVideos').hasClass('btn--base');
                formData['_toggle_css'] = $('#toggleCSS').hasClass('btn--base');
                formData['_toggle_fonts'] = $('#toggleFonts').hasClass('btn--base');
                formData['_toggle_scripts'] = $('#toggleScripts').hasClass('btn--base');
                formData['_toggle_url_order'] = $('#toggleUrlOrder').hasClass('btn--base');
                
                // Include widget list state (order and content)
                const widgetState = [];
                $('#page_sections li:not(.empty-state)').each(function() {
                    const $widget = $(this);
                    const widgetData = {
                        key: $widget.data('key'),
                        inputs: {}
                    };
                    
                    $widget.find('input, select').each(function() {
                        const $input = $(this);
                        const inputName = $input.attr('name');
                        if (inputName) {
                            widgetData.inputs[inputName] = $input.val() || '';
                        }
                    });
                    
                    widgetState.push(widgetData);
                });
                formData['_widget_state'] = JSON.stringify(widgetState);
                
                return JSON.stringify(formData);
            }
            
            // Function to compare form states
            function checkFormChanges() {
                const currentState = getFormState();
                isFormChanged = (currentState !== originalFormState);
                
                // Update button state based on changes
                updateButtonState();
                
                // Visual feedback
                if (isFormChanged) {
                    $('#createButton').removeClass('btn-outline--base').addClass('btn--base');
                } else {
                    $('#createButton').removeClass('btn--base').addClass('btn-outline--base');
                }
                
                return isFormChanged;
            }
            
            // Function to update button enabled/disabled state
            function updateButtonState() {
                const hasCredit = {{ $user->bot_credit > $user->bot_used ? 'true' : 'false' }};
                const hasCreditError = $('input[name="active_users"]').hasClass('is-invalid');
                const hasProxyError = $('#custom_proxy_enabled').val() == 1 && $('#customProxyInput').val().trim() === '';
                const hasValidationError = !hasCredit || hasCreditError || hasProxyError;
                
                // Only enable button if:
                // 1. Form has changes AND
                // 2. No validation errors AND
                // 3. User is allowed to update (not completed/cancelled/expired)
                const shouldEnable = isFormChanged && !hasValidationError;
                
                if (shouldEnable) {
                    $('#createButton').prop('disabled', false).removeClass('btn--disabled');
                } else {
                    $('#createButton').prop('disabled', true).addClass('btn--disabled');
                }
            }
            
            // Capture original form state after page loads
            setTimeout(function() {
                originalFormState = getFormState();
                
                // Disable button initially (no changes yet)
                @if(!in_array($order->status, [2, 4, 5]) && $user->bot_status == 1)
                $('#createButton').prop('disabled', true).addClass('btn--disabled').removeClass('btn--base').addClass('btn-outline--base');
                @endif
                
                // Monitor all form changes
                $('#projectForm').on('input change', 'input, select, textarea', function() {
                    checkFormChanges();
                });
                
                // Monitor toggle button clicks
                $(document).on('click', '#toggleFreeProxy, #toggleCustomProxy, #toggleReferral, #toggleSearch, #toggleImages, #toggleVideos, #toggleCSS, #toggleFonts, #toggleScripts, #toggleUrlOrder', function() {
                    setTimeout(checkFormChanges, 50);
                });
                
                // Monitor widget additions/deletions/reordering
                const widgetObserver = new MutationObserver(function() {
                    setTimeout(checkFormChanges, 100);
                });
                
                const widgetContainer = document.getElementById('page_sections');
                if (widgetContainer) {
                    widgetObserver.observe(widgetContainer, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['value', 'class']
                    });
                }
                
                // Monitor sortable reordering
                if (typeof $.ui !== 'undefined' && $('#page_sections').length) {
                    $('#page_sections').on('sortstop', function() {
                        setTimeout(checkFormChanges, 100);
                    });
                }
            }, 1000); // Wait for all dynamic content to load
            
            // Reset change detection after successful form submission
            $('#projectForm').on('submit', function() {
                if (isFormChanged) {
                    // Form is being submitted with changes
                    localStorage.setItem('formSubmitted', 'true');
                }
            });
        });
        
        // Toast notification function
        function showToast(message, type = 'warning') {
            // Define color schemes for each type
            const backgrounds = {
                'warning': '#fff3cd',
                'error': '#f8d7da',
                'success': '#d1e7dd'
            };
            
            const borders = {
                'warning': '#ffc107',
                'error': '#dc3545',
                'success': '#198754'
            };
            
            const textColors = {
                'warning': '#856404',
                'error': '#721c24',
                'success': '#0f5132'
            };
            
            const icons = {
                'warning': 'la-exclamation-triangle',
                'error': 'la-times-circle',
                'success': 'la-check-circle'
            };
            
            const toast = $(`
                <div class="toast-notification toast-${type}" style="margin-bottom: 10px; padding: 12px 20px; background: ${backgrounds[type]}; border: 1px solid ${borders[type]}; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; min-width: 300px; animation: slideIn 0.3s ease;">
                    <i class="las ${icons[type]}" style="font-size: 1.5rem; color: ${textColors[type]}; margin-right: 10px;"></i>
                    <span style="color: ${textColors[type]}; flex: 1; font-size: 0.9rem;">${message}</span>
                    <button type="button" class="toast-close" style="background: none; border: none; color: ${textColors[type]}; font-size: 1.2rem; cursor: pointer; padding: 0; margin-left: 10px; opacity: 0.7;">&times;</button>
                </div>
            `);
            
            $('#toast-container').append(toast);
            
            // Auto remove after 4 seconds
            setTimeout(() => {
                toast.fadeOut(300, function() { $(this).remove(); });
            }, 4000);
            
            // Manual close
            toast.find('.toast-close').on('click', function() {
                toast.fadeOut(300, function() { $(this).remove(); });
            });
        }
        
        // Toggle logic for URL order
        $('#toggleUrlOrder').click(function() {
            if ($(this).hasClass('btn--primary')) {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Random');
                $('#urlOrderInput').val('random');
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('One by One');
                $('#urlOrderInput').val('one_by_one');
            }
        });

        // Toggle logic for referral and search extensions
        $('#toggleReferral').click(function() {
            if ($(this).hasClass('btn--primary')) {
                if ($('#toggleSearch').hasClass('btn--base')) {
                    showToast('Please disable Search Extension to enable Referrer Extension.', 'warning');
                } else {
                    $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                    $('#referrer_enabled').val(1);
                }
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#referrer_enabled').val(0);
            }
        });

        $('#toggleSearch').click(function() {
            if ($(this).hasClass('btn--primary')) {
                if ($('#toggleReferral').hasClass('btn--base')) {
                    showToast('Please disable Referrer Extension to enable Search Extension.', 'warning');
                } else {
                    $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                    $('#search_enabled').val(1);
                }
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#search_enabled').val(0);
            }
        });

        // Toggle logic for proxy settings
        $('#toggleFreeProxy').click(function() {
            if ($(this).hasClass('btn--primary')) {
                // Enabling Free Proxy
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#free_proxy_enabled').val(1);
                $('#freeProxyInput').attr('required', true);
                
                // Auto-disable Custom Proxy if it's enabled
                if ($('#toggleCustomProxy').hasClass('btn--base')) {
                    $('#toggleCustomProxy').removeClass('btn--base').addClass('btn--primary').text('Disabled');
                    $('#custom_proxy_enabled').val(0);
                    $('#customProxyInput').removeAttr('required');
                    // Clear custom proxy validation errors
                    $('#proxyValidationError').remove();
                    showToast('Free Proxy is now enabled.', 'success');
                }
                
                // Re-enable create button when switching to free proxy (if user has credit and no other errors)
                const hasCredit = {{ $user->bot_credit > $user->bot_used ? 'true' : 'false' }};
                const hasCreditError = $('input[name="active_users"]').hasClass('is-invalid');
                if (hasCredit && !hasCreditError) {
                    // Check if form has changes before enabling
                    if (typeof checkFormChanges === 'function') {
                        setTimeout(checkFormChanges, 50);
                    } else {
                        $('#createButton').prop('disabled', false).removeClass('btn--disabled');
                    }
                }
            } else {
                // Disabling Free Proxy - check if Custom Proxy is also disabled
                if ($('#toggleCustomProxy').hasClass('btn--primary')) {
                    showToast('At least one proxy option must be enabled.', 'warning');
                    return false;
                }
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#free_proxy_enabled').val(0);
                $('#freeProxyInput').removeAttr('required');
            }
        });

        $('#toggleCustomProxy').click(function() {
            if ($(this).hasClass('btn--primary')) {
                // Enabling Custom Proxy
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#custom_proxy_enabled').val(1);
                $('#customProxyInput').attr('required', true);
                
                // Auto-disable Free Proxy if it's enabled
                if ($('#toggleFreeProxy').hasClass('btn--base')) {
                    $('#toggleFreeProxy').removeClass('btn--base').addClass('btn--primary').text('Disabled');
                    $('#free_proxy_enabled').val(0);
                    $('#freeProxyInput').removeAttr('required');
                    showToast('Custom Proxy is now enabled.', 'success');
                }
                
                // Check proxy validation when enabling custom proxy
                // This will check if field is empty and disable button accordingly
                setTimeout(function() {
                    $('#customProxyInput').trigger('input');
                }, 100);
            } else {
                // Disabling Custom Proxy - check if Free Proxy is also disabled
                if ($('#toggleFreeProxy').hasClass('btn--primary')) {
                    showToast('At least one proxy option must be enabled.', 'warning');
                    return false;
                }
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#custom_proxy_enabled').val(0);
                $('#customProxyInput').removeAttr('required');
                
                // Clear validation errors and re-enable create button when custom proxy is disabled (if user has credit and no other errors)
                $('#proxyValidationError').remove();
                const hasCredit = {{ $user->bot_credit > $user->bot_used ? 'true' : 'false' }};
                const hasCreditError = $('input[name="active_users"]').hasClass('is-invalid');
                if (hasCredit && !hasCreditError) {
                    // Check if form has changes before enabling
                    if (typeof checkFormChanges === 'function') {
                        setTimeout(checkFormChanges, 50);
                    } else {
                        $('#createButton').prop('disabled', false).removeClass('btn--disabled');
                    }
                }
            }
        });

        // Toggle logic for resource loading configuration
        $('#toggleImages').click(function() {
            if ($(this).hasClass('btn--primary')) {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#load_images').val(1);
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#load_images').val(0);
            }
        });

        $('#toggleVideos').click(function() {
            if ($(this).hasClass('btn--primary')) {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#load_videos').val(1);
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#load_videos').val(0);
            }
        });

        $('#toggleCSS').click(function() {
            if ($(this).hasClass('btn--primary')) {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#load_css').val(1);
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#load_css').val(0);
            }
        });

        $('#toggleFonts').click(function() {
            if ($(this).hasClass('btn--primary')) {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#load_fonts').val(1);
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#load_fonts').val(0);
            }
        });

        $('#toggleScripts').click(function() {
            if ($(this).hasClass('btn--primary')) {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#load_scripts').val(1);
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
                $('#load_scripts').val(0);
            }
        });
        
        function getSectionKeys() {
            return $(document).find('#page_sections input[name="secs[]"]').map(function() {
                return $(this).val();
            }).get();
        }

        function addInputField(key, index) {
            let inputFields = '';
            if (key === 'URL') {
                inputFields = `<input type="url" name="url[${index}]" class="form-control mt-2" placeholder="https://example.com" pattern="https?://.+">`;
            } else if (key === 'Wait') {
                inputFields = `<div class="d-flex mt-2"><input type="number" name="min_wait[${index}]" class="form-control me-2" placeholder="Min (s)"> \
                               <input type="number" name="max_wait[${index}]" class="form-control" placeholder="Max (s)"></div>`;
            } else if (key === 'Scroll') {
                inputFields = `<div class="scroll-widget-inputs mt-2">\
                                <select name="scroll_type[${index}]" class="form-control scroll-input-item">\
                                    <option value="Random">Random</option>\
                                    <option value="Up">Up</option>\
                                    <option value="Down">Down</option>\
                                </select>\
                                <input type="number" name="scroll_percentage[${index}]" class="form-control scroll-input-item" placeholder="Depth %" min="0" max="100">\
                                <input type="number" name="scroll_count[${index}]" class="form-control scroll-input-item" placeholder="No of Times" min="1">\
                                <input type="number" name="scroll_delay[${index}]" class="form-control scroll-input-item" placeholder="Delay (s)" min="0" step="1">\
                              </div>`;
            } else if (key === 'Click') {
                inputFields = `<div class="d-flex justify-content-between mt-2">\
                                <select name="click_type[${index}]" class="form-control me-2">\
                                    <option value="Internal">Internal</option>\
                                    <option value="External">External</option>\
                                    <option value="Random">Random</option>\
                                </select>\
                                <input type="number" name="click_percentage[${index}]" class="form-control" placeholder=" Click-Through Rate" min="0" max="100">\
                              </div>`;
            } else if (key === 'Refresh' || key === 'NavigateForward' || key === 'NavigateBack' || key === 'LoadPageFull') {
                inputFields = `<input type="hidden" name="${key.toLowerCase()}[${index}]" value="Enabled">`;
            }
            return inputFields;
        }

        $(document).on('click', '.add-widget', function() {
            const key = $(this).data('key');
            const widgetText = $(this).find('span').text();
            const index = $('#page_sections li:not(.empty-state)').length;
            const inputFields = addInputField(key, index);
            const element = $(`<li class="highlight icon-move" data-key="${key}">
                <div class="d-flex align-items-center mb-2">
                    <i class="sortable-icon la la-arrows-alt-v" title="Drag to reorder"></i>
                    <span class="d-inline-block me-auto text--base flex-grow-1">${widgetText}</span>
                    <button type="button" class="ms-auto d-inline-block remove-icon remove-icon-color btn-remove" style="border: none; background: none; padding: 0;" title="Delete widget">
                        <i class="la la-trash"></i>
                    </button>
                </div>
                ${inputFields}
                <input type="hidden" name="secs[]" value="${key}">
                <input type="hidden" name="order[]" value="${index}">
            </li>`);
            
            // Remove empty state if it exists and append new element
            $('#page_sections .empty-state').remove();
            $('#page_sections').append(element);
            
            // Add real-time validation for URL inputs
            if (key === 'URL') {
                element.find('input[name^="url["]').on('input blur', function() {
                    const urlValue = $(this).val().trim();
                    const urlPattern = /^https?:\/\/.+/;
                    
                    if (urlValue === '') {
                        $(this).removeClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                    } else if (!urlPattern.test(urlValue)) {
                        $(this).addClass('is-invalid');
                        if ($(this).next('.invalid-feedback').length === 0) {
                            $(this).after('<div class="invalid-feedback d-block">Please enter a valid URL starting with http:// or https://</div>');
                        }
                    } else {
                        $(this).removeClass('is-invalid');
                        $(this).next('.invalid-feedback').remove();
                    }
                });
            }
            
            // Bind delete event immediately to the new element
            element.find('.btn-remove').on('click', function(e) {
                e.stopPropagation();
                e.preventDefault();
                $(this).closest('li.highlight').remove();
                watchState();
                reindexSections();
                return false;
            });
            
            // Close the modal after adding widget
            $('#widgetModal').modal('hide');
            
            watchState();
            // Reinitialize sortable after adding new widget
            initializeSortable();
        });

        // Test sortable icon click detection (including touch events for mobile)
        $(document).on('mousedown touchstart', '.sortable-icon', function(e) {
            // Sortable icon interaction detected
        });

        // Enhanced mobile drag-and-drop fix with touch-to-mouse event simulation
        let isDragging = false;
        let dragStarted = false;
        let startY = 0;
        let startX = 0;
        let currentElement = null;
        const dragThreshold = 15;
        
        // Enhanced touch-to-mouse event simulation for jQuery UI sortable
        function simulateMouseEvent(type, touch, target) {
            try {
                // Create a native-like mouse event first
                const nativeEvent = new MouseEvent(type, {
                    bubbles: true,
                    cancelable: true,
                    view: window,
                    detail: 0,
                    screenX: touch.screenX || touch.clientX,
                    screenY: touch.screenY || touch.clientY,
                    clientX: touch.clientX,
                    clientY: touch.clientY,
                    pageX: touch.pageX || touch.clientX,
                    pageY: touch.pageY || touch.clientY,
                    button: 0,
                    buttons: 1,
                    which: 1
                });
                
                // Create a proper jQuery event with native event as base
                const event = $.Event(nativeEvent);
                
                // Ensure all required properties are set
                event.type = type;
                event.which = 1;
                event.button = 0;
                event.buttons = 1;
                event.pageX = touch.pageX || touch.clientX;
                event.pageY = touch.pageY || touch.clientY;
                event.clientX = touch.clientX;
                event.clientY = touch.clientY;
                event.screenX = touch.screenX || touch.clientX;
                event.screenY = touch.screenY || touch.clientY;
                event.target = target;
                event.currentTarget = target;
                
                // Set the originalEvent to the native event
                event.originalEvent = nativeEvent;
                
                // Trigger the event using jQuery
                $(target).trigger(event);
                
                return event;
            } catch (error) {
                //console.error('Error simulating mouse event:', error);
                return null;
            }
        }
        
        $(document).on('touchstart', '.highlight', function(e) {
            const touch = e.originalEvent.touches[0];
            const target = e.currentTarget;
            
            // Don't handle drag if touching delete button
            const touchTarget = document.elementFromPoint(touch.clientX, touch.clientY);
            if ($(touchTarget).closest('.btn-remove, .remove-icon').length) {
                return;
            }
            
            // Only handle if touching the sortable handle or if no handle exists
            const sortableHandle = $(target).find('.sortable-icon')[0];
            
            if (sortableHandle && !$(touchTarget).closest('.sortable-icon').length) {
                return;
            }
            
            startX = touch.clientX;
            startY = touch.clientY;
            currentElement = target;
            isDragging = false;
            dragStarted = false;
            
            // Add immediate visual feedback with enhanced highlighting
            $(target).addClass('touch-active');
            
            // Add haptic feedback if available
            if (navigator.vibrate) {
                navigator.vibrate(50); // Light vibration for touch feedback
            }
            
            // Prevent text selection and context menu
            e.preventDefault();
        });
        
        $(document).on('touchmove', '.highlight', function(e) {
            if (!currentElement || currentElement !== e.currentTarget) return;
            
            const touch = e.originalEvent.touches[0];
            const deltaX = Math.abs(touch.clientX - startX);
            const deltaY = Math.abs(touch.clientY - startY);
            const totalDelta = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
            
            // Progressive visual feedback as user moves finger
            if (totalDelta > 5 && !dragStarted) {
                // Add "preparing to drag" visual feedback
                $(currentElement).addClass('touch-preparing-drag');
            }
            
            if (totalDelta > dragThreshold && !dragStarted) {
                dragStarted = true;
                isDragging = true;
                
                // Remove preparation class and add dragging class
                $(currentElement).removeClass('touch-preparing-drag').addClass('touch-dragging');
                
                // Stronger haptic feedback for drag start
                if (navigator.vibrate) {
                    navigator.vibrate([50, 30, 50]); // Pattern for drag start
                }
                
                // Prevent default scrolling
                e.preventDefault();
                e.stopPropagation();
                
                // Simulate mouse events to trigger jQuery UI sortable
                const targetElement = $(currentElement).find('.sortable-icon')[0] || currentElement;
                simulateMouseEvent('mousedown', touch, targetElement);
                
                // Lock scroll to script section on mobile during drag
                $('body').addClass('mobile-drag-active');
                
                // Store current scroll position and lock page scroll
                window.dragScrollTop = $(window).scrollTop();
                
                // Enable scroll lock for script section only
                const $scriptSection = $('#page_sections').closest('.card-body');
                $scriptSection.addClass('script-scroll-locked');
                
                // Disable page scroll, enable script scroll only
                $('html, body').addClass('page-scroll-locked');
                
                return false;
            } else if (isDragging) {
                // Continue drag simulation
                e.preventDefault();
                simulateMouseEvent('mousemove', touch, currentElement);
                return false;
            }
        });
        
        $(document).on('touchend', '.highlight', function(e) {
            if (!currentElement || currentElement !== e.currentTarget) return;
            
            if (isDragging) {
                // Complete the drag
                const touch = e.originalEvent.changedTouches[0];
                simulateMouseEvent('mouseup', touch, currentElement);
                
                // Clean up dragging state and restore scroll
                $(currentElement).removeClass('touch-dragging');
                $('body').removeClass('mobile-drag-active');
                
                // Restore scroll functionality
                const $scriptSection = $('#page_sections').closest('.card-body');
                $scriptSection.removeClass('script-scroll-locked');
                $('html, body').removeClass('page-scroll-locked');
                
                // Success haptic feedback
                if (navigator.vibrate) {
                    navigator.vibrate(100); // Success vibration
                }
            }
            
            // Clean up all touch states with a smooth transition
            $(currentElement).removeClass('touch-active touch-preparing-drag touch-dragging');
            
            // Add a brief "release" effect
            $(currentElement).addClass('touch-release');
            setTimeout(() => {
                $(currentElement).removeClass('touch-release');
            }, 200);
            
            currentElement = null;
            isDragging = false;
            dragStarted = false;
        });
        
        // Handle touch cancel (when drag is interrupted)
        $(document).on('touchcancel', '.highlight', function(e) {
            if (!currentElement || currentElement !== e.currentTarget) return;
            
            // Clean up all states immediately
            $(currentElement).removeClass('touch-active touch-preparing-drag touch-dragging');
            $('body').removeClass('mobile-drag-active ui-sortable-dragging');
            
            // Restore scroll functionality
            const $scriptSection = $('#page_sections').closest('.card-body');
            $scriptSection.removeClass('script-scroll-locked');
            $('html, body').removeClass('page-scroll-locked');
            
            // Reset variables
            currentElement = null;
            isDragging = false;
            dragStarted = false;
        });

        // Function to initialize or reinitialize sortable
        function initializeSortable() {
            // Wait for DOM to be ready and ensure jQuery UI is available
            $(document).ready(function() {
                setTimeout(function() {
                    // Check if jQuery UI is loaded
                    if (typeof $.ui === 'undefined' || typeof $.ui.sortable === 'undefined') {
                        return;
                    }
                    
                    // Destroy existing sortable if it exists
                    try {
                        if ($("#page_sections").hasClass('ui-sortable')) {
                            $("#page_sections").sortable('destroy');
                        }
                    } catch(e) {
                        // No existing sortable to destroy
                    }
                    
                    // Detect if we're on mobile
                    const isMobile = window.innerWidth <= 768 || /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                    
                    // Initialize sortable with mobile-optimized configuration
                    try {
                        const sortableConfig = {
                            items: "li:not(.empty-state)",
                            handle: isMobile ? false : ".sortable-icon", // Disable handle on mobile for touch simulation
                            cancel: ".btn-remove, input, select, textarea",
                            placeholder: "sortable-placeholder",
                            cursor: "move",
                            opacity: 0.8,
                            tolerance: "intersect", // Use intersect for better placeholder positioning
                            scroll: !isMobile, // Disable scroll on mobile to prevent conflicts
                            scrollSensitivity: 100,
                            scrollSpeed: 20,
                            // Remove containment to allow proper placeholder positioning
                            axis: "y", // Only allow vertical movement
                            forceHelperSize: true,
                            forcePlaceholderSize: true, // Ensure placeholder maintains proper size
                            update: function(event, ui) {
                                // Prevent scroll restoration in reindexSections since we handle it in stop handler
                                reindexSections(true);
                            },
                            start: function(event, ui) {
                                ui.item.addClass('ui-sortable-dragging');
                                
                                // Enhanced placeholder with proper sizing and visibility
                                ui.placeholder.height(ui.item.outerHeight());
                                ui.placeholder.width(ui.item.outerWidth());
                                ui.placeholder.html('<div class="sortable-placeholder-content">Drop here</div>');
                                ui.placeholder.addClass('sortable-placeholder-active');
                                
                                // Ensure placeholder is visible and positioned correctly
                                ui.placeholder.css({
                                    'display': 'block',
                                    'visibility': 'visible',
                                    'opacity': '1'
                                });
                                
                                // Prevent body scrolling during drag on mobile and lock to script
                                if (isMobile) {
                                    $('body').addClass('ui-sortable-dragging mobile-drag-active');
                                    
                                    // Lock scroll to script section
                                    window.dragScrollTop = $(window).scrollTop();
                                    const $scriptSection = $('#page_sections').closest('.card-body');
                                    $scriptSection.addClass('script-scroll-locked');
                                    $('html, body').addClass('page-scroll-locked');
                                }
                            },
                            stop: function(event, ui) {
                                ui.item.removeClass('ui-sortable-dragging touch-dragging');
                                
                                // Store the moved item's position and calculate proper scroll restoration
                                let targetScrollPosition = null;
                                
                                // Calculate ideal scroll position to show the moved widget
                                // We need to calculate this AFTER scroll locks are removed to get accurate position
                                if (isMobile) {
                                    // For mobile, we'll calculate scroll position in the callback after unlocking
                                    // Store item index for later calculation
                                    const movedItemIndex = ui.item.index();
                                } else {
                                    // For desktop, calculate immediately since no scroll lock is active
                                    const movedItemOffset = ui.item.offset();
                                    if (movedItemOffset) {
                                        const viewportHeight = $(window).height();
                                        
                                        // Position the moved item in the upper third of the viewport
                                        targetScrollPosition = movedItemOffset.top - (viewportHeight / 3);
                                        
                                        // Ensure we don't scroll past the top of the document
                                        targetScrollPosition = Math.max(0, targetScrollPosition);
                                    }
                                }
                                
                                // Restore body scrolling on mobile and unlock scroll
                                if (isMobile) {
                                    $('body').removeClass('ui-sortable-dragging mobile-drag-active');
                                    
                                    // Remove scroll lock classes first, then calculate and scroll
                                    const $scriptSection = $('#page_sections').closest('.card-body');
                                    $scriptSection.removeClass('script-scroll-locked');
                                    $('html, body').removeClass('page-scroll-locked');
                                    
                                    // Calculate scroll position AFTER removing locks to get accurate measurements
                                    setTimeout(() => {
                                        const movedItemOffset = ui.item.offset();
                                        if (movedItemOffset) {
                                            const viewportHeight = $(window).height();
                                            
                                            // Position the moved item in the upper third of the viewport
                                            const calculatedScrollPosition = movedItemOffset.top - (viewportHeight / 3);
                                            
                                            // Ensure we don't scroll past the top of the document
                                            const finalScrollPosition = Math.max(0, calculatedScrollPosition);
                                            
                                            // Animate to the target position with smooth scrolling
                                            $('html, body').animate({
                                                scrollTop: finalScrollPosition
                                            }, 400, 'swing');
                                        }
                                    }, 100); // Allow time for DOM to settle after removing locks
                                    
                                } else {
                                    // Desktop: Simple immediate scroll restoration
                                    if (targetScrollPosition !== null) {
                                        setTimeout(() => {
                                            $(window).scrollTop(targetScrollPosition);
                                        }, 50);
                                    }
                                }
                                
                                // Success feedback removed - no green highlighting after move
                            },
                            sort: function(event, ui) {
                                // Provide visual feedback during sort
                                if (isMobile) {
                                    ui.helper.addClass('mobile-sorting');
                                }
                                
                                // Ensure placeholder remains visible and properly positioned
                                if (ui.placeholder) {
                                    ui.placeholder.css({
                                        'display': 'block',
                                        'visibility': 'visible',
                                        'opacity': '1'
                                    });
                                }
                            },
                            change: function(event, ui) {
                                // Called when the placeholder changes position
                                // Ensure placeholder styling is maintained
                                ui.placeholder.addClass('sortable-placeholder-active');
                                ui.placeholder.css({
                                    'display': 'block',
                                    'visibility': 'visible',
                                    'opacity': '1'
                                });
                            }
                        };
                        
                        // Add mobile-specific configurations
                        if (isMobile) {
                            sortableConfig.delay = 200; // Longer delay for mobile
                            sortableConfig.distance = 15; // More distance required to start drag
                            // tolerance already set to "intersect" above for both mobile and desktop
                        }
                        
                        $("#page_sections").sortable(sortableConfig);
                        
                    } catch(error) {
                        //console.error('Error initializing sortable:', error);
                    }
                }, 100); // Small delay to ensure everything is loaded
            });
        }

        // Initialize sortable on page load
        initializeSortable();
        
        // Additional mobile debugging and fallback
        if (window.innerWidth <= 768) {
            // Add mobile-specific event listeners
            $(document).on('touchstart', '#page_sections', function(e) {
                // Touch start on sortable container
            });
            
            $(document).on('touchmove', '#page_sections', function(e) {
                // Only prevent if we're actively dragging
                if ($('body').hasClass('mobile-drag-active')) {
                    e.preventDefault();
                }
            });
            
            // Prevent scrolling outside script area during drag mode
            $(document).on('touchmove', function(e) {
                if ($('body').hasClass('mobile-drag-active')) {
                    // Allow scroll only within script section
                    const $target = $(e.target);
                    const $scriptSection = $('#page_sections').closest('.card-body');
                    
                    // If touch is outside script section during drag, prevent default
                    if (!$target.closest($scriptSection).length) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
            
            // Visual feedback for scroll lock engagement
            $(document).on('touchstart', function(e) {
                if ($('body').hasClass('mobile-drag-active')) {
                    const $target = $(e.target);
                    const $scriptSection = $('#page_sections').closest('.card-body');
                    
                    // Show feedback if touching outside script area
                    if (!$target.closest($scriptSection).length) {
                        // Brief visual indication that scrolling is locked
                        $('body').addClass('scroll-lock-feedback');
                        setTimeout(() => {
                            $('body').removeClass('scroll-lock-feedback');
                        }, 300);
                    }
                }
            });
            
            // Force enable touch events for sortable items
            $('#page_sections').css({
                'touch-action': 'none',
                '-webkit-touch-callout': 'none',
                '-webkit-user-select': 'none',
                'user-select': 'none'
            });
        }
        
        // Check for touch device and add mobile-specific enhancements
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

        // Simple test to verify sortable is working
        setTimeout(function() {
            if (!$("#page_sections").hasClass('ui-sortable')) {
                // Try to initialize again
                initializeSortable();
            }
        }, 2000);

        // Enhanced delete functionality that prevents page jumping
        function deleteWidget(button) {
            const $li = $(button).closest('li.highlight');
            if ($li.length > 0) {
                // Store current scroll position
                const currentScrollTop = $(window).scrollTop();
                
                $li.remove();
                watchState();
                reindexSections();
                
                // Restore scroll position after DOM manipulation
                setTimeout(() => {
                    $(window).scrollTop(currentScrollTop);
                }, 0);
            }
        }
        
        // Mobile-optimized delete handlers
        $(document).on('touchstart', ".btn-remove", function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            // Add visual feedback for touch
            $(this).addClass('delete-touch-active');
            
            // Set up for potential delete
            $(this).data('touch-delete-ready', true);
            
            return false;
        });
        
        $(document).on('touchend', ".btn-remove", function(e) {
            e.stopPropagation();
            e.preventDefault();
            
            // Remove visual feedback
            $(this).removeClass('delete-touch-active');
            
            // Only delete if this was a proper touch (not a drag)
            if ($(this).data('touch-delete-ready')) {
                deleteWidget(this);
                $(this).removeData('touch-delete-ready');
            }
            
            return false;
        });
        
        // Prevent delete during drag operations
        $(document).on('touchmove', ".btn-remove", function(e) {
            // If there's movement, don't delete
            $(this).removeData('touch-delete-ready');
        });
        
        // Desktop delete handler
        $(document).on('mousedown', ".btn-remove", function(e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            
            deleteWidget(this);
            
            return false;
        });
        
        // Backup click handler for compatibility
        $(document).on('click', ".btn-remove, .remove-icon, .remove-icon-color", function(e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            
            // Only handle if not already handled by touchend or mousedown
            if (!$(this).data('touch-delete-ready')) {
                deleteWidget(this);
            }
            
            return false;
        });

        function reindexSections(preventScrollRestore = false) {
            // Only store scroll position if we're not preventing restore
            const currentScrollTop = preventScrollRestore ? null : $(window).scrollTop();
            
            let actualIndex = 0;
            $('#page_sections li:not(.empty-state)').each(function() {
                const $li = $(this);
                
                // Update all input and select elements with array indices
                $li.find('input, select').each(function() {
                    const $input = $(this);
                    const name = $input.attr('name');
                    if (name && name.includes('[') && name.includes(']')) {
                        const newName = name.replace(/\[\d+\]/, `[${actualIndex}]`);
                        $input.attr('name', newName);
                    }
                });
                
                // Update the order hidden input specifically
                $li.find('input[name="order[]"]').val(actualIndex);
                
                actualIndex++;
            });
            
            // Only restore scroll position if not prevented (for drag operations, 
            // the sortable stop handler will handle scroll restoration)
            if (currentScrollTop !== null) {
                setTimeout(() => {
                    $(window).scrollTop(currentScrollTop);
                }, 0);
            }
        }

        function watchState() {
            const scriptItems = $('#page_sections li:not(.empty-state)').length;
            
            if (scriptItems === 0) {
                // Show empty state and hide add widget button
                if ($('#page_sections .empty-state').length === 0) {
                    // Store scroll position before DOM manipulation
                    const currentScrollTop = $(window).scrollTop();
                    
                    $('#page_sections').html(`<li class="empty-state">
                        <div class="empty-state-content">
                            <i class="las la-robot empty-state-icon"></i>
                            <h5>@lang('Your script will appear here')</h5>
                            <p class="text-muted mb-3">@lang('Start building your automation by adding widgets below')</p>
                            <button type="button" class="btn btn--primary add-widget-btn" data-toggle="modal" data-target="#widgetModal">
                                <i class="las la-plus"></i> @lang('Add Widget')
                            </button>
                        </div>
                    </li>`);
                    
                    // Restore scroll position after DOM update
                    setTimeout(() => {
                        $(window).scrollTop(currentScrollTop);
                    }, 0);
                }
                $('#addWidgetSection').hide();
            } else {
                // Remove empty state and show add widget button
                $('#page_sections .empty-state').remove();
                $('#addWidgetSection').show();
            }
        }

        // Function to normalize proxy input from various formats (comma, space, newline separated)
        function normalizeProxyInput(proxyString) {
            if (!proxyString || proxyString.trim() === '') {
                return [];
            }
            
            let proxies = [];
            
            // Handle different separators: newlines, commas, spaces, or combination
            const lines = proxyString.split('\n');
            for (let line of lines) {
                // Split by comma first, then by space
                const commaSplit = line.split(',');
                for (let commaPart of commaSplit) {
                    const spaceSplit = commaPart.split(/\s+/);
                    for (let spacePart of spaceSplit) {
                        const trimmed = spacePart.trim();
                        if (trimmed !== '') {
                            proxies.push(trimmed);
                        }
                    }
                }
            }
            
            return proxies.filter(proxy => proxy.length > 0);
        }
        
        // Function to validate a complete list of proxies (for form submission)
        function validateCompleteProxyList(proxies) {
            if (proxies.length === 0) {
                return { valid: true };
            }
            
            const ipv4Pattern = /^(\d{1,3}\.){3}\d{1,3}:\d{1,5}(:[^:]+:[^:]+)?$/;
            
            for (let i = 0; i < proxies.length; i++) {
                const proxy = proxies[i];
                
                if (!ipv4Pattern.test(proxy)) {
                    return { 
                        valid: false, 
                        error: `Invalid proxy format: "${proxy}". Expected IP:PORT or IP:PORT:USERNAME:PASSWORD` 
                    };
                }
                
                // Validate IP parts
                const parts = proxy.split(':');
                const ipParts = parts[0].split('.');
                
                for (let j = 0; j < 4; j++) {
                    const num = parseInt(ipParts[j]);
                    if (isNaN(num) || num < 0 || num > 255) {
                        return { 
                            valid: false, 
                            error: `Invalid IP address in proxy "${proxy}"` 
                        };
                    }
                }
                
                // Validate port
                const port = parseInt(parts[1]);
                if (isNaN(port) || port < 1 || port > 65535) {
                    return { 
                        valid: false, 
                        error: `Invalid port in proxy "${proxy}". Port must be between 1 and 65535` 
                    };
                }
            }
            
            return { valid: true };
        }

        // Function to validate custom proxy format
        function validateProxyFormat(proxyString) {
            // Allow empty string - let backend handle the validation
            if (!proxyString || proxyString.trim() === '') {
                return { valid: true };
            }

            // Handle different separators: newlines, commas, spaces, or combination
            let proxies = [];
            
            // First split by newlines, then by commas and spaces
            const lines = proxyString.split('\n');
            for (let line of lines) {
                // Split by comma first, then by space
                const commaSplit = line.split(',');
                for (let commaPart of commaSplit) {
                    const spaceSplit = commaPart.split(/\s+/);
                    for (let spacePart of spaceSplit) {
                        const trimmed = spacePart.trim();
                        if (trimmed !== '') {
                            proxies.push(trimmed);
                        }
                    }
                }
            }
            
            // If no complete proxy found yet, allow partial input during typing
            if (proxies.length === 0) {
                return { valid: true };
            }
            
            const ipv4Pattern = /^(\d{1,3}\.){3}\d{1,3}:\d{1,5}(:[^:]+:[^:]+)?$/;
            
            for (let i = 0; i < proxies.length; i++) {
                const proxy = proxies[i];
                
                // Allow incomplete proxies during typing (partial IPs, missing ports, etc.)
                if (proxy.includes('.') && !proxy.includes(':')) {
                    // Incomplete proxy - still typing port
                    continue;
                }
                
                if (proxy.split('.').length < 4) {
                    // Incomplete IP - still typing
                    continue;
                }
                
                // Only validate complete-looking proxies
                if (!ipv4Pattern.test(proxy)) {
                    // Check if it looks like a complete proxy attempt
                    if (proxy.includes(':') && proxy.split('.').length === 4) {
                        return { 
                            valid: false, 
                            error: `Invalid proxy format: "${proxy}". Expected IP:PORT or IP:PORT:USERNAME:PASSWORD` 
                        };
                    }
                    // Otherwise, assume it's still being typed
                    continue;
                }
                
                // Validate IP parts for complete proxies
                const parts = proxy.split(':');
                const ipParts = parts[0].split('.');
                
                // Check if we have 4 IP parts
                if (ipParts.length !== 4) {
                    continue; // Incomplete IP
                }
                
                for (let j = 0; j < 4; j++) {
                    const num = parseInt(ipParts[j]);
                    if (isNaN(num) || num < 0 || num > 255) {
                        return { 
                            valid: false, 
                            error: `Invalid IP address: "${proxy}"` 
                        };
                    }
                }
                
                // Validate port
                if (parts.length > 1) {
                    const port = parseInt(parts[1]);
                    if (isNaN(port) || port < 1 || port > 65535) {
                        return { 
                            valid: false, 
                            error: `Invalid port in "${proxy}". Port must be between 1 and 65535` 
                        };
                    }
                }
            }
            
            return { valid: true };
        }

        // Real-time validation for custom proxy input
        $('#customProxyInput').on('input', function() {
            if ($('#custom_proxy_enabled').val() == 1) {
                const proxyValue = $(this).val().trim();
                const validation = validateProxyFormat(proxyValue);
                
                // Check if button should be enabled based on credit and validation
                const hasCredit = {{ $user->bot_credit > $user->bot_used ? 'true' : 'false' }};
                const hasCreditError = $('input[name="active_users"]').hasClass('is-invalid');
                
                // Check if custom proxy is enabled but empty
                if (proxyValue === '') {
                    $('#createButton').prop('disabled', true).addClass('btn--disabled');
                    // Show error message
                    if (!$('#proxyValidationError').length) {
                        $('#customProxyInput').after('<div id="proxyValidationError" class="text-danger small mt-1"></div>');
                    }
                    $('#proxyValidationError').text('Please enter at least one proxy when custom proxy is enabled');
                    return;
                }
                
                // Only disable button for clear validation errors, not during typing
                if (!validation.valid) {
                    $('#createButton').prop('disabled', true).addClass('btn--disabled');
                    // Show error message
                    if (!$('#proxyValidationError').length) {
                        $('#customProxyInput').after('<div id="proxyValidationError" class="text-danger small mt-1"></div>');
                    }
                    $('#proxyValidationError').text(validation.error);
                } else {
                    // Only enable if user has credit, no validation errors, AND form has changes
                    if (hasCredit && !hasCreditError && (typeof isFormChanged !== 'undefined' ? isFormChanged : true)) {
                        $('#createButton').prop('disabled', false).removeClass('btn--disabled');
                    }
                    $('#proxyValidationError').remove();
                }
            }
        });

        // Form submission validation for proxy settings
        $('#projectForm').submit(function(event) {
            // Validate URL widgets if any exist
            let hasInvalidUrl = false;
            let firstInvalidUrl = null;
            
            $('input[name^="url["]').each(function() {
                const urlValue = $(this).val().trim();
                const urlPattern = /^https?:\/\/.+/;
                
                if (urlValue === '') {
                    $(this).addClass('is-invalid');
                    if ($(this).next('.invalid-feedback').length === 0) {
                        $(this).after('<div class="invalid-feedback d-block">URL is required for this widget.</div>');
                    }
                    hasInvalidUrl = true;
                    if (!firstInvalidUrl) firstInvalidUrl = $(this);
                } else if (!urlPattern.test(urlValue)) {
                    $(this).addClass('is-invalid');
                    if ($(this).next('.invalid-feedback').length === 0) {
                        $(this).after('<div class="invalid-feedback d-block">Please enter a valid URL starting with http:// or https://</div>');
                    }
                    hasInvalidUrl = true;
                    if (!firstInvalidUrl) firstInvalidUrl = $(this);
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });
            
            if (hasInvalidUrl) {
                event.preventDefault();
                showToast('Please fill in all URL widgets with valid URLs.', 'error');
                
                // Switch to Campaign Details tab where widgets are located
                const detailsTab = $('#campaignTabs a[href="#details-content"]');
                if (detailsTab.length) {
                    detailsTab.tab('show');
                    
                    // Scroll to first invalid URL after tab switch
                    setTimeout(function() {
                        if (firstInvalidUrl) {
                            $('html, body').animate({
                                scrollTop: firstInvalidUrl.offset().top - 150
                            }, 500);
                            firstInvalidUrl.focus();
                        }
                    }, 400);
                }
                
                // Re-enable the button since validation failed
                $('#createButton').prop('disabled', false).removeClass('btn--disabled').text('Update Campaign');
                return false;
            }
            
            if (!$('#toggleFreeProxy').hasClass('btn--base') && !$('#toggleCustomProxy').hasClass('btn--base')) {
                event.preventDefault();
                $('#proxyError').removeClass('d-none');
                showToast('Please enable at least one proxy option.', 'warning');
                // Scroll to proxy settings section
                $('html, body').animate({
                    scrollTop: $('#toggleFreeProxy').offset().top - 100
                }, 500);
                
                // Re-enable the button since validation failed
                $('#createButton').prop('disabled', false).removeClass('btn--disabled').text('Update Campaign');
                return false;
            }

            // Validate custom proxy format if custom proxy is enabled
            if ($('#custom_proxy_enabled').val() == 1) {
                const proxyValue = $('#customProxyInput').val().trim();
                
                // Check if custom proxy is enabled but empty
                if (!proxyValue || proxyValue === '') {
                    event.preventDefault();
                    showToast('Please enter at least one proxy when custom proxy is enabled', 'error');
                    // Scroll to custom proxy input
                    $('html, body').animate({
                        scrollTop: $('#customProxyInput').offset().top - 100
                    }, 500);
                    
                    // Re-enable the button since validation failed
                    $('#createButton').prop('disabled', false).removeClass('btn--disabled').text('Update Campaign');
                    return false;
                }
                
                // Validate proxy format if there's actual content
                if (proxyValue) {
                    // Create a more thorough validation for form submission
                    const normalizedProxies = normalizeProxyInput(proxyValue);
                    const validation = validateCompleteProxyList(normalizedProxies);
                    
                    if (!validation.valid) {
                        event.preventDefault();
                        showToast(validation.error, 'error');
                        // Scroll to custom proxy input
                        $('html, body').animate({
                            scrollTop: $('#customProxyInput').offset().top - 100
                        }, 500);
                        
                        // Re-enable the button since validation failed
                        $('#createButton').prop('disabled', false).removeClass('btn--disabled').text('Update Campaign');
                        return false;
                    }
                }
            }

            // Remove referrer_urls from form data if referrer is not enabled
            if ($('#referrer_enabled').val() != 1) {
                $('input[name="referrer_urls[]"]').remove();
            }

            // Only disable button and change text if validation passed
            $('#proxyError').addClass('d-none');
            $('#createButton').prop('disabled', true).addClass('btn--disabled').text('Updating Campaign, Please wait...');
        });
    })(jQuery);
</script>
@endpush

@push('style')
    <style>
        
        .simple_with_drop,
        .simple_with_no_drop {
            user-select: none;
        }

        /* Allow dragging on sortable items */
        #page_sections li.highlight {
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }

        #page_sections li.highlight * {
            pointer-events: auto;
        }

        #page_sections li.highlight .btn-remove {
            pointer-events: auto;
        }

        .icon-move .sortable-icon {
            font-family: "Line Awesome Free";
            font-weight: 900;
            font-style: normal;
            font-size: 14px;
        }

        .simple_with_no_drop .sortable-icon:before {
            content: "\f060";
        }

        .simple_with_drop .sortable-icon:before {
            content: "\f2a1";
        }

        /* Modal widget grid */
        .widget-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
        }

        /* Widget button styling in modal */
        .widget-btn {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            padding: 1rem 0.75rem !important;
            min-height: 110px !important;
            text-align: center !important;
            border-radius: 10px !important;
            transition: all 0.3s ease !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            border: 2px solid #e9ecef !important;
            background: #ffffff !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .widget-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .widget-btn:hover:before {
            left: 100%;
        }

        .widget-btn i {
            font-size: 2.25rem !important;
            margin-bottom: 0.5rem !important;
            color: hsl(var(--base)) !important;
            transition: all 0.3s ease !important;
        }

        .widget-btn span {
            font-size: 0.9rem !important;
            line-height: 1.3 !important;
            font-weight: 600 !important;
            color: #495057 !important;
            margin-bottom: 0.4rem !important;
        }

        .widget-description {
            font-size: 0.75rem !important;
            color: #6c757d !important;
            line-height: 1.3 !important;
            font-weight: 400 !important;
        }

        .widget-btn:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.2) !important;
            border-color: hsl(var(--base)) !important;
            background: #f8f9ff !important;
        }

        .widget-btn:hover i {
            color: hsl(var(--base-d-200)) !important;
            transform: scale(1.15) !important;
        }

        .widget-btn:hover span {
            color: hsl(var(--base)) !important;
        }

        .highlight {
            padding: 1rem;
            background-color: #fdfdfd;
            border: 1px solid rgb(0 0 0 / 6%);
            border-radius: .5rem;
            display: flex;
            flex-direction: column;
            position: relative;
            margin-bottom: 0.5rem;
        }

        .remove-icon, .btn-remove {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer !important;
            color: #ea5455 !important;
            font-size: 16px !important;
            width: 24px !important;
            height: 24px !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            z-index: 999 !important;
            background: rgba(234, 84, 85, 0.1) !important;
            border: none !important;
            border-radius: 50% !important;
            pointer-events: auto !important;
        }

        .remove-icon:hover, .btn-remove:hover {
            color: #ffffff !important;
            background: #ea5455 !important;
        }

        .btn-remove i {
            color: inherit !important;
            font-size: 14px !important;
            pointer-events: none !important; /* Prevent event bubbling on the icon */
        }
        
        /* Delete button touch feedback */
        .btn-remove.delete-touch-active {
            transform: scale(1.1) !important;
            background: #dc3545 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4) !important;
        }
        
        /* Ensure button is above sortable handle */
        .btn-remove {
            z-index: 1000 !important;
        }
        
        /* Make sure the highlight item allows for proper positioning */
        .highlight.icon-move {
            position: relative !important;
        }

        .empty-state {
            border: 2px dashed #e9ecef;
            border-radius: 12px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            text-align: center;
            padding: 3rem 2rem;
            cursor: default;
            transition: all 0.3s ease;
        }

        .empty-state:hover {
            border-color: hsl(var(--base));
            background: linear-gradient(135deg, #f0f8ff 0%, #ffffff 100%);
        }

        .empty-state-content h5 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: #6c757d;
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        .add-widget-btn {
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .add-widget-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
        }
        
        /* Modal styling - ensure proper visibility */
        .modal {
            z-index: 9999 !important;
        }
        
        .modal.show {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        .modal-backdrop {
            z-index: 9998 !important;
        }
        
        .modal-dialog {
            max-width: 900px !important;
            width: 90% !important;
            margin: 1.75rem auto !important;
        }
        
        .modal-content {
            border-radius: 15px !important;
            border: none !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15) !important;
        }

        .modal-header {
            border-bottom: 2px solid #e9ecef !important;
            padding: 1.25rem 1.5rem !important;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
            border-radius: 15px 15px 0 0 !important;
        }

        .modal-title {
            font-weight: 700 !important;
            color: #495057 !important;
            font-size: 1.25rem !important;
        }

        .modal-body {
            padding: 1.5rem !important;
            max-height: 70vh !important;
            overflow-y: auto !important;
        }

        /* Bootstrap 4 modal close button styling */
        .close {
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            background: transparent;
            border: 0;
            cursor: pointer;
        }

        .close:hover {
            color: #000;
            text-decoration: none;
            opacity: .75;
        }
        
        /* Tablet/Medium screen responsive styles */
        @media (max-width: 991px) {
            .widget-grid {
                grid-template-columns: repeat(3, 1fr) !important;
                gap: 0.75rem !important;
            }
            
            .modal-dialog {
                max-width: 720px !important;
            }
            
            .widget-btn {
                min-height: 95px !important;
                padding: 0.75rem 0.5rem !important;
            }
            
            .widget-btn i {
                font-size: 1.75rem !important;
            }
            
            .widget-btn span {
                font-size: 0.85rem !important;
            }
        }
        
        /* Mobile responsive styles */
        @media (max-width: 767px) {
            .empty-state {
                padding: 2rem 1rem;
                margin: 1rem 0;
            }

            .empty-state-icon {
                font-size: 2.5rem;
            }

            .empty-state-content h5 {
                font-size: 1.1rem;
            }

            .widget-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.5rem !important;
            }
            
            .widget-btn {
                min-height: 85px !important;
                padding: 0.5rem 0.25rem !important;
                border-radius: 6px !important;
            }
            
            .widget-btn i {
                font-size: 1.3rem !important;
                margin-bottom: 0.25rem !important;
            }
            
            .widget-btn span {
                font-size: 0.75rem !important;
                margin-bottom: 0.2rem !important;
                line-height: 1.1 !important;
            }

            .widget-description {
                font-size: 0.6rem !important;
                line-height: 1.1 !important;
            }

            .modal-dialog {
                margin: 0.25rem !important;
            }

            .modal-body {
                padding: 0.75rem !important;
            }

            .modal-header {
                padding: 0.75rem !important;
            }

            .modal-footer {
                padding: 0.5rem !important;
            }
        }

        @media (max-width: 575px) {
            .empty-state {
                padding: 1.5rem 0.75rem;
            }

            .add-widget-btn {
                padding: 0.6rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        #page_sections.dropping {
            border: 2px dotted #ccc;
            padding: 0 1rem;
        }

        /* Sortable placeholder styling */
        .sortable-placeholder {
            border: 2px dashed hsl(var(--base)) !important;
            background: rgba(0, 123, 255, 0.1) !important;
            border-radius: 0.5rem !important;
            margin: 0.5rem 0 !important;
            min-height: 60px !important;
            position: relative !important;
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .sortable-placeholder-active {
            animation: placeholderPulse 1.5s ease-in-out infinite !important;
        }

        @keyframes placeholderPulse {
            0%, 100% { 
                background: rgba(0, 123, 255, 0.1) !important;
                border-color: hsl(var(--base)) !important;
            }
            50% { 
                background: rgba(0, 123, 255, 0.2) !important;
                border-color: hsl(var(--base-d-200)) !important;
            }
        }

        .sortable-placeholder-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: hsl(var(--base));
            font-weight: 600;
            font-size: 14px;
            pointer-events: none;
        }

        /* Improve sortable handle */
        .sortable-icon {
            cursor: move !important;
            cursor: grab !important;
            color: #6c757d !important;
            margin-right: 10px !important;
            padding: 8px !important;
            font-size: 18px !important;
            background: #f8f9fa !important;
            border: 2px solid #e9ecef !important;
            border-radius: 6px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 32px !important;
            height: 32px !important;
            transition: all 0.2s ease !important;
        }

        .sortable-icon:hover {
            color: hsl(var(--base)) !important;
            background: #e3f2fd !important;
            border-color: hsl(var(--base)) !important;
            cursor: grabbing !important;
        }

        .sortable-icon:active {
            color: hsl(var(--base-d-200)) !important;
            background: #bbdefb !important;
            transform: scale(0.95) !important;
            cursor: grabbing !important;
        }

        /* Enhanced widget hover behavior */
        .highlight {
            transition: all 0.2s ease !important;
            cursor: move !important;
        }

        .highlight:hover {
            background-color: #f8f9ff !important;
            border-color: hsl(var(--base)) !important;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15) !important;
            transform: translateY(-1px) !important;
        }
        
        /* Desktop sortable handle hover enhancement */
        .highlight:hover .sortable-icon {
            background: hsl(var(--base)) !important;
            color: #ffffff !important;
            border-color: hsl(var(--base-d-200)) !important;
            transform: scale(1.05) !important;
        }

        /* Visual feedback during dragging */
        .ui-sortable-helper {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            transform: rotate(2deg) !important;
            z-index: 1000 !important;
            max-width: 100% !important; /* Prevent helper from exceeding container width */
            position: relative !important; /* Keep helper positioned relative to container */
        }

        .highlight.ui-sortable-dragging {
            opacity: 0.8 !important;
        }

        /* Ensure the sortable container maintains proper positioning */
        #page_sections {
            position: relative !important;
            overflow: visible !important;
        }

        /* Constrain helper within the script area */
        #page_sections .ui-sortable-helper {
            left: 0 !important; /* Prevent horizontal movement outside container */
            right: 0 !important;
            margin-left: 0 !important;
            margin-right: 0 !important;
        }

        /* Enhanced touch feedback for mobile - similar to desktop hover */
        .highlight.touch-active {
            background-color: #f8f9ff !important;
            border-color: hsl(var(--base)) !important;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15) !important;
            transform: scale(0.99) !important;
            transition: all 0.2s ease !important;
        }
        
        /* Enhanced mobile drag feedback - more prominent */
        .highlight.touch-dragging {
            background-color: #e3f2fd !important;
            border: 2px solid hsl(var(--base)) !important;
            transform: scale(1.03) rotate(2deg) !important;
            z-index: 1000 !important;
            box-shadow: 0 12px 30px rgba(0, 123, 255, 0.4) !important;
            transition: none !important;
        }
        
        /* Mobile sortable handle active state */
        .highlight.touch-active .sortable-icon {
            background: hsl(var(--base)) !important;
            color: #ffffff !important;
            border-color: hsl(var(--base-d-200)) !important;
            transform: scale(1.1) !important;
        }
        
        /* Progressive drag preparation state */
        .highlight.touch-preparing-drag {
            background-color: #e8f4fd !important;
            border-color: hsl(var(--base)) !important;
            transform: scale(1.01) !important;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2) !important;
        }
        
        .highlight.touch-preparing-drag .sortable-icon {
            background: hsl(var(--base-d-200)) !important;
            color: #ffffff !important;
            animation: pulse 0.8s infinite !important;
        }
        
        @keyframes pulse {
            0% { transform: scale(1.1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1.1); }
        }
        
            
        /* Complete page scroll lock during drag operations */
        html.page-scroll-locked,
        body.page-scroll-locked {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
            height: 100% !important;
            top: 0 !important;
            left: 0 !important;
        }
        
        /* Add dark overlay when script is locked */
        body.page-scroll-locked::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            pointer-events: none;
        }
        
        /* Enhanced mobile drag scroll control */
        body.mobile-drag-active {
            overflow: hidden !important;
            position: fixed !important;
            width: 100% !important;
            height: 100% !important;
        }
        
        /* Enable scroll only in script section during drag - more prominent */
        .script-scroll-locked {
            overflow-y: auto !important;
            max-height: 70vh !important;
            position: fixed !important;
            top: 15vh !important;
            left: 5% !important;
            right: 5% !important;
            width: 90% !important;
            z-index: 10000 !important;
            border: 3px solid hsl(var(--base)) !important;
            border-radius: 12px !important;
            background: #ffffff !important;
            box-shadow: 0 20px 60px rgba(0, 123, 255, 0.4) !important;
            -webkit-overflow-scrolling: touch !important;
        }
        
        /* Mobile specific scroll lock adjustments - full screen overlay */
        @media (max-width: 767px) {
            .script-scroll-locked {
                max-height: 80vh !important;
                top: 10vh !important;
                left: 2% !important;
                right: 2% !important;
                width: 96% !important;
                border-radius: 8px !important;
            }
            
            .script-scroll-locked::before {
                font-size: 0.8rem !important;
                padding: 0.8rem !important;
                border-radius: 5px 5px 0 0 !important;
                margin: -3px -3px 0.5rem -3px !important;
            }
        }
        
        @media (max-width: 480px) {
            .script-scroll-locked {
                max-height: 85vh !important;
                top: 5vh !important;
                left: 1% !important;
                right: 1% !important;
                width: 98% !important;
                border-radius: 4px !important;
            }
        }
        
        /* Enhance script section during locked scroll */
        .script-scroll-locked #page_sections {
            min-height: 200px !important;
            padding: 1rem !important;
        }
        

        
        /* Enhanced scroll lock feedback animation */
        body.scroll-lock-feedback::after {
            content: "🔒 Page Scroll Disabled - Use Script Window Only";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.95) 0%, rgba(220, 53, 69, 0.9) 100%);
            color: white;
            padding: 1.2rem 2.5rem;
            border-radius: 30px;
            font-size: 1rem;
            font-weight: 700;
            z-index: 20000;
            animation: bounceInOut 0.6s ease;
            pointer-events: none;
            border: 2px solid white;
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
            text-align: center;
            max-width: 80vw;
        }
        
        @keyframes bounceInOut {
            0% { 
                opacity: 0; 
                transform: translate(-50%, -50%) scale(0.3) rotate(-5deg); 
            }
            25% { 
                opacity: 1; 
                transform: translate(-50%, -50%) scale(1.1) rotate(2deg); 
            }
            50% { 
                opacity: 1; 
                transform: translate(-50%, -50%) scale(1) rotate(0deg); 
            }
            75% { 
                opacity: 1; 
                transform: translate(-50%, -50%) scale(1.05) rotate(-1deg); 
            }
            100% { 
                opacity: 0; 
                transform: translate(-50%, -50%) scale(0.8) rotate(0deg); 
            }
        }
        
        /* Mobile touch improvements */
        .highlight {
            touch-action: none !important; /* Prevent default touch behaviors */
        }
        
        /* Ensure sortable handle is touchable */
        .sortable-icon {
            touch-action: manipulation !important;
        }
        
        /* Enhanced mobile sorting feedback */
        .ui-sortable-helper.mobile-sorting {
            transform: rotate(3deg) scale(1.08) !important;
            box-shadow: 0 15px 40px rgba(0, 123, 255, 0.5) !important;
            z-index: 9999 !important;
            border: 3px solid hsl(var(--base)) !important;
            background: linear-gradient(135deg, #f8f9ff 0%, #e3f2fd 100%) !important;
        }
        
        /* Enhanced mobile placeholder animation */
        @media (max-width: 767px) {
            .ui-sortable-placeholder {
                background: linear-gradient(45deg, rgba(0, 123, 255, 0.2) 25%, transparent 25%, transparent 50%, rgba(0, 123, 255, 0.2) 50%, rgba(0, 123, 255, 0.2) 75%, transparent 75%, transparent) !important;
                background-size: 20px 20px !important;
                animation: move-placeholder 1s linear infinite !important;
                border: 3px dashed hsl(var(--base)) !important;
            }
            
            @keyframes move-placeholder {
                0% { background-position: 0 0; }
                100% { background-position: 20px 20px; }
            }
        }
        
        /* Improved mobile placeholder */
        @media (max-width: 767px) {
            .sortable-placeholder {
                background: linear-gradient(45deg, rgba(0, 123, 255, 0.1) 25%, transparent 25%, transparent 50%, rgba(0, 123, 255, 0.1) 50%, rgba(0, 123, 255, 0.1) 75%, transparent 75%, transparent) !important;
                background-size: 20px 20px !important;
                animation: move-placeholder 2s linear infinite !important;
            }
            
            @keyframes move-placeholder {
                0% { background-position: 0 0; }
                100% { background-position: 20px 20px; }
            }
        }

        /* Improve touch targets */
        @media (pointer: coarse) {
            .sortable-icon, .btn-remove {
                min-width: 44px !important;
                min-height: 44px !important;
            }
        }

        .btn--disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        /* Additional mobile improvements for script section */
        @media (max-width: 767px) {
            /* Mobile script section improvements */
            .highlight {
                padding: 0.75rem !important;
                margin-bottom: 0.75rem !important;
            }
            
            /* Mobile-specific sortable improvements - much smaller size */
            .sortable-icon {
                width: 17px !important;
                height: 17px !important;
                font-size: 10px !important;
                padding: 2px !important;
                margin-right: 4px !important;
                /* Adequate touch target for mobile */
                min-width: 22px !important;
                min-height: 22px !important;
            }
            
            .btn-remove {
                width: 14px !important;
                height: 14px !important;
                font-size: 8px !important;
                /* Adequate touch target for mobile */
                min-width: 20px !important;
                min-height: 20px !important;
                top: 6px !important;
                right: 6px !important;
            }
            
            /* Improved alignment for mobile widget titles */
            .d-flex.align-items-center.mb-2 {
                margin-bottom: 0.5rem !important;
                align-items: center !important;
            }
            
            .d-flex.align-items-center.mb-2 span {
                line-height: 1.2 !important;
                font-size: 0.9rem !important;
            }
            

            
            /* Mobile drag feedback */
            .ui-sortable-helper {
                transform: rotate(1deg) scale(1.02) !important;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2) !important;
            }
            
            /* Ensure mobile scrolling works during drag */
            body.ui-sortable-dragging {
                overflow: hidden;
            }
            
            /* Better placeholder visibility on mobile */
            .sortable-placeholder {
                min-height: 70px !important;
                margin: 0.75rem 0 !important;
                border-width: 2px !important;
            }
            
            .sortable-placeholder-content {
                font-size: 14px !important;
                font-weight: 600 !important;
            }
            
            /* Mobile card improvements */
            .card-body {
                padding: 1rem !important;
            }
            
            /* Mobile text size adjustments */
            .card-title {
                font-size: 1.1rem !important;
            }
            
            .text--base {
                font-size: 0.8rem !important;
            }
            
            /* Mobile Scroll widget - stack vertically */
            .scroll-widget-inputs {
                display: flex;
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .scroll-input-item {
                width: 100%;
            }
        }

        /* Desktop Scroll widget - display horizontally */
        @media (min-width: 769px) {
            .scroll-widget-inputs {
                display: flex;
                flex-direction: row;
                gap: 0.5rem;
            }
            
            .scroll-input-item {
                flex: 1;
            }
        }

        /* Custom dropdown arrow for scroll type select */
        .scroll-widget-inputs select.scroll-input-item {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        /* Custom dropdown arrow for click type select */
        .highlight select[name^="click_type"] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        /* Custom dropdown arrow for all form select elements */
        select.form--control,
        select#devices,
        select#search_ext,
        select#freeProxyInput {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
            padding-right: 2.5rem;
        }

        /* Toast notification animation */
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .toast-notification {
            animation: slideIn 0.3s ease;
        }

        .alert {
            margin-top: 20px;
            font-size: 1rem;
        }

        /* Credit information styling */
        .form--label .text--base {
            background: #e3f2fd;
            padding: 2px 6px;
            border-radius: 4px;
            font-weight: 500;
        }

        .credit-info {
            display: inline-block;
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            margin-left: 5px;
        }

        .credit-available {
            color: #28a745;
            font-weight: 600;
        }

        .credit-used {
            color: #dc3545;
            font-weight: 600;
        }

        .credit-total {
            color: #6c757d;
            font-weight: 600;
        }
        
        /* Info icon styling to ensure visibility */
        .info-icon {
            color: #6c757d !important;
            font-size: 1.1rem;
            margin-left: 5px;
            cursor: help;
            transition: color 0.2s ease;
            display: inline-block;
            vertical-align: middle;
        }
        
        .info-icon:hover {
            color: #495057 !important;
            transform: scale(1.1);
        }
        
        /* Tooltip styling */
        .tooltip {
            font-size: 0.875rem;
            z-index: 1070;
        }
        
        .tooltip-inner {
            max-width: 300px;
            padding: 8px 12px;
            background-color: #333;
            color: #fff;
            border-radius: 6px;
            text-align: left;
        }
        
        /* Simple tooltip fallback */
        .simple-tooltip {
            position: absolute;
            background: #333;
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            max-width: 300px;
            word-wrap: break-word;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Ensure info icons are visible on mobile */
        @media (max-width: 768px) {
            .info-icon {
                font-size: 1.2rem;
                margin-left: 8px;
            }
        }
        
        /* Validation error styling */
        .is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        
        .invalid-feedback {
            display: block !important;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .form-control.is-invalid:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        /* Card Header with Nav Tabs - Button Style */
        .card-header.border-0 {
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-bottom: 2px solid #e9ecef !important;
            padding: 1.5rem 1rem !important;
        }

        /* Nav Pills Tabs Styling - Button Style inspired by sidebar badges */
        #campaignTabs {
            margin-bottom: 0;
            border-bottom: none;
            display: flex;
            flex-wrap: nowrap;
            justify-content: space-between;
            padding: 0;
            gap: 0.5rem;
            width: 100%;
        }

        #campaignTabs .nav-item {
            flex: 1 1 0;
        }

        #campaignTabs .nav-link {
            /* Button-like styling similar to sidebar badges */
            border-radius: 12px;
            padding: 12px 24px;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.3px;
            text-transform: none;
            transition: all 0.3s ease;
            
            /* Default state - light solid color */
            background: #e9ecef;
            border: 2px solid #dee2e6;
            color: #495057;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            
            cursor: pointer;
            white-space: nowrap;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        #campaignTabs .nav-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #adb5bd;
            background: #dee2e6;
            color: #495057;
        }

        #campaignTabs .nav-link.active {
            /* Solid color #375c6c */
            background: #375c6c !important;
            color: white !important;
            border-color: #375c6c !important;
            box-shadow: 0 4px 12px rgba(55, 92, 108, 0.4) !important;
            animation: pulse-tab 2s ease-in-out infinite;
        }

        #campaignTabs .nav-link.active:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(55, 92, 108, 0.5) !important;
            background: #375c6c !important;
        }

        #campaignTabs .nav-link i {
            margin-right: 6px;
            font-size: 16px;
        }

        /* Pulse animation for active tab - similar to sidebar badges */
        @keyframes pulse-tab {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 4px 12px rgba(55, 92, 108, 0.4);
            }
            50% {
                transform: scale(1.02);
                box-shadow: 0 6px 16px rgba(55, 92, 108, 0.6);
            }
        }

        .tab-content {
            animation: fadeIn 0.3s ease-in;
            min-height: 400px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Update Button Section */
        #createButton {
            position: relative;
            z-index: 10;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        #createButton:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        /* Responsive tabs for tablets - Keep horizontal layout */
        @media (max-width: 991px) and (min-width: 768px) {
            #campaignTabs {
                gap: 0.5rem;
                flex-wrap: nowrap !important;
            }
            
            #campaignTabs .nav-item {
                flex: 1 1 0 !important;
                min-width: 0;
            }
            
            #campaignTabs .nav-link {
                padding: 8px 12px;
                font-size: 12px;
                flex-direction: row;
                white-space: nowrap;
            }

            #campaignTabs .nav-link i {
                font-size: 14px;
                margin-right: 4px;
            }
            
            .tab-text {
                font-size: 11px;
            }
        }

        /* Responsive tabs for mobile - Compact horizontal layout */
        @media (max-width: 767px) {
            .card-header.border-0 {
                padding: 1rem 0.5rem !important;
            }
            
            #campaignTabs {
                flex-wrap: nowrap !important;
                gap: 0.25rem;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none; /* Firefox */
                -ms-overflow-style: none; /* IE and Edge */
            }
            
            #campaignTabs::-webkit-scrollbar {
                display: none; /* Chrome, Safari, Opera */
            }

            #campaignTabs .nav-item {
                flex: 0 0 auto;
                min-width: 80px;
            }

            #campaignTabs .nav-link {
                padding: 8px 12px;
                font-size: 11px;
                flex-direction: row;
                border-radius: 8px;
                white-space: nowrap;
            }

            #campaignTabs .nav-link i {
                margin-right: 4px;
                margin-bottom: 0;
                font-size: 14px;
            }

            .tab-text {
                text-align: center;
                font-size: 10px;
            }
        }

        /* Very small mobile - Ultra compact */
        @media (max-width: 576px) {
            #campaignTabs .nav-item {
                min-width: 70px;
            }
            
            #campaignTabs .nav-link {
                padding: 6px 8px;
                font-size: 10px;
            }
            
            #campaignTabs .nav-link i {
                font-size: 12px;
                margin-right: 3px;
            }
            
            .tab-text {
                font-size: 9px;
            }
        }
        
        /* Optimized Campaign Tabs Styling */
        .campaign-tabs {
            background: var(--bs-card-bg, #fff);
            padding: 0.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            gap: 0.5rem;
        }
        
        .campaign-tabs .nav-item {
            flex: 1 1 auto;
            min-width: 0;
        }
        
        .campaign-tabs .nav-link {
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            white-space: nowrap;
            font-weight: 500;
        }
        
        .campaign-tabs .nav-link:hover:not(.disabled) {
            background: rgba(var(--base-color-rgb, 74, 85, 104), 0.1);
            transform: translateY(-1px);
        }
        
        .campaign-tabs .nav-link.active {
            box-shadow: 0 4px 12px rgba(var(--base-color-rgb, 74, 85, 104), 0.2);
        }
        
        .campaign-tabs .nav-link.disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .campaign-tabs .tab-icon {
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .campaign-tabs .tab-text {
            font-size: 0.875rem;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        /* Tab Content Optimization */
        .tab-content {
            min-height: 400px;
        }
        
        .tab-pane {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Responsive Tab Design */
        @media (max-width: 768px) {
            .campaign-tabs {
                padding: 0.25rem;
                gap: 0.25rem;
            }
            
            .campaign-tabs .nav-link {
                padding: 10px 12px;
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .campaign-tabs .tab-icon {
                font-size: 1.5rem;
            }
            
            .campaign-tabs .tab-text {
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .campaign-tabs .nav-link {
                padding: 8px 6px;
            }
            
            .campaign-tabs .tab-icon {
                font-size: 1.25rem;
            }
            
            .campaign-tabs .tab-text {
                font-size: 0.65rem;
            }
        }
    </style>
@endpush
