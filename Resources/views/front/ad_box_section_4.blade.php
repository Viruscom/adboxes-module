<section class="section section-videos section-lines">
    <div class="titles-wrapper">
        <div class="title-row" data-aos="fade-up" data-aos-delay="40"><h3 class="section-title-alt">{!! trans('messages.video_accents') !!}</h3></div>
    </div>

    <div class="boxes-type-video">
        @foreach($viewArray['adBoxesFrontAll'][4] as $adBox)
            <div class="box" data-aos="fade" data-aos-delay="40">
                <div class="box-top">
                    <div class="box-image parent-image-wrapper">
                        <a href="{{ $adBox->getUrl() }}"></a>
                        <img src="{{ $adBox->imageUrl() }}" alt="{{ $adBox->title }}" class="bg-image">
                    </div>
                </div>

                <div class="box-content">
                    <h3>
                        <a href="{{ $adBox->getUrl() }}">{{ $adBoxTranslation->title }}</a>
                    </h3>
                    <p>{!! $adBox->getAnnounce() !!}</p>

                    <a href="{{ $adBox->getUrl() }}" class="btn btn-main">{{ trans('messages.see_more') }}</a>
                </div>
            </div>
            @php
                $i++
            @endphp
        @endforeach
    </div>

    {{--    @php--}}
    {{--        $adBoxButton = \App\Models\AdBoxButton::getTranslation(4, $language->id);--}}
    {{--    @endphp--}}
    {{--    @if($adBoxButton && $adBoxButton->url)--}}
    {{--        <div class="section-actions">--}}
    {{--            <a href="{{ (!is_null($adBoxButton)) ? ($adBoxButton->external_url) ? $adBoxButton->url : url($adBoxButton->url) :''}}" class="btn" data-aos="fade-up" data-aos-delay="100">{!! $adBoxButton->title !!}</a>--}}
    {{--        </div>--}}
    {{--    @endif--}}
</section>
