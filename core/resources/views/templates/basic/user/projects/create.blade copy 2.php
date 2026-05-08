@extends($activeTemplate.'layouts.master')
@section('content')
    <div class="row g-1 g-lg-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> @lang('New Project')</h4>
                </div>

                <div class="card-body">
                    <form action="" method="post">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12 col-md-8">
                                <label class="form--label">@lang('Enter URL')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Enter URL/URLs with HTTP or HTTPS Prefix') https://example.com</small></label>
                                <textarea class="form--control" name="urls" rows="2" placeholder="Enter URL(https://example.com)" required></textarea>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('URL Order')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleUrlOrder" class="btn btn--primary me-2 w-100">One by One</button>
                                </div>
                                <hr>
                                <div class="form-group">
                                    <label class="form--label">@lang('Devices')</label>
                                    <select id="devices" class="form--control" name="devices">
                                        <option value="desktop">Desktop</option>
                                        <option value="mobile">Mobile</option>
                                        <option value="random" selected>Random</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Referrer Extensions')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleReferral" class="btn btn--primary me-2 w-100">Disabled</button>
                                </div>
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Search Extensions')</label>
                                <div class="d-flex align-items-center">
                                    <button type="button" id="toggleSearch" class="btn btn--primary me-2 w-100">Disabled</button>
                                </div>                                
                            </div>
                            <div class="form-group col-12 col-md-4">
                                <label class="form--label">@lang('Search Engine')</label>
                                <select id="search_ext" class="form--control" name="search_ext">
                                    <option value="google">Google</option>
                                    <option value="google_maps">Google Maps/My Business</option>
                                    <option value="bing">Bing</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Referrer URLs')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Traffic Source URLs with HTTP or HTTPS Prefix') https://example.com</small></label>
                                <textarea id="referrer_urls" class="form--control" name="referrer_urls" rows="3" placeholder="Add the referrer URLs/websites (each per line)  https://example.com"></textarea>
                            </div>
                            <div class="form-group col-12 col-md-6">
                                <label class="form--label">@lang('Search Keywords')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Keywords from Search Console or Bing Master indexed in top 100.')</small></label>
                                <textarea id="search_keywords" class="form--control" name="search_keywords" rows="3" placeholder="Add the keywords (each per line)"></textarea>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <label class="form--label">@lang('Flow')</label>
                            <ol id="page_sections" class="simple_with_drop vertical sec-item">
                                <li class="empty-state">
                                    <span>@lang('Drag & drop your section here')</span>
                                </li>
                            </ol>
                        </div>
                        <hr>
                        <div class="row">
                            <label class="form--label">@lang('Delay')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Wait Time After Each Visit in Seconds')</small></label>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Min')<small class="text--base" style="font-size: 0.75rem;">@lang(' (in seconds)')</small></label>
                                <input type="number" name="min_delay[]" class="form-control me-2" placeholder="Min Delay" min="0" value="10">
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Max')<small class="text--base" style="font-size: 0.75rem;">@lang(' (in seconds)')</small></label>
                                <input type="number" name="max_delay[]" class="form-control" placeholder="Max Delay" min="0" value="30">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Max Visits')<br><small class="text--base" style="font-size: 0.75rem;">@lang('Set 0 for unlimited visits')</small></label>
                                <input type="number" name="max_visits" class="form-control me-2" placeholder="Max Visits" value="100">
                            </div>
                            <div class="form-group col-sm-6">
                                <label class="form--label">@lang('Time Out')<br><small class="text--base" style="font-size: 0.75rem;">@lang(' Page load timeout in seconds')</small></label>
                                <input type="number" name="max_delay[]" class="form-control" placeholder="Max Delay" min="0" value="60">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <label class="form--label">@lang('Proxy')</label>
                            <div class="form-group col-12">
                                <div class="d-flex align-items-center">
                                    <div class="proxy-toggle-switch">
                                        <input type="checkbox" id="toggleProxy" class="proxy-toggle-input">
                                        <label for="toggleProxy" class="proxy-toggle-label">
                                            <span class="proxy-toggle-inner" data-on="Custom Proxy" data-off="Free Proxy"></span>
                                            <span class="proxy-toggle-switch"></span>
                                        </label>
                                    </div>
                                </div>
                                <div id="freeProxySection">
                                    <label class="form--label">@lang('Free Proxy')</label>
                                    <select id="freeProxyInput" class="form--control">
                                        <option value="us">United States</option>
                                        <option value="uk">United Kingdom</option>
                                        <option value="ca">Canada</option>
                                        <option value="au">Australia</option>
                                        <!-- Add more countries as needed -->
                                    </select>
                                </div>
                                <div id="customProxySection" class="d-none">
                                    <label class="form--label">@lang('Custom Proxy')</label>
                                    <textarea id="customProxyInput" class="form--control" name="custom_proxy" rows="3" placeholder="Enter custom proxies (each per line)"></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <button class="btn btn--base w-100 h-45 mt-3" type="submit">@lang('Create')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">@lang('Sections')</h4>
                    <small class="text--primary">@lang('Drag the section to the left side you want to show on the page.')</small>
                </div>

                <div class="card-body">
                    <ol id="sections_items" class="simple_with_no_drop grid-container">
                        <li class="highlight icon-move" data-key="URL">
                            <i class="sortable-icon"></i>
                            <span class="d-inline-block me-auto">URL</span>
                            <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                        </li>
                        <li class="highlight icon-move" data-key="Wait">
                            <i class="sortable-icon"></i>
                            <span class="d-inline-block me-auto">Wait</span>
                            <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                        </li>
                        <li class="highlight icon-move" data-key="Scroll">
                            <i class="sortable-icon"></i>
                            <span class="d-inline-block me-auto">Scroll</span>
                            <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                        </li>
                        <li class="highlight icon-move" data-key="Click">
                            <i class="sortable-icon"></i>
                            <span class="d-inline-block me-auto">Click</span>
                            <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@push('script-lib')
