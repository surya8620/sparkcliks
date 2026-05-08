@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="row g-1 g-lg-3 justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> @lang('Edit Campaign')</h4>
                </div>

                <div class="card-body">
                    <form id="campaignForm" action="{{ route('user.campaign.update', $campaign->id) }}" method="post">
                        @csrf
                        @method('POST')
                        <div class="row">
                            <div class="form-group col-12 col-md-12">
                                <label class="form--label">@lang('Name your Campaign')</label>
                                <input class="form--control" name="name" value="{{ $campaign->name }}" placeholder="My Campaign" required>
                            </div>
                            <div class="form-group col-12 col-md-8">
                                <label class="form--label">@lang('Enter URL')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Enter URL/URLs with HTTP or HTTPS Prefix') https://example.com</small></label>
                                <textarea class="form--control" name="urls" rows="2" placeholder="Enter URL(https://example.com)" required>{{ implode("\n", json_decode($campaign->urls)) }}</textarea>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('URL Order')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleUrlOrder" class="btn btn--primary me-2 w-100" name="url_order">{{ $campaign->url_order === 'one_by_one' ? 'One by One' : 'Shuffle URLs' }}</button>
                                    <input type="hidden" name="url_order" id="urlOrderInput" value="{{ $campaign->url_order }}">
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="form--label">@lang('Devices')</label>
                                    <select id="devices" class="form--control" name="devices">
                                        <option value="desktop" {{ $campaign->devices === 'desktop' ? 'selected' : '' }}>Desktop</option>
                                        <option value="mobile" {{ $campaign->devices === 'mobile' ? 'selected' : '' }}>Mobile</option>
                                        <option value="random" {{ $campaign->devices === 'random' ? 'selected' : '' }}>Random</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Referrer Extensions')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleReferral" class="btn btn--primary me-2 w-100">{{ $campaign->referrer_enabled ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="referrer_enabled" id="referrer_enabled" value="{{ $campaign->referrer_enabled }}">
                                </div>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Search Extensions')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleSearch" class="btn btn--primary me-2 w-100" disabled="">{{ $campaign->search_enabled ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="search_enabled" id="search_enabled" value="{{ $campaign->search_enabled }}">
                                </div>                                
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Search Engine')</label>
                                <select id="search_ext" class="form--control" name="search_ext" disabled="">
                                    <option value="google" {{ $campaign->search_engine === 'google' ? 'selected' : '' }}>Google</option>
                                    <option value="google_maps" {{ $campaign->search_engine === 'google_maps' ? 'selected' : '' }}>Google Maps/My Business</option>
                                    <option value="bing" {{ $campaign->search_engine === 'bing' ? 'selected' : '' }}>Bing</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Referrer URLs')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Traffic Source URLs with HTTP or HTTPS Prefix') https://example.com</small></label>
                                <textarea id="referrer_urls" class="form--control" name="referrer_urls" rows="3" placeholder="Add the referrer URLs/websites (each per line)  https://example.com">{{ implode("\n", json_decode($campaign->referrer_urls)) }}</textarea>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Search Keywords')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Keywords from Search Console or Bing Master indexed in top 100.')</small></label>
                                <textarea id="search_keywords" class="form--control" name="search_keywords" rows="3" placeholder="Add the keywords (each per line)" disabled="">{{ $campaign->search_keywords }}</textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">@lang('Script')</h4>
                                        <small class="text--base">@lang('Click the buttons to add widgets to the script.')</small>
                                    </div>
                                    <div class="card-body">
                                        <ol id="page_sections" class="simple_with_drop vertical sec-item">
                                        @forelse($campaign->sections as $index => $section)
                                            @php
                                                $settings = is_string($section->settings) ? json_decode($section->settings, true) : $section->settings;
                                            @endphp
                                            <li class="highlight icon-move" data-key="{{ $section->type }}">
                                                <i class="sortable-icon"></i>
                                                <span class="d-inline-block me-auto text--base">{{ $section->type }}</span>
                                                <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                                                <input type="hidden" name="secs[{{ $index }}]" value="{{ $section->type }}">
                                                <input type="hidden" name="order[{{ $index }}]" value="{{ $loop->index }}">
                                                @if($section->type === 'URL')
                                                    <input type="text" name="url[{{ $index }}]" class="form-control mt-2" value="{{ $settings['url'] }}" placeholder="Enter URL">
                                                @elseif($section->type === 'Wait')
                                                    <div class="d-flex mt-2">
                                                        <input type="number" name="min_wait[{{ $index }}]" class="form-control me-2" value="{{ $settings['min_wait'] }}" placeholder="Min Wait Time">
                                                        <input type="number" name="max_wait[{{ $index }}]" class="form-control" value="{{ $settings['max_wait'] }}" placeholder="Max Wait Time">
                                                    </div>
                                                @elseif($section->type === 'Scroll')
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <select name="scroll_type[{{ $index }}]" class="form-control me-2">
                                                            <option value="Random" {{ $settings['scroll_type'] === 'Random' ? 'selected' : '' }}>Random</option>
                                                            <option value="Up" {{ $settings['scroll_type'] === 'Up' ? 'selected' : '' }}>Up</option>
                                                            <option value="Down" {{ $settings['scroll_type'] === 'Down' ? 'selected' : '' }}>Down</option>
                                                        </select>
                                                        <input type="number" name="scroll_percentage[{{ $index }}]" class="form-control" value="{{ $settings['scroll_percentage'] }}" placeholder="Scroll Time in seconds" min="1">
                                                    </div>
                                                @elseif($section->type === 'Click')
                                                    <div class="d-flex justify-content-between mt-2">
                                                        <select name="click_type[{{ $index }}]" class="form-control me-2">
                                                            <option value="Internal" {{ $settings['click_type'] === 'Internal' ? 'selected' : '' }}>Internal</option>
                                                            <option value="External" {{ $settings['click_type'] === 'External' ? 'selected' : '' }}>External</option>
                                                            <option value="Random" {{ $settings['click_type'] === 'Random' ? 'selected' : '' }}>Random</option>
                                                            <!-- <option value="Adsense" {{ $settings['click_type'] === 'Adsense' ? 'selected' : '' }}>Adsense</option> -->
                                                        </select>
                                                        <input type="number" name="click_percentage[{{ $index }}]" class="form-control" value="{{ $settings['click_percentage'] }}" placeholder="Percentage" min="0" max="100">
                                                    </div>
                                                @endif
                                            </li>
                                        @empty
                                            <li class="empty-state">
                                                <span>@lang('Your script will appear here')</span>
                                            </li>
                                        @endforelse

                                        </ol>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card sticky-top" style="top: 20px;">
                                    <div class="card-header">
                                        <h4 class="card-title">@lang('Widgets')</h4>
                                        <small class="text--base">@lang('Click on the Widgets to add it to the script.')</small>
                                    </div>
                                    <div class="card-body">
                                        <div class="grid-container">
                                            <button type="button" class="btn btn-outline--base add-widget" data-key="URL">@lang('Navigate to URL')</button>
                                            <button type="button" class="btn btn-outline--base add-widget" data-key="Wait">@lang('Wait')</button>
                                            <button type="button" class="btn btn-outline--base add-widget" data-key="Scroll">@lang('Scroll')</button>
                                            <button type="button" class="btn btn-outline--base add-widget" data-key="Click">@lang('Click')</button>
                                            <button type="button" class="btn btn-outline--base add-widget" data-key="Refresh">@lang('Refresh')</button>
                                            <!-- <button type="button" class="btn btn-outline--base add-widget" data-key="LoadPageFull">@lang('Wait For Page to Fully Load')</button>
                                            <button type="button" class="btn btn-outline--base add-widget" data-key="NavigateForward">@lang('Navigate Forward')</button> -->
                                            <button type="button" class="btn btn-outline--base add-widget" data-key="NavigateBack">@lang('Navigate Back')</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <label class="form--label col-sm-12">
                                @lang('Delay')
                                <br>
                                <small class="text--base" style="font-size: 0.75rem;">@lang('Wait Time After Each Visit in Seconds')</small>
                            </label>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Min')<small class="text--base" style="font-size: 0.75rem;">@lang(' (in seconds)')</small></label>
                                <input type="number" name="min_delay" class="form-control me-2 w-50" placeholder="Min Delay" min="0" value="{{ $campaign->min_delay }}">
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Max')<small class="text--base" style="font-size: 0.75rem;">@lang(' (in seconds)')</small></label>
                                <input type="number" name="max_delay" class="form-control me-2 w-50" placeholder="Max Delay" min="0" value="{{ $campaign->max_delay }}">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-sm-4">
                                <label class="form--label">@lang('Active Users')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Available: '){{auth()->user()->user_limit - auth()->user()->used_limit}} / @lang('Used: '){{auth()->user()->used_limit}}</small></label>
                                <input type="number" name="active_users" class="form-control me-2 w-50" placeholder="Max Visits" min="1" max="{{auth()->user()->user_limit - auth()->user()->used_limit}}" value="{{ $campaign->active_users }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="form--label">@lang('Max Visits')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Set 0 for unlimited visits')</small></label>
                                <input type="number" name="max_visits" class="form-control me-2 w-50" placeholder="Max Visits" min="1" value="{{ $campaign->max_visits }}">
                            </div>
                            <div class="form-group col-sm-4">
                                <label class="form--label">@lang('Time Out')<br><small class="text--base" style="font-size: 0.75rem;">@lang(' Page load timeout in seconds')</small></label>
                                <input type="number" name="timeout" class="form-control me-2 w-50" placeholder="Max Timeout" min="0" value="{{ $campaign->timeout }}">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <label class="form--label">@lang('Proxy Settings')</label>
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Free Proxy')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleFreeProxy" class="btn btn--primary me-2 w-50" disabled="">{{ $campaign->free_proxy_enabled ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="free_proxy_enabled" id="free_proxy_enabled" value="{{ $campaign->free_proxy_enabled }}" disabled="">
                                </div><br>
                                <select id="freeProxyInput" class="form--control w-50" name="free_proxy_country" disabled="">
                                    <option value="us" {{ $campaign->free_proxy_country === 'us' ? 'selected' : '' }}>Worldwide</option>
                                    <option value="uk" {{ $campaign->free_proxy_country === 'uk' ? 'selected' : '' }}>United States</option>
                                    <!-- Add more countries as needed -->
                                </select>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Custom Proxy')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleCustomProxy" class="btn btn--base me-2 w-50">{{ $campaign->custom_proxy_enabled ? 'Enabled' : 'Disabled' }}</button>
                                    <input type="hidden" name="custom_proxy_enabled" id="custom_proxy_enabled" value="{{ $campaign->custom_proxy_enabled }}">
                                </div>
                                <label class="form--label text--base" style="font-size: 0.75rem;">@lang('IP:PORT or IP:PORT:USERNAME:PASSWORD')</label>
                                <textarea id="customProxyInput" class="form--control" name="custom_proxies" rows="3" placeholder="Enter custom proxies (each per line)">{{ $campaign->custom_proxies ? implode("\n", json_decode($campaign->custom_proxies)) : '' }}</textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-center align-items-center">
                            <button id="createButton" class="btn btn-outline--base w-50 h-45 mt-3" type="submit">@lang('Update')</button>
                        </div>
                    </form>
                    <div id="proxyError" class="alert alert-danger d-none mt-2">@lang('Please enable at least one proxy option.')</div>
                </div>
            </div>
        </div>
    </div>
