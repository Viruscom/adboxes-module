@php use Modules\AdBoxes\Models\AdBox; @endphp@extends('layouts.admin.app')
@section('styles')
    <link href="{{ asset('admin/assets/css/select2.min.css') }}" rel="stylesheet"/>
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
    @include('adboxes::admin.breadcrumbs')
    @include('admin.notify')
    <form class="my-form" action="{{ route('ad-boxes.update', ['id'=>$adBox->id]) }}" method="POST" data-form-type="store" enctype="multipart/form-data">
        <div class="col-xs-12 p-0">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="position" value="{{(old('position')) ?: $adBox->position}}">
            <input type="hidden" name="type" value="{{(old('type')) ?: $adBox->type}}">

            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    @if (!$adBox->isWaitingAction())
                        <a href="{{ route('ad-boxes.return-to-waiting', ['id'=>$adBox->id]) }}" role="button" class="btn btn-lg btn yellow margin-bottom-10 tooltips" style="padding: 8px 10px;" data-toggle="tooltip" data-placement="left" data-original-title="Върни карето в изчакващи"><img src="{{ asset('admin/assets/images/back_to_wait.svg') }}" width="23px"></a>
                    @endif
                    <a href="{{ route('ad-boxes') }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
        </div>
        @if($adBox->isWaitingAction())
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label page-label m-r-5 p-t-7"><span class="text-purple">* </span>Покажи в рекламни карета:</label>
                        <div class="btn-group" data-toggle="buttons">
                            <label class="btn btn-light-green {{ old('type') ?: 'active' }} abox-type">
                                <input type="radio" name="type" class="adbox-type" value="1" required="" aria-required="true" {{ old('type') ?: 'checked' }}> @lang('adboxes::admin.ad_boxes_type_1')
                            </label>
                            <label class="btn btn-light-purple {{ old('type') ?: '' }} abox-type">
                                <input type="radio" name="type" class="adbox-type" value="2" {{ old('type') ?: '' }}> @lang('adboxes::admin.ad_boxes_type_2')
                            </label>
                            <label class="btn btn-light-yellow {{ old('type') ?: '' }} abox-type">
                                <input type="radio" name="type" class="adbox-type" value="3" {{ old('type') ?: '' }}> @lang('adboxes::admin.ad_boxes_type_3')
                            </label>
                            <label class="btn btn-light-blue  {{ old('type') ?: '' }} abox-type">
                                <input type="radio" name="type" class="adbox-type" value="4" {{ old('type') ?: '' }}> @lang('adboxes::admin.ad_boxes_type_4')
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-sm-6 col-xs-12">
                <ul class="nav nav-tabs">
                    @foreach($languages as $language)
                        <li @if($language->code === config('default.app.admin_language.code')) class="active" @endif}}><a data-toggle="tab" href="#{{$language->code}}">{{$language->code}} <span class="err-span-{{$language->code}} hidden text-purple"><i class="fas fa-exclamation"></i></span></a></li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($languages as $language)
                            <?php
                            $adTrans         = $adBox->translate($language->code);
                            $langTitle       = 'title_' . $language->code;
                            $langType        = 'type_' . $language->code;
                            $langShortDescr  = 'short_description_' . $language->code;
                            $langVisible     = 'visible_' . $language->code;
                            $langLink        = 'url_' . $language->code;
                            $langExternalUrl = 'external_url_' . $language->code;
                            if (is_null($adTrans)) {
                                continue;
                            }
                            ?>

                        <div id="{{$language->code}}" class="tab-pane fade in @if($language->code === config('default.app.language.code')) active @endif">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group @if($errors->has($langTitle)) has-error @endif">
                                        <label class="control-label p-b-10"><span class="text-purple">* </span>Заглавие (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                        <input class="form-control" type="text" name="{{$langTitle}}" value="{{ old($langTitle) ?: $adTrans->title }}" required>
                                        @if($errors->has($langTitle))
                                            <span class="help-block">{{ trans($errors->first($langTitle)) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group @if($errors->has($langType)) has-error @endif">
                                        <label class="control-label p-b-10">Етикет (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                        <input class="form-control" type="text" name="{{$langType}}" value="{{ old($langType) ?: $adTrans->type }}" placeholder="Услуга">
                                        @if($errors->has($langType))
                                            <span class="help-block">{{ trans($errors->first($langType)) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-group @if($errors->has($langShortDescr)) has-error @endif">
                                <label class="control-label p-b-10">Кратко описание (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <textarea name="{{$langShortDescr}}" class="form-control" rows="3">{{ old($langShortDescr) ?: $adTrans->short_description }}</textarea>
                                @if($errors->has($langShortDescr))
                                    <span class="help-block">{{ trans($errors->first($langShortDescr)) }}</span>
                                @endif
                            </div>

                            <div class="form-group @if($errors->has($langLink)) has-error @endif hidden">
                                <label class="control-label p-b-10"><span class="text-purple">* </span> Линк (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <input class="form-control" type="text" name="{{$langLink}}" value="{{ old($langLink) }}">
                                @if($errors->has($langLink))
                                    <span class="help-block">{{ trans($errors->first($langLink)) }}</span>
                                @endif
                            </div>

                            <div class="form-group {{ (!is_null($adTrans) && $adTrans->external_url !== 0) ? 'hidden': '' }}">
                                <label class="control-label">Вътрешен линк (<span class="text-uppercase">{{$language->code}}</span>) <span class="text-purple">Моля, изберете</span>:</label>
                                {{--                                <div>--}}
                                {{--                                    <select name="{{$langLink}}" class="form-control select2 select2-{{$language->code}}" style="width: 100%;" {{ (!is_null($adboxTranslation) && $adboxTranslation->external_url != 0) ? 'disabled': '' }}>--}}
                                {{--                                        @foreach($navigations as $navigation)--}}
                                {{--                                            @php--}}
                                {{--                                                $navTranslation = $navigation->getTranslation($language->id)->first();--}}
                                {{--                                                if(is_null($navTranslation)){--}}
                                {{--                                                    continue;--}}
                                {{--                                                }--}}
                                {{--                                                $pages = $navigation->content_pages()->orderBy('position')->get();--}}
                                {{--                                                $url = ($navigation->isHomeModule()) ? $language->code.'/': $language->code.'/page/'.$navTranslation->slug;--}}
                                {{--                                            @endphp--}}
                                {{--                                            <option value="{{$url}}" {{ ($adboxTranslation->url == $url) ? 'selected': ''}}>{{$navTranslation->title}}</option>--}}
                                {{--                                            @if($navigation->isBrandModule())--}}
                                {{--                                                @foreach($brands as $brand)--}}
                                {{--                                                    @php--}}
                                {{--                                                        $brandTranslation = $brand->translations()->where('language_id', $language->id)->first();--}}
                                {{--                                                        if(is_null($brandTranslation)){--}}
                                {{--                                                            continue;--}}
                                {{--                                                        }--}}
                                {{--                                                    @endphp--}}
                                {{--                                                    <option value="{{$url.'/'.$brandTranslation->slug}}" {{ ($adboxTranslation->url == $url.'/'.$brandTranslation->slug) ? 'selected': ''}}><span>&#8226;</span> {{$brandTranslation->title}}</option>--}}
                                {{--                                                @endforeach--}}
                                {{--                                            @else--}}
                                {{--                                                @foreach($pages as $page)--}}
                                {{--                                                    @php--}}
                                {{--                                                        $pageTranslation = $page->translations()->where('language_id', $language->id)->first();--}}
                                {{--                                                        if(is_null($pageTranslation)){--}}
                                {{--                                                            continue;--}}
                                {{--                                                        }--}}
                                {{--                                                    @endphp--}}
                                {{--                                                    <option value="{{$url.'/'.$pageTranslation->slug}}" {{ ($adboxTranslation->url == $url.'/'.$pageTranslation->slug) ? 'selected': ''}}><span>&#8226;</span> {{$pageTranslation->title}}</option>--}}
                                {{--                                                @endforeach--}}
                                {{--                                            @endif--}}

                                {{--                                            @if($navigation->isHotelModule())--}}
                                {{--                                                <optgroup label="@lang('administration_messages.module_11')">--}}
                                {{--                                                    @php--}}
                                {{--                                                        $hotels = $navigation->hotels()->orderBy('position', 'asc')->get();--}}
                                {{--                                                    @endphp--}}
                                {{--                                                    @foreach($hotels as $hotel)--}}
                                {{--                                                        @php--}}
                                {{--                                                            $hotelTranslation = $hotel->translations->where('language_id', $language->id)->first();--}}
                                {{--                                                        @endphp--}}
                                {{--                                                        <option value="{{$language->code.'/page/'.$navTranslation->slug.'/'.$hotelTranslation->slug}}" {{ ($adboxTranslation->url == $url.'/'.$hotelTranslation->slug) ? 'selected': ''}}> - - {{--}}
                                {{--                                                        $hotelTranslation->title}}</option>--}}
                                {{--                                                    @endforeach--}}
                                {{--                                                </optgroup>--}}
                                {{--                                            @endif--}}
                                {{--                                        @endforeach--}}
                                {{--                                        <optgroup label="Продуктови категории и продукти">--}}
                                {{--                                            @foreach($productCategories as $productCategory)--}}
                                {{--                                                @php--}}
                                {{--                                                    $productCategoryTranslation = $productCategory->translations()->where('language_id', $language->id)->first();--}}
                                {{--                                                    $navTranslation = $productCategory->navigation->translations()->where('language_id', $language->id)->first();--}}
                                {{--                                                    if(is_null($navTranslation)){--}}
                                {{--                                                        continue;--}}
                                {{--                                                    }--}}
                                {{--                                                    $products = $productCategory->products()->orderBy('position')->get();--}}
                                {{--                                                @endphp--}}
                                {{--                                                <option value="{{$language->code.'/product_category/'.$navTranslation->slug.'/'.$productCategoryTranslation->slug}}" {{ ($adboxTranslation->url == $language->code.'/product_category/'.$productCategoryTranslation->slug) ? 'selected': ''}}>{{$productCategoryTranslation->title}}</option>--}}
                                {{--                                                @foreach($products as $product)--}}
                                {{--                                                    @php--}}
                                {{--                                                        $productTranslation = $product->translations()->where('language_id', $language->id)->first();--}}
                                {{--                                                        if(is_null($pageTranslation)){--}}
                                {{--                                                            continue;--}}
                                {{--                                                        }--}}
                                {{--                                                    @endphp--}}
                                {{--                                                    <option value="{{$language->code.'/product_category/'.$navTranslation->slug.'/'.$productCategoryTranslation->slug.'/'.$productTranslation->slug}}" {{ ($adboxTranslation->url == $language->code.'/product_category/'.$navTranslation->slug.'/'.$productCategoryTranslation->slug.'/'.$productTranslation->slug) ? 'selected': ''}}><span>&#8226;</span> {{$productTranslation->title}}</option>--}}
                                {{--                                                @endforeach--}}
                                {{--                                            @endforeach--}}
                                {{--                                        </optgroup>--}}
                                {{--                                    </select>--}}
                                {{--                                </div>--}}
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
                                                <input type="checkbox" name="{{$langVisible}}" class="success" data-size="small" {{(old($langVisible) ? 'checked' : ($adTrans->visible ? 'checked': 'active'))}}>
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
            <div class="col-sm-6 col-xs-12">
                <div class="form form-horizontal m-t-10">
                    <div class="form-body">
                        <div class="col-lg-6 col-xs-12">
                            <div class="form-group col-lg-m-r-0 @if($errors->has('price')) has-error @endif">
                                <label class="control-label m-b-10">Цена (0.00):</label>
                                <input class="form-control" type="number" step="0.01" name="price" value="{{ (old('price')) ?: $adBox->price }}">
                                @if($errors->has('price'))
                                    <span class="help-block">{{ trans($errors->first('price')) }}</span>
                                @endif
                                <div class="col-md-12 m-t-10 p-l-0">
                                    <div class="pretty p-default p-square">
                                        <input type="checkbox" name="from_price" class="tooltips" data-toggle="tooltip" data-placement="right" data-original-title="Активирай/Деактивирай от цена" data-trigger="hover" {{ ($adBox->from_price) ? 'checked' : '' }}/>
                                        <div class="state p-primary">
                                            <label>Активирай "От цена"</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-m-r-0 @if($errors->has('new_price')) has-error @endif">
                                <label class="control-label m-b-10">Нова Цена (0.00):</label>
                                <input class="form-control" type="number" step="0.01" name="new_price" value="{{ (old('new_price')) ?: $adBox->new_price }}">
                                @if($errors->has('new_price'))
                                    <span class="help-block">{{ trans($errors->first('new_price')) }}</span>
                                @endif
                                <div class="col-md-12 m-t-10 p-l-0">
                                    <div class="pretty p-default p-square">
                                        <input type="checkbox" name="from_new_price" class="tooltips" data-toggle="tooltip" data-placement="right" data-original-title="Активирай/Деактивирай от цена" data-trigger="hover" {{ ($adBox->from_new_price) ? 'checked' : '' }}/>
                                        <div class="state p-primary">
                                            <label>Активирай "От цена"</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            {{--                            @if($otherSettings->adboxes_show_dates)--}}
                            {{--                                <div class="form-group @if($errors->has('date_from_to')) has-error @endif">--}}
                            {{--                                    <label class="control-label m-b-10">За период (от-до):</label>--}}
                            {{--                                    <div class="input-group m-b-10">--}}
                            {{--                                        <div class="input-group-addon">От дата</div>--}}
                            {{--                                        <input type="text" class="form-control" value="{{ old('from_date') ?: $adBox->from_date }}" name="from_date" id="dpd1" autocomplete="off">--}}
                            {{--                                    </div>--}}
                            {{--                                    <div class="input-group">--}}
                            {{--                                        <div class="input-group-addon">До дата</div>--}}
                            {{--                                        <input type="text" class="form-control" value="{{ old('to_date') ?: $adBox->to_date }}" name="to_date" id="dpd2" autocomplete="off">--}}
                            {{--                                    </div>--}}
                            {{--                                    @if($errors->has('date_from_to'))--}}
                            {{--                                        <span class="help-block">{{ trans($errors->first('date_from_to')) }}</span>--}}
                            {{--                                    @endif--}}
                            {{--                                </div>--}}
                            {{--                            @else--}}
                            {{--                                <div class="@if($errors->has('date')) has-error @endif">--}}
                            {{--                                    <label class="control-label">Обратен брояч:</label>--}}
                            {{--                                    <div class="input-group m-b-10">--}}
                            {{--                                        <div class="input-group-addon">Дата</div>--}}
                            {{--                                        <input type="text" class="form-control" value="{{ old('date') ?: $adBox->date }}" name="date" id="oneDayEventPicker" autocomplete="off">--}}
                            {{--                                    </div>--}}
                            {{--                                    @if($errors->has('date'))--}}
                            {{--                                        <span class="help-block">{{ trans($errors->first('date')) }}</span>--}}
                            {{--                                    @endif--}}
                            {{--                                </div>--}}
                            {{--                            @endif--}}
                        </div>

                        @if (!$adBox->isWaitingAction())
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <hr>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Изображение:</label>
                                        <div class="col-md-9">
                                            <input type="file" name="image" class="filestyle" data-buttonText="@lang('admin.browse_file')" data-iconName="fas fa-upload" data-buttonName="btn green" data-badge="true">
                                            @if($adBox->isWaitingAction())
                                                <p class="help-block file-rules-0">{!! $fileRules[AdBox::$FIRST_TYPE]['file_rules'] !!}</p>
                                                <p class="help-block file-rules-1 hidden">{!! $fileRules[AdBox::$FIRST_TYPE]['file_rules'] !!}</p>
                                                <p class="help-block file-rules-2 hidden">{!! $fileRules[AdBox::$SECOND_TYPE]['file_rules'] !!}</p>
                                                <p class="help-block file-rules-3 hidden">{!! $fileRules[AdBox::$THIRD_TYPE]['file_rules'] !!}</p>
                                                <p class="help-block file-rules-4 hidden">{!! $fileRules[AdBox::$FOURTH_TYPE]['file_rules'] !!}</p>
                                            @else

                                                <p class="help-block file-rules-default">{!! AdBox::getFileRuleMessage($adBox->type) !!}</p>
                                            @endif
                                            <div>
                                                @if ($adBox->existsFile($adBox->filename))
                                                    <div class="overlay-delete-img hidden">
                                                        <a href="{{ route('ad-boxes.delete-img', ['id'=>$adBox->id]) }}" class="del-link del-link-ajax"><i class="fas fa-times"></i>
                                                            <p>Изтрий</p></a>
                                                    </div>
                                                    <img class="thumbnail content-box1 has-img img-responsive" src="{{ $adBox->getFileUrl() }}" width="300"/>
                                                @else
                                                    <img class="thumbnail img-responsive" src="{{ $adBox->getFileUrl() }}" width="300"/>
                                                @endif
                                                <div class="default-img-path hidden">{{ $adBox->getFileUrl() }}</div>

                                                <div class="alert alert-success removed-img-ajax hidden" style="width: 300px;">Успешно изтриване!</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-lg-6 col-xs-12">
                                @include('admin.partials.on_edit.active_checkbox', ['model' => $adBox])
                            </div>

                            @if (!$adBox->isWaitingAction())
                                <div class="col-lg-6 col-xs-12">
                                    <div>
                                        <label class="control-label">Позиция в сайта:</label>
                                        <p class="position-label"> № {{ $adBox->position }}</p>
                                        <a href="#" class="btn btn-default" data-toggle="modal" data-target="#myModal">Моля, изберете позиция</a>
                                        <p class="help-block">(ако не изберете позиция, записът се добавя като последен)</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-xs-12">
                @include('admin.partials.on_edit.form_actions_bottom')
            </div>

            @if(!$adBox->isWaitingAction())
                <!-- Modal -->
                <div id="myModal" class="modal fade" role="dialog">
                    <div class="modal-dialog">
                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close text-purple" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Изберете позиция</h4>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped table-hover table-positions">
                                    <tbody>
                                    @if(!is_null($adBoxesAdminAll) && count($adBoxesAdminAll))
                                        @foreach($adBoxesAdminAll[$adBox->type] as $adBox)
                                            <tr class="pickPositionTr" data-position="{{$adBox->position}}">
                                                <td>{{$adBox->position}}</td>
                                                <td>{{$adBox->translate('bg')->title}}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="pickPositionTr" data-position="{{$adBoxesAdminAll[$adBox->type]->last()->position+1}}">
                                            <td>{{$adBoxesAdminAll[$adBox->type]->last()->position+1}}</td>
                                            <td>--{{trans('administration_messages.last_position')}}--</td>
                                        </tr>
                                    @else
                                        <tr class="pickPositionTr" data-position="1">
                                            <td>1</td>
                                            <td>--{{trans('administration_messages.last_position')}}--</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-offset-3 col-md-9">
                                            <a href="#" class="btn save-btn margin-bottom-10 accept-position-change" data-dismiss="modal"><i class="fas fa-save"></i> потвърди</a>
                                            <a role="button" class="btn back-btn margin-bottom-10 cancel-position-change" current-position="{{ old('position') }}" data-dismiss="modal"><i class="fa fa-reply"></i> назад</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </form>
@endsection
