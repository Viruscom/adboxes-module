<section class="section-kareta-1">
    <div class="infinite-text-loop">
        <div class="inner">
            <p>get to know our<span>products</span></p>
            <p>get to know our<span>products</span></p>
        </div>
    </div>

    <div class="kareta1">
        @foreach($viewArray['adBoxesFrontAll'][1] as $adBox)
            <div class="box" data-aos="fade-up">
                <div class="box-image-wrapper">
                    <div class="box-status">
                        New
                    </div>

                    <a href=""></a>

                    <div class="box-image parent-image-wrapper">
                        <img src="{{ $adBox->getUrl() }}" alt="" class="bg-image">
                    </div>
                </div>

                <div class="box-content">
                    <h3>
                        <a href="">Illuminating day cream</a>
                    </h3>

                    <p>
                        Dolor sit amet, consectetur adipisicing elit, sed eiusmod tempor incididunt ut labore etsd dhha dolore magna aliqua atenim ad minim ven quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. </p>

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
                                    <img src="{{ asset('front/assets/icons/heart-alt.svg') }}" alt="">

                                    <img src="{{ asset('front/assets/icons/heart-alt-hover.svg') }}" alt="">
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
                </div>

                <a href="" class="link-more">see more</a>
            </div>


            <div class="box " data-aos="fade" data-aos-delay="40">
                <div class="box-image parent-image-wrapper">
                    <img src="{{ $adBox->getFileUrl() }}" alt="{{ $adBox->title }}" class="bg-image">
                </div>

                <div class="box-content">
                    <h3>{{ $adBox->title }}</h3>
                    <p>{!! $adBox->getAnnounce() !!}</p>
                    <a href="{{ $adBox->getUrl() }}" class="btn btn-main">{{ trans('messages.see_more') }}</a>
                </div>

                {{-- <h3>{{ $adBox->title }}</h3> --}}

                {{-- <div class="box-status promo">Promo</div> --}}
            </div>
        @endforeach
    </div>

    <div class="section-footer" data-aos="fade-up" data-aos-delay="200">
        <a href="" class="link-more-big"></a>
    </div>
</section>

{{--    @php--}}{{--        $adBoxButton = \App\Models\AdBoxButton::getTranslation(1, $language->id);--}}{{--    @endphp--}}{{--    @if($adBoxButton && $adBoxButton->url)--}}{{--        <div class="section-actions">--}}{{--            <a href="{{ (!is_null($adBoxButton)) ? ($adBoxButton->external_url) ? $adBoxButton->url : url($adBoxButton->url) :''}}" class="btn" data-aos="fade-up" data-aos-delay="100">{!! $adBoxButton->title !!}</a>--}}{{--        </div>--}}{{--    @endif--}}
