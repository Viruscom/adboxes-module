@php use Modules\Adboxes\Models\AdBoxButton; @endphp
<section class="section">
    <div class="boxes boxes-type-2">
        @foreach($viewArray['adBoxesFrontAll'][2] as $adBox)
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

    @php
        $adBoxButton = AdBoxButton::getTranslation(2, $languageSlug);
    @endphp
    @if(!is_null($adBoxButton) && $adBoxButton->url)
        <div class="section-actions" data-aos="fade-up" data-aos-delay="50">
            <a href="{{ $adBoxButton->getUrl($languageSlug) }}" class="btn btn-main btn-main-alt">
                {!! $adBoxButton->title !!}

                <i class="arrow-right"></i>
            </a>
        </div>
    @endif
</section>
