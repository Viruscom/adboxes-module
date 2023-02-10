<section class="section section-prod">
    <div class="titles-wrapper">
        <div class="title-row" data-aos="fade-up" data-aos-delay="40"><h3 class="section-title-alt">{!! trans('messages.for_a_perfect') !!}</h3></div>
        <div class="title-row" data-aos="fade-up" data-aos-delay="150"><h3 class="section-title-alt">{{ trans('messages.first_imp') }}</h3></div>
    </div>
    <div class="boxes-type-2">
        @foreach($viewArray['adBoxesFrontAll'][2] as $adBox)
            <div class="box" data-aos="fade" data-aos-delay="40">
                <div class="box-top">
                    <a href="{{ $adBox->getUrl() }}"></a>

                    <div class="box-image parent-image-wrapper">
                        <img src="{{ $adBox->imageUrl() }}" alt="{{ $adBox->title }}" class="bg-image">
                    </div>
                </div>

                <div class="box-content">
                    <a href="{{ $adBox->getUrl() }}">{{ $adBox->type }}</a>

                    <h3>
                        <a href="{{ $adBox->getUrl() }}">{{ $adBox->title }}</a>
                    </h3>
                    <p>{!! $adBox->getAnnounce() !!}</p>
                </div>
            </div>
        @endforeach

    </div>

    <div class="custom-arrows-wrapper boxes-type-2-arrows">
        <a href="" class="custom-arrow custom-prev">
            <img src="{{ asset('website/assets/icons/arrow-slider-grey.svg') }}" alt="" width="45" height="40">
            <img src="{{ asset('website/assets/icons/arrow-slider-red.svg') }}" alt="" width="45" height="40">
        </a>

        <a href="" class="custom-arrow custom-next">
            <img src="{{ asset('website/assets/icons/arrow-slider-grey.svg') }}" alt="" width="45" height="40">
            <img src="{{ asset('website/assets/icons/arrow-slider-red.svg') }}" alt="" width="45" height="40">
        </a>
    </div>

    {{--    @php--}}
    {{--        $adBoxButton = \App\Models\AdBoxButton::getTranslation(2, $language->id);--}}
    {{--    @endphp--}}
    {{--    @if($adBoxButton && $adBoxButton->url)--}}
    {{--        <div class="section-actions">--}}
    {{--            <a href="{{ (!is_null($adBoxButton)) ? ($adBoxButton->external_url) ? $adBoxButton->url : url($adBoxButton->url) :''}}" class="btn" data-aos="fade-up" data-aos-delay="100">{!! $adBoxButton->title !!}</a>--}}
    {{--        </div>--}}
    {{--    @endif--}}
</section>

<section class="section-kareta-2">
    <div class="infinite-text-loop">
        <div class="inner">
            <p>discover more about<span>Crozia</span></p>
            <p>discover more about<span>Crozia</span></p>
        </div>
    </div>

    <div class="kareta2">
        <div class="box">
            <div class="box-image-wrapper" data-aos="fade-up" data-aos-delay="50">
                <a href=""></a>

                <div class="box-image parent-image-wrapper">
                    <img src="assets/images/kareta2-1.jpg" alt="" class="bg-image">
                </div>
            </div>

            <div class="box-content" data-aos="fade-up" data-aos-delay="200">
                <h3>
                    <a href="">Illuminating day cream</a>
                </h3>

                <div class="box-inner">
                    <p>
                        Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore etsd dhha dolore magna aliqua atenim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>

                    <div class="box-actions">
                        <div class="box-price">
                            <p class="old-price">
                                <span>126.00</span> BGN </p>

                            <p>
                                <span>104.00</span> BGN </p>
                        </div>

                        <div class="box-icons">
                            <div class="icon-wrapper tooltop-box hover-images">
                                <div class="tooltip-info">
                                    <p>add to wishlist</p>
                                </div>

                                <a href="">
                                    <img src="assets/icons/heart-alt.svg" alt="">

                                    <img src="assets/icons/heart-alt-hover.svg" alt="">
                                </a>
                            </div>

                            <div class="icon-wrapper tooltop-box hover-images">
                                <div class="tooltip-info tooltip-info-alt">
                                    <p>add to cart</p>
                                </div>

                                <a href="">
                                    <img src="assets/icons/cart-alt.svg" alt="">

                                    <img src="assets/icons/cart-alt-hover.svg" alt="">
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="" class="link-more link-more-alt">see more</a>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-image-wrapper" data-aos="fade-up" data-aos-delay="50">
                <a href=""></a>

                <div class="box-image parent-image-wrapper">
                    <img src="assets/images/kareta2-1.jpg" alt="" class="bg-image">
                </div>
            </div>

            <div class="box-content" data-aos="fade-up" data-aos-delay="200">
                <h3>
                    <a href="">Nourishing night cream</a>
                </h3>

                <div class="box-inner">
                    <p>
                        Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem... </p>

                    <a href="" class="link-more link-more-alt">see more</a>
                </div>
            </div>
        </div>

        <div class="box">
            <div class="box-image-wrapper" data-aos="fade-up" data-aos-delay="50">
                <a href=""></a>

                <div class="box-image parent-image-wrapper">
                    <img src="assets/images/kareta2-1.jpg" alt="" class="bg-image">
                </div>
            </div>

            <div class="box-content" data-aos="fade-up" data-aos-delay="200">
                <h3>
                    <a href="">anti-ageing &amp; illuminating serum</a>
                </h3>

                <div class="box-inner">
                    <p>
                        Dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore etsd dhha dolore magna aliqua atenim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>

                    <a href="" class="link-more link-more-alt">see more</a>
                </div>
            </div>
        </div>
    </div>
</section>