<script src="{{ asset('assets/templates/user/js/jquery-ui.min.js') }}"></script>
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

        function addInputField(key) {
            let inputFields = '';
            if (key === 'URL') {
                inputFields = '<input type="text" name="url[]" class="form-control mt-2" placeholder="Enter URL">';
            } else if (key === 'Wait') {
                inputFields = '<div class="d-flex mt-2"><input type="number" name="min_wait[]" class="form-control me-2" placeholder="Min Wait Time"> \
                               <input type="number" name="max_wait[]" class="form-control" placeholder="Max Wait Time"></div>';
            } else if (key === 'Scroll') {
                inputFields = '<div class="d-flex justify-content-between mt-2">\
                                <select name="scroll_type[]" class="form-control me-2">\
                                    <option value="Random">Random</option>\
                                    <option value="Up">Up</option>\
                                    <option value="Down">Down</option>\
                                </select>\
                                <input type="number" name="scroll_percentage[]" class="form-control" placeholder="Percentage" min="0" max="100">\
                              </div>';
            } else if (key === 'Click') {
                inputFields = '<div class="d-flex justify-content-between mt-2">\
                                <select name="click_type[]" class="form-control me-2">\
                                    <option value="Internal">Internal</option>\
                                    <option value="External">External</option>\
                                    <option value="Random">Random</option>\
                                    <option value="Adsense">Adsense</option>\
                                </select>\
                                <input type="number" name="click_percentage[]" class="form-control" placeholder="Percentage" min="0" max="100">\
                              </div>';
            }
            return inputFields;
        }

        $("#page_sections").sortable({
            items: "li:not(.empty-state)",
            update: () => handleShowSubmissionAlert()
        });

        $("#sections_items li").draggable({
            stop: function(event, ui) {
                const element = ui.helper;
                const key = element.data('key');
                const inputFields = addInputField(key);
                element.append(`<input type="hidden" name="secs[]" value="${key}">`);
                element.append(inputFields);
                element.css('height', 'auto'); // Adjust height dynamically
                element.css('display', 'flex').css('flex-direction', 'column');

                if ($('#page_sections').children().length == 0) {
                    watchState(true);
                }
                handleShowSubmissionAlert();
                $('#page_sections').removeClass('dropping');
            },
            start: function(event, ui, offset) {
                const height = $('.empty-state').outerHeight();

                if ($('#page_sections').children().length == 1) {
                    $('.empty-state').remove();
                }

                $('#page_sections').addClass('dropping').css('min-height', `${height}px`);
            },
            helper: function() {
                var originalElement = $(this);
                var originalWidth = '100%';
                var clonedElement = originalElement.clone();
                clonedElement.css('width', originalWidth);
                return clonedElement;
            },
            connectToSortable: '#page_sections'
        });

        $("#page_sections").droppable({
            accept: '#sections_items li',
            drop: function(event, ui) {
                let originalWidth = $(event.target).width();
                $(this).append(ui.draggable);
                ui.draggable.removeAttr('style');
                ui.draggable.removeClass();
                ui.draggable.addClass('highlight icon-move item ui-sortable-handle').css('height', 'auto').css('display', 'flex').css('flex-direction', 'column');
            }
        });

        $(document).on('click', ".remove-icon", function() {
            $(this).parent('.highlight').remove();
            handleShowSubmissionAlert();
            watchState();
        });

        function watchState(override = false) {
            if ($('#page_sections').children().length == 0 || override) {
                $('#page_sections').html(`<li class="empty-state">
                    <span>@lang('Drag & drop your section here')</span>
                </li>`);
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
                }
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
            }
        });

        $('#toggleSearch').click(function() {
            if ($(this).hasClass('btn--primary')) {
                if ($('#toggleReferral').hasClass('btn--base')) {
                    alert('Only one extension can be enabled at a time.');
                } else {
                    $(this).removeClass('btn--primary').addClass('btn--base').text('Enabled');
                }
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('Disabled');
            }
        });

        // Toggle logic for proxy settings
        $('#toggleProxy').change(function() {
            if (this.checked) {
                $('#freeProxySection').addClass('d-none');
                $('#customProxySection').removeClass('d-none');
            } else {
                $('#customProxySection').addClass('d-none');
                $('#freeProxySection').removeClass('d-none');
            }
        });

        // Toggle logic for URL order
        $('#toggleUrlOrder').click(function() {
            if ($(this).hasClass('btn--primary')) {
                $(this).removeClass('btn--primary').addClass('btn--base').text('Shuffle URLs');
            } else {
                $(this).removeClass('btn--base').addClass('btn--primary').text('One by One');
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

        .proxy-toggle-switch {
            position: relative;
            display: inline-block;
            width: 100px;
            height: 34px;
        }

        .proxy-toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .proxy-toggle-label {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            border-radius: 34px;
            cursor: pointer;
        }

        .proxy-toggle-inner {
            display: block;
            width: 200%;
            height: 100%;
            padding-left: 100%;
            box-sizing: border-box;
            transition: .4s;
            background-color: #2196F3;
            border-radius: 34px;
            position: relative;
        }

        .proxy-toggle-inner:before, .proxy-toggle-inner:after {
            display: block;
            position: absolute;
            width: 50%;
            height: 100%;
            content: '';
            line-height: 34px;
            text-align: center;
            color: white;
            font-size: 12px;
        }

        .proxy-toggle-inner:before {
            content: attr(data-off);
            left: 0;
        }

        .proxy-toggle-inner:after {
            content: attr(data-on);
            right: 0;
            opacity: 0;
        }

        .proxy-toggle-switch input:checked + .proxy-toggle-label .proxy-toggle-inner {
            transform: translateX(-50%);
            background-color: #4CAF50;
        }

        .proxy-toggle-switch input:checked + .proxy-toggle-label .proxy-toggle-inner:after {
            opacity: 1;
        }

        .proxy-toggle-switch input:checked + .proxy-toggle-label .proxy-toggle-inner:before {
            opacity: 0;
        }

        .proxy-toggle-switch input:checked + .proxy-toggle-label .proxy-toggle-switch {
            transform: translateX(66px);
        }

        .proxy-toggle-switch {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 30px;
            height: 30px;
            background-color: white;
            border-radius: 50%;
            transition: .4s;
        }

        @media(max-width: 767px) {
            .highlight {
                padding: 0.5rem;
            }
        }
    </style>
@endpush