@stop

@push('script-lib')
<script src="{{ asset('assets/templates/user/js/jquery-ui.js') }}"></script>
@endpush

@push('script')
<script>
(function($) {
    "use strict";
    var initialSections = getSectionKeys();

    function getSectionKeys() {
        return $(document).find('#page_sections input[name="secs[]"]').map(function() {
            return $(this).val();
        }).get();
    }

    function addInputField(key, index) {
        let inputFields = '';
        if (key === 'URL') {
            inputFields = `<input type="text" name="url[${index}]" class="form-control mt-2" placeholder="Enter URL">`;
        } else if (key === 'Wait') {
            inputFields = `<div class="d-flex mt-2"><input type="number" name="min_wait[${index}]" class="form-control me-2" placeholder="Min Wait Time"> \
                        <input type="number" name="max_wait[${index}]" class="form-control" placeholder="Max Wait Time"></div>`;
        } else if (key === 'Scroll') {
            inputFields = `<div class="d-flex justify-content-between mt-2">\
                            <select name="scroll_type[${index}]" class="form-control me-2">\
                                <option value="Random">Random</option>\
                                <option value="Up">Up</option>\
                                <option value="Down">Down</option>\
                            </select>\
                            <input type="number" name="scroll_percentage[${index}]" class="form-control" placeholder="Percentage" min="0" max="100">\
                        </div>`;
        } else if (key === 'Click') {
            inputFields = `<div class="d-flex justify-content-between mt-2">\
                            <select name="click_type[${index}]" class="form-control me-2">\
                                <option value="Internal">Internal</option>\
                                <option value="External">External</option>\
                                <option value="Random">Random</option>\
                                <option value="Adsense">Adsense</option>\
                            </select>\
                            <input type="number" name="click_percentage[${index}]" class="form-control" placeholder="Percentage" min="0" max="100">\
                        </div>`;
        } else if (key === 'Refresh' || key === 'NavigateForward' || key === 'NavigateBack' || key === 'LoadPageFull') {
            inputFields = `<input type="hidden" name="${key.toLowerCase()}[${index}]" value="Enabled">`;
        }
        return inputFields;
    }

    $(document).on('click', '.add-widget', function() {
        const key = $(this).data('key');
        const index = $('#page_sections li').length; // Use the current number of items as the index
        const inputFields = addInputField(key, index);
        const element = $(`<li class="highlight icon-move" data-key="${key}">
            <i class="sortable-icon"></i>
            <span class="d-inline-block me-auto text--base">${$(this).text()}</span>
            <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
            ${inputFields}
            <input type="hidden" name="secs[]" value="${key}">
            <input type="hidden" name="order[]" value="${index}">
        </li>`);
        $('#page_sections').append(element);
        handleShowSubmissionAlert();
        watchState();
        $("#page_sections").sortable("refresh");
    });

    $("#page_sections").sortable({
        items: "li:not(.empty-state)",
        update: () => handleShowSubmissionAlert()
    });

    $(document).on('click', ".remove-icon", function() {
        $(this).parent('.highlight').remove();
        handleShowSubmissionAlert();
        watchState();
        reindexSections();
    });

    function reindexSections() {
    $('#page_sections li').each(function(index) {
        $(this).find('input, select').each(function() {
            const name = $(this).attr('name');
            const newName = name.replace(/\[\d+\]/, `[${index}]`);
            $(this).attr('name', newName);
        });
        $(this).find('input[name="order[]"]').val(index);
    });
}

    function watchState() {
        if ($('#page_sections').children().length == 0) {
            $('#page_sections').html(`<li class="empty-state">
                <span>@lang('Your script will appear here')</span>
            </li>`);
        } else {
            $('#page_sections .empty-state').remove();
        }
    }

    function handleShowSubmissionAlert() {
        const arraysAreEqual = (arr1, arr2) => JSON.stringify(arr1) === JSON.stringify(arr2);
        if (!arraysAreEqual(initialSections, getSectionKeys())) {
            $('.submitRequired').removeClass('d-none');
        }
    }

    // Toggle logic for referral and search extensions
    $('#toggleReferral').click(function() {
        if ($(this).hasClass('btn--primary')) {
            if ($('#toggleSearch').hasClass('btn--base')) {
                alert('Only one extension can be enabled at a time.');
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
                alert('Only one extension can be enabled at a time.');
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
            if ($('#toggleCustomProxy').hasClass('btn--base')) {
                alert('Only one proxy option can be enabled at a time.');
            } else {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#free_proxy_enabled').val(1);
                $('#custom_proxy_enabled').val(0);
                $('#customProxyInput').removeAttr('required');
                $('#freeProxyInput').attr('required', true);
            }
        } else {
            $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
            $('#free_proxy_enabled').val(0);
            $('#freeProxyInput').removeAttr('required');
        }
    });

    $('#toggleCustomProxy').click(function() {
        if ($(this).hasClass('btn--primary')) {
            if ($('#toggleFreeProxy').hasClass('btn--base')) {
                alert('Only one proxy option can be enabled at a time.');
            } else {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                $('#custom_proxy_enabled').val(1);
                $('#free_proxy_enabled').val(0);
                $('#customProxyInput').attr('required', true);
                $('#freeProxyInput').removeAttr('required');
            }
        } else {
            $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
            $('#custom_proxy_enabled').val(0);
            $('#customProxyInput').removeAttr('required');
        }
    });

    // Form submission validation for proxy settings
    $('#campaignForm').submit(function(event) {
        if (!$('#toggleFreeProxy').hasClass('btn--base') && !$('#toggleCustomProxy').hasClass('btn--base')) {
            event.preventDefault();
            $('#proxyError').removeClass('d-none');
        } else {
            $('#proxyError').addClass('d-none');
            $('#createButton').prop('disabled', true).addClass('btn--disabled').text('Updating Campaign, Please wait...');
        }
    });

    // Toggle logic for URL order
    $('#toggleUrlOrder').click(function() {
        if ($(this).hasClass('btn--primary')) {
            $(this).removeClass('btn--primary').addClass('btn--base').text('Shuffle URLs');
            $('#urlOrderInput').val('shuffle');
        } else {
            $(this).removeClass('btn--base').addClass('btn--primary').text('One by One');
            $('#urlOrderInput').val('one_by_one');
        }
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

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .highlight {
            padding: 1rem;
            background-color: #fdfdfd;
            border: 1px solid rgb(0 0 0 / 6%);
            border-radius: .5rem;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .remove-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            color: #777777;
        }

        .remove-icon:hover {
            color: #ea5455;
        }

        .empty-state {
            border: 2px dotted #ccc;
            text-align: center;
            padding: 3rem;
            cursor: default;
        }

        #page_sections.dropping {
            border: 2px dotted #ccc;
            padding: 0 1rem;
        }

        .btn--disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }

        @media(max-width: 767px) {
            .highlight {
                padding: 0.5rem;
            }
            .grid-container {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
            .btn-outline--base {
                padding: 5px;
                font-size: 12px;
            }
        }

        .alert {
            margin-top: 20px;
            font-size: 1rem;
        }

        /* Add this to fix the overlapping issue */
        .modal-dialog {
            max-width: 100%;
            margin: 1.75rem auto;
        }
    </style>
@endpush
