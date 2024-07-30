<section class="section section-services">
    <div class="section-head">
        <div class="label" data-aos="fade-up" data-aos-delay="50"></div>

        <h3 data-aos="fade-up" data-aos-delay="100">{{ __('front.good_service') }}<span class="color-red">,</span></h3>

        <h4 data-aos="fade-up" data-aos-delay="150">{{ __('front.for') }} <span class="color-red">{{ __('front.good') }}</span> {{ __('front.taste') }}</h4>

        <img src="{{ asset('front/assets/icons/heart-1.svg') }}" alt="" data-aos="fade-up" data-aos-delay="200">
    </div>

    <div class="boxes boxes-type-3">
        @foreach($viewArray['adBoxesFrontAll'][3] as $adBox)
            @php
                if (is_null($adBox->translate($languageSlug))) {
                    continue;
                }
            @endphp
            <div class="box" data-aos="fade-up">
                <div class="box-image-wrapper">
                    @if($adBox->getPrice() != '' || $adBox->getNewPrice() != '')
                        <div class="box-prices">
                            <div class="inner">
                                @if($adBox->getPrice() != '')
                                    <p class="{{ $adBox->getPrice() != '' && $adBox->getNewPrice() != '' ? 'old-price':'' }}">
                                        @if($adBox->from_price)
                                            <span>{{ __('front.from') }}</span>
                                        @endif

                                        <strong>{{ $adBox->getPrice() }}
                                            <span>{{ __('front.currency') }}</span>
                                        </strong>
                                    </p>
                                @endif

                                @if($adBox->getNewPrice() != '')
                                    <p>
                                        @if($adBox->from_new_price)
                                            <span>{{ __('front.from') }}</span>
                                        @endif

                                        <strong>{{ $adBox->getNewPrice() }}
                                            <span>{{ __('front.currency') }}</span>
                                        </strong>
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <a href="{{ $adBox->getUrl($languageSlug) }}"></a>

                    <div class="box-image-inner">
                        <div class="box-image parent-image-wrapper">
                            <img src="{{ $adBox->getFileUrl() }}" alt="{{ $adBox->title }}" class="bg-image">
                        </div>
                    </div>
                </div>

                <div class="box-content">
                    <div class="box-label {{ $adBox->getLabelColor() }}">{{ $adBox->label }}</div>

                    <h3>
                        <a href="{{ $adBox->getUrl($languageSlug) }}">{{ $adBox->title }}</a>
                    </h3>

                    <p>{!! $adBox->getAnnounce() !!}</p>

                    <div class="box-actions">
                        <div class="box-date">
                            @if($adBox->getFromDate('d.m.') !== '')
                                <span>{{$adBox->getFromDate('d.m.')}}</span>
                            @endif

                            @if($adBox->getToDate('d.m.') !== '')
                                <span>{{$adBox->getToDate('d.m.')}}</span>
                            @endif
                        </div>

                        <a href="{{ $adBox->getUrl($languageSlug) }}" class="link-more {{ $adBox->getLabelColor() }}">...{{ __('front.see_more') }}</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <img src="{{ asset('front/assets/icons/double-heart.svg') }}" alt="" data-aos="fade-up" data-aos-delay="50">
</section>
