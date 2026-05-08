@php
    $actionContent = getContent('action.content', true);
@endphp
<!-- action-section start -->
<section class="action-section ptb-80 bg_img"
    data-background="{{ getImage($activeTemplateTrue . 'images/action-bg.png', '1920x510') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12 text-center">
                <div class="action-content">
                    <span class="sub-title">{{ __(@$actionContent->data_values->heading) }}</span>
                    <h2 class="title">{{ __(@$actionContent->data_values->sub_heading) }}</h2>
                    <div class="action-btn">
                        <a href="{{ @$actionContent->data_values->button_url }}"
                            class="btn--base-active">{{ __(@$actionContent->data_values->button) }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- action-section end -->
