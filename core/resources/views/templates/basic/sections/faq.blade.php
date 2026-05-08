@php
    $faqtureContent = getContent('faq.content', true);
    $faqElements = getContent('faq.element', orderById: true);
@endphp

<div class="faq pt-85 pb-85">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-6">
                <div class="faq-content">
                    <div class="section-heading">
                        <span class="section-heading__subtitle"> {{ __(@$faqtureContent->data_values->section_title) }} </span>
                        <h2 class="section-heading__title" s-break="-1" s-color="bg--green">{{ __(@$faqtureContent->data_values->heading) }}</h2>
                        <p class="section-heading__desc"> {{ __(@$faqtureContent->data_values->heading) }}</p>
                    </div>
                    <div class="faq-content__bottom">
                        <h5>
                            {{ __(@$faqtureContent->data_values->title) }}</h2>
                        </h5>
                        <a href="mailto:{{ @$faqtureContent->data_values->email }}"> {{ @$faqtureContent->data_values->email }} </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="accordion custom--accordion" id="accordionExample">
                    @foreach ($faqElements as $key => $faqElement)
                        <div class="accordion-item">
                            <h5 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne_{{ $key }}" aria-expanded="false"
                                    aria-controls="collapseOne_{{ $key }}">
                                    {{ __(@$faqElement->data_values->question) }}
                                </button>
                            </h5>
                            <div id="collapseOne_{{ $key }}" class="accordion-collapse collapse"
                                aria-labelledby="headingOne_{{ $key }}" data-bs-parent="#accordionExample" style="">
                                <div class="accordion-body">
                                    <p class="accordion-body__desc">
                                        {{ __(@$faqElement->data_values->answer) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
