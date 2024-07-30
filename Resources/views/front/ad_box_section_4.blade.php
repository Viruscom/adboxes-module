<section class="section">
    <div class="section-head section-head-alt">
        <div class="label" data-aos="fade-up" data-aos-delay="50"></div>

        <h3 data-aos="fade-up" data-aos-delay="100">{{ __('front.tasety_chronicles') }}<span class="color-green">:</span></h3>

        <h4 data-aos="fade-up" data-aos-delay="150"><span class="color-green">{{ __('front.fresh') }}</span> {{ __('front.from_the_kitchen') }}</h4>

        <img src="{{ asset('front/assets/icons/heart-1.svg') }}" alt="" data-aos="fade-up" data-aos-delay="200">
    </div>
</section>

<div class="boxes boxes-type-4 slider-element">
    @foreach($viewArray['adBoxesFrontAll'][4] as $adBox)
        @php
            if (is_null($adBox->translate($languageSlug))) {
                continue;
            }
        @endphp
        <div class="box" data-aos="fade-up">
            <div class="box-date">
                @if($adBox->getFromDate('d.m.') !== '')
                    <span>{{$adBox->getFromDate('d.m.')}}</span>
                @endif

                @if($adBox->getToDate('d.m.') !== '')
                    <span>{{$adBox->getToDate('d.m.')}}</span>
                @endif
            </div>
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

                <div class="box-image-inner">
                    <a href="{{ $adBox->getUrl($languageSlug) }}"></a>

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
