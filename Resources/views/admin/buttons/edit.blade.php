@extends('layouts.app')
@section('styles')
    <link href="{{ asset('admin/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('admin/plugins/foundation-datepicker/datepicker.css') }}" rel="stylesheet"/>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/foundation-datepicker/datepicker.js') }}"></script>
    <script>
        $(".select2").select2({language: "bg"});

        $(document).ready(function () {
            @foreach($languages as $language)
            $('input[name="external_url_{{$language->code}}"]').on('click', function () {
                adBoxExternalLinkToggle($(this), '{{$language->code}}');
            });
            @endforeach
            function adBoxExternalLinkToggle(el, languageCode) {
                if (el.val() == "on" && $('.select2-' + languageCode).parents('.form-group').hasClass('hidden')) {
                    $('.select2-' + languageCode + '').removeClass('hidden').removeAttr('disabled');
                    $('.select2-' + languageCode + '').parents('.form-group').removeClass('hidden');
                    $('input[name="url_' + languageCode + '"]').parent().addClass('hidden');
                } else {
                    $('.select2-' + languageCode + '').addClass('hidden').attr('disabled', 'disabled');
                    $('.select2-' + languageCode + '').parents('.form-group').addClass('hidden');
                    $('input[name="url_' + languageCode + '"]').parent().removeClass('hidden');
                    $('input[name="url_' + languageCode + '"]').val('');
                }
            }
        });

        var nowTemp  = new Date();
        var now      = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
        var checkin  = $('#dpd1').fdatepicker({
            onRender: function (date) {
                return '';
            },
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            if (ev.date.valueOf() > checkout.date.valueOf()) {
                var newDate = new Date(ev.date)
                newDate.setDate(newDate.getDate() + 1);
                checkout.update(newDate);
            }
            checkin.hide();
            $('#dpd2')[0].focus();
        }).data('datepicker');
        var checkout = $('#dpd2').fdatepicker({
            onRender: function (date) {
                return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
            },
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            checkout.hide();
        }).data('datepicker');
        $('#oneDayEventPicker').fdatepicker({
            onRender: function (date) {
                return '';
            },
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
@section('content')
    <form class="my-form" action="{{ url('/admin/adboxes/'.$adBoxButton->adbox_type.'/updateButton') }}" method="POST" data-form-type="store" enctype="multipart/form-data">
        <div class="col-xs-12 p-0">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    <a href="{{ url('/admin/adboxes') }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <ul class="nav nav-tabs">
                    @foreach($languages as $language)
                        <li @if($language->code == env('DEF_LANG_CODE')) class="active" @endif}}><a data-toggle="tab" href="#{{$language->code}}">{{$language->code}} <span class="err-span-{{$language->code}} hidden text-purple"><i class="fas fa-exclamation"></i></span></a></li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($languages as $language)
                            <?php
                            $langTitle        = 'title_' . $language->code;
                            $langVisible      = 'visible_' . $language->code;
                            $langLink         = 'url_' . $language->code;
                            $langExternalUrl  = 'external_url_' . $language->code;
                            $adboxTranslation = $adBoxButton->where('adbox_type', $adBoxButton->adbox_type)->where('language_id', $language->id)->first();
                            ?>
                        <div id="{{$language->code}}" class="tab-pane fade in @if($language->code == env('DEF_LANG_CODE')) active @endif">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group @if($errors->has($langTitle)) has-error @endif">
                                        <label class="control-label p-b-10"><span class="text-purple">* </span>Заглавие (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                        <input class="form-control" type="text" name="{{$langTitle}}" value="{{ old($langTitle) ?: (!is_null($adboxTranslation) ? $adboxTranslation->title : '') }}" required>
                                        @if($errors->has($langTitle))
                                            <span class="help-block">{{ trans($errors->first($langTitle)) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group @if($errors->has($langLink)) has-error @endif hidden">
                                <label class="control-label p-b-10"><span class="text-purple">* </span> Линк (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <input class="form-control" type="text" name="{{$langLink}}" value="{{ old($langLink) }}">
                                @if($errors->has($langLink))
                                    <span class="help-block">{{ trans($errors->first($langLink)) }}</span>
                                @endif
                            </div>

                            <div class="form-group {{ (!is_null($adboxTranslation) && $adboxTranslation->external_url != 0) ? 'hidden': '' }}">
                                <label class="control-label">Вътрешен линк (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <div>
                                    <select name="{{$langLink}}" class="form-control select2 select2-{{$language->code}}" style="width: 100%;" {{ (!is_null($adboxTranslation) && $adboxTranslation->external_url != 0) ? 'disabled': '' }}>
                                        @foreach($navigations as $navigation)
                                            @php
                                                $navTranslation = $navigation->getTranslation($language->id)->first();
                                                if(is_null($navTranslation)){
                                                    continue;
                                                }
                                                $pages = $navigation->content_pages()->orderBy('position')->get();
                                                $url = ($navigation->isHomeModule()) ? $language->code.'/': $language->code.'/page/'.$navTranslation->slug;
                                            @endphp
                                            <option value="{{$url}}" {{ (!is_null($adboxTranslation) && $adboxTranslation->url == $url) ? 'selected': ''}}>{{$navTranslation->title}}</option>
                                            @if($navigation->isBrandModule())
                                                @foreach($brands as $brand)
                                                    @php
                                                        $brandTranslation = $brand->translations()->where('language_id', $language->id)->first();
                                                        if(is_null($brandTranslation)){
                                                            continue;
                                                        }
                                                    @endphp
                                                    <option value="{{$url.'/'.$brandTranslation->slug}}" {{ (!is_null($adboxTranslation) && $adboxTranslation->url == $url.'/'.$brandTranslation->slug) ? 'selected': ''}}><span>&#8226;</span> {{$brandTranslation->title}}</option>
                                                @endforeach
                                            @else
                                                @foreach($pages as $page)
                                                    @php
                                                        $pageTranslation = $page->translations()->where('language_id', $language->id)->first();
                                                        if(is_null($pageTranslation)){
                                                            continue;
                                                        }
                                                    @endphp
                                                    <option value="{{$url.'/'.$pageTranslation->slug}}" {{ (!is_null($adboxTranslation) && $adboxTranslation->url == $url.'/'.$pageTranslation->slug) ? 'selected': ''}}><span>&#8226;</span> {{$pageTranslation->title}}</option>
                                                @endforeach
                                            @endif

                                            @if($navigation->isHotelModule())
                                                <optgroup label="@lang('administration_messages.module_11')">
                                                    @php
                                                        $hotels = $navigation->hotels()->orderBy('position', 'asc')->get();
                                                    @endphp
                                                    @foreach($hotels as $hotel)
                                                        @php
                                                            $hotelTranslation = $hotel->translations->where('language_id', $language->id)->first();
                                                        @endphp
                                                        <option value="{{$language->code.'/page/'.$navTranslation->slug.'/'.$hotelTranslation->slug}}" {{ (!is_null($adboxTranslation) && $adboxTranslation->url == $url.'/'.$hotelTranslation->slug) ? 'selected': ''}}> - - {{
                                                        $hotelTranslation->title}}</option>
                                                        @php $rooms = $hotel->rooms()->orderBy('position', 'asc')->get(); @endphp
                                                        @foreach($rooms as $room)
                                                            @php $roomTranslation = $room->translations->where('language_id', 1)->first(); @endphp
                                                            <option value="{{$language->code.'/page/'.$navTranslation->slug.'/'.$hotelTranslation->slug.'/'.$roomTranslation->slug}}" {{ (!is_null($adboxTranslation) && $adboxTranslation->url == $url.'/'.$hotelTranslation->slug.'/'.$roomTranslation->slug) ? 'selected': ''}}> - - - {{$roomTranslation->title}}</option>
                                                        @endforeach
                                                    @endforeach
                                                </optgroup>
                                            @endif
                                        @endforeach
                                        <optgroup label="Продуктови категории и продукти">
                                            @foreach($productCategories as $productCategory)
                                                @php
                                                    $productCategoryTranslation = $productCategory->translations()->where('language_id', $language->id)->first();
                                                    $navTranslation = $productCategory->navigation->translations()->where('language_id', $language->id)->first();
                                                    if(is_null($navTranslation)){
                                                        continue;
                                                    }
                                                    $products = $productCategory->products()->orderBy('position')->get();
                                                @endphp
                                                <option value="{{$language->code.'/product_category/'.$navTranslation->slug.'/'.$productCategoryTranslation->slug}}" {{ (!is_null($adboxTranslation) && $adboxTranslation->url == $language->code.'/product_category/'.$productCategoryTranslation->slug) ? 'selected': ''}}>{{$productCategoryTranslation->title}}</option>
                                                @foreach($products as $product)
                                                    @php
                                                        $productTranslation = $product->translations()->where('language_id', $language->id)->first();
                                                        if(is_null($pageTranslation)){
                                                            continue;
                                                        }
                                                    @endphp
                                                    <option value="{{$language->code.'/product_category/'.$navTranslation->slug.'/'.$productCategoryTranslation->slug.'/'.$productTranslation->slug}}" {{ (!is_null($adboxTranslation) && $adboxTranslation->url == $language->code.'/product_category/'.$navTranslation->slug.'/'.$productCategoryTranslation->slug.'/'.$productTranslation->slug) ? 'selected': ''}}><span>&#8226;</span> {{$productTranslation->title}}</option>
                                                @endforeach
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-xs-12">
                                    <div class="form-group m-t-10">
                                        <label class="control-label col-lg-6 text-right p-t-7 p-l-0">Външен линк (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                        <div class="col-lg-6 p-l-0">
                                            <label class="switch pull-left">
                                                <input type="checkbox" name="{{$langExternalUrl}}" class="success" data-size="small" {{(old($langExternalUrl) ? 'checked' : 'active')}}>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-xs-12">
                                    <div class="form-group m-t-10">
                                        <label class="control-label col-lg-6 text-right p-t-7 p-l-0">Покажи в езикова версия (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                        <div class="col-lg-6 p-l-0">
                                            <label class="switch pull-left">
                                                <input type="checkbox" name="{{$langVisible}}" class="success" data-size="small" {{(old($langVisible) ? 'checked' : ((!is_null($adboxTranslation) && $adboxTranslation->visible) ? 'checked': 'active'))}}>
                                                <span class="slider"></span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-sm-12 col-xs-12">
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" name="submit" value="submit" class="btn save-btn margin-bottom-10"><i class="fas fa-save"></i> запиши</button>
                            <a href="{{ url()->previous() }}" role="button" class="btn back-btn margin-bottom-10"><i class="fa fa-reply"></i> назад</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
