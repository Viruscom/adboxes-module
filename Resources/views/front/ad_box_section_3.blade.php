<section class="section section-videos section-lines">
    <div class="titles-wrapper">
        <div class="title-row" data-aos="fade-up" data-aos-delay="40"><h3 class="section-title-alt">{!! trans('messages.video_accents') !!}</h3></div>
    </div>

    <div class="boxes-type-video">
        @foreach($viewArray['adBoxesFrontAll'][3] as $adBox)
            <div class="box" data-aos="fade" data-aos-delay="40">
                <div class="box-top">
                    <div class="box-image parent-image-wrapper">
                        <a href="{{ $adBox->getUrl() }}"></a>
                        <img src="{{ $adBox->getFileUrl() }}" alt="{{ $adBox->title }}" class="bg-image">
                    </div>
                </div>

                <div class="box-content">
                    <h3>
                        <a href="{{ $adBox->getUrl() }}">{{ $adBox->title }}</a>
                    </h3>
                    <p>{!! $adBox->getAnnounce() !!}</p>

                    <a href="{{ $adBox->getUrl() }}" class="btn btn-main">{{ trans('messages.see_more') }}</a>
                </div>
            </div>
        @endforeach
    </div>

    {{--    @php--}}
    {{--        $adBoxButton = \App\Models\AdBoxButton::getTranslation(3, $language->id);--}}
    {{--    @endphp--}}
    {{--    @if($adBoxButton && $adBoxButton->url)--}}
    {{--        <div class="section-actions">--}}
    {{--            <a href="{{ (!is_null($adBoxButton)) ? ($adBoxButton->external_url) ? $adBoxButton->url : url($adBoxButton->url) :''}}" class="btn" data-aos="fade-up" data-aos-delay="100">{!! $adBoxButton->title !!}</a>--}}
    {{--        </div>--}}
    {{--    @endif--}}
</section>

<section class="section section-latest-news">
    <h3 class="section-title">
        <strong data-aos="fade-up" data-aos-delay="50">Last events &amp;</strong>

        <i data-aos="fade-up" data-aos-delay="200">news</i>
    </h3>

    <ul class="slider-news kareta3">
        <li class="slide box" data-aos="fade-up">
            <div class="box-image-wrapper" data-aos="fade-up" data-aos-delay="150">
                <h3><a href="">About us</a></h3>

                <div class="box-image parent-image-wrapper">
                    <img src="assets/images/kareta3-1.jpg" alt="" class="bg-image">

                </div>
            </div>

            <div class="box-content" data-aos="fade-up" data-aos-delay="200">
                <div class="box-inner">
                    <p class="date">24.03.2020</p>

                    <h3><a href="">Lorem ipsum dolor sit amet Nectetur</a></h3>

                    <p>Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore etsd dhha dolore magna aliqua atenim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip</p>

                    <a href="" class="link-more">see more</a>

                </div>
            </div>
        </li>

        <li class="slide box" data-aos="fade-up">
            <div class="box-image-wrapper" data-aos="fade-up" data-aos-delay="150">
                <h3><a href="">our blog</a></h3>

                <div class="box-image parent-image-wrapper">
                    <img src="assets/images/kareta3-1.jpg" alt="" class="bg-image">
                </div>
            </div>

            <div class="box-content" data-aos="fade-up" data-aos-delay="200">
                <div class="box-inner">
                    <p class="date">08.03.2020</p>

                    <h3><a href="">sit amet Nectetur</a></h3>

                    <p>Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore etsd dhha dolore magna aliqua atenim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip</p>

                    <a href="" class="link-more">see more</a>

                </div>
            </div>
        </li>

        <li class="slide box" data-aos="fade-up">
            <div class="box-image-wrapper" data-aos="fade-up" data-aos-delay="150">
                <h3><a href="">About us</a></h3>

                <div class="box-image parent-image-wrapper">
                    <img src="assets/images/kareta3-2.jpg" alt="" class="bg-image">
                </div>
            </div>

            <div class="box-content" data-aos="fade-up" data-aos-delay="200">
                <div class="box-inner">
                    <h3><a href="">dolor sit amet Nectetur adipisicig elit sed do eiusm</a></h3>

                    <p>Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore etsd dhha dolore magna aliqua atenim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip</p>

                    <a href="" class="link-more">see more</a>

                </div>
            </div>
        </li>

        <li class="slide box" data-aos="fade-up">
            <div class="box-image-wrapper" data-aos="fade-up" data-aos-delay="150">
                <h3><a href="">About us</a></h3>

                <div class="box-image parent-image-wrapper">
                    <img src="assets/images/kareta3-1.jpg" alt="" class="bg-image">
                </div>
            </div>

            <div class="box-content" data-aos="fade-up" data-aos-delay="200">
                <div class="box-inner">
                    <p class="date">24.03.2020</p>

                    <h3><a href="">Lorem ipsum dolor sit amet Nectetur</a></h3>

                    <p>Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore etsd dhha dolore magna aliqua atenim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip</p>

                    <a href="" class="link-more">see more</a>

                </div>
            </div>
        </li>
    </ul>
</section>
