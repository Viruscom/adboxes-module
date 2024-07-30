@extends('layouts.admin.app')
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
            //Load external links AdBoxes
            $('input[name="external_url_{{$language->code}}"]').on('click', function () {
                adBoxExternalLinkToggle($(this), '{{$language->code}}');
            });
            @endforeach
            function adBoxExternalLinkToggle(el, languageCode) {
                var select = $('.select2-' + languageCode + '');
                if (el.val() == "on" && $('.select2-' + languageCode).hasClass('hidden')) {
                    select.removeClass('hidden').removeAttr('disabled');
                    select.parents('.form-group').removeClass('hidden');
                    $('input[name="url_' + languageCode + '"]').parent().addClass('hidden');
                } else {
                    select.addClass('hidden').attr('disabled', 'disabled');
                    select.parents('.form-group').addClass('hidden');
                    $('input[name="url_' + languageCode + '"]').parent().removeClass('hidden');
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
    <form class="my-form" action="{{ route('admin.ad-boxes.store') }}" method="POST" data-form-type="store" enctype="multipart/form-data">
        <div class="col-xs-12 p-0">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="position" value="{{old('position')}}">
            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submitaddnew" value="submitaddnew" class="btn btn-lg green saveplusicon margin-bottom-10"></button>
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    <a href="{{ route('admin.ad-boxes.index') }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label page-label m-r-5 p-t-7"><span class="text-purple">* </span>{{ __('adboxes::admin.adboxes.show_in_adboxes') }}:</label>
                    <div class="btn-group" data-toggle="buttons">
                        <label class="btn btn-light-green active  abox-type">
                            <input type="radio" name="type" class="adbox-type" value="1" required="" aria-required="true" {{ (old('type')) ?? 'checked'}}> @lang('adboxes::admin.ad_boxes_type_1')
                        </label>
                        <label class="btn btn-light-purple  abox-type">
                            <input type="radio" name="type" class="adbox-type" value="2" {{ (old('type')) ?? 'checked'}}> @lang('adboxes::admin.ad_boxes_type_2')
                        </label>
                        <label class="btn btn-light-yellow abox-type">
                            <input type="radio" name="type" class="adbox-type" value="3" aria-required="true" {{ (old('type')) ?? 'checked'}}> @lang('adboxes::admin.ad_boxes_type_3')
                        </label>
                        <label class="btn btn-light-blue abox-type">
                            <input type="radio" name="type" class="adbox-type" value="4" aria-required="true" {{ (old('type')) ?? 'checked'}}> @lang('adboxes::admin.ad_boxes_type_4')
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-xs-12">

                <ul class="nav nav-tabs">
                    @foreach($languages as $language)
                        <li @if($language->code === config('default.app.language.code')) class="active" @endif><a data-toggle="tab" href="#{{$language->code}}">{{$language->code}} <span class="err-span-{{$language->code}} hidden text-purple"><i class="fas fa-exclamation"></i></span></a></li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($languages as $language)
                            <?php
                            $langShortDescr  = 'short_description_' . $language->code;
                            $langVisible     = 'visible_' . $language->code;
                            $langLink        = 'url_' . $language->code;
                            $langExternalUrl = 'external_url_' . $language->code;
                            ?>
                        <div id="{{$language->code}}" class="tab-pane fade in @if($language->code === config('default.app.language.code')) active @endif">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('admin.partials.on_create.form_fields.input_text', ['fieldName' => 'title_' . $language->code, 'label' => trans('admin.title'), 'required' => true])
                                </div>

                                <div class="col-md-6">
                                    @include('admin.partials.on_create.form_fields.input_text', ['fieldName' => 'label_' . $language->code, 'label' => trans('admin.label'), 'required' => false])
                                </div>
                            </div>

                            <div class="form-group @if($errors->has($langShortDescr)) has-error @endif">
                                <label class="control-label p-b-10">{{ __('admin.common.short_description') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <textarea name="{{$langShortDescr}}" class="form-control" rows="3">{{ old($langShortDescr) }}</textarea>
                                @if($errors->has($langShortDescr))
                                    <span class="help-block">{{ trans($errors->first($langShortDescr)) }}</span>
                                @endif
                            </div>
                            <div class="form-group @if($errors->has($langLink)) has-error @endif hidden">
                                <label class="control-label p-b-10"><span class="text-purple">* </span> {{ __('admin.common.link') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <input class="form-control" type="text" name="{{$langLink}}" value="{{ old($langLink) }}">
                                @if($errors->has($langLink))
                                    <span class="help-block">{{ trans($errors->first($langLink)) }}</span>
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="select_internal_link" class="control-label">{{ __('admin.common.intenal_link') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <div>
                                    <select id="select_internal_link" name="{{$langLink}}" class="form-control select2 select2-{{$language->code}}" style="width: 100%;">
                                        @include('admin.partials.on_create.select_tag_internal_links', ['language' => $language->code, 'internalLinks' => $internalLinks])
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6 col-xs-12">
                                    <div class="form-group m-t-10">
                                        <label class="control-label col-lg-6 text-right p-t-7 p-l-0">{{ __('admin.common.external_link') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
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
                                        <label class="control-label col-lg-6 text-right p-t-7 p-l-0">{{ __('admin.common.show_in_lang_version') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                        <div class="col-lg-6 p-l-0">
                                            <label class="switch pull-left">
                                                <input type="checkbox" name="{{$langVisible}}" class="success" data-size="small" checked {{(old($langVisible) ? 'checked' : 'active')}}>
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
                        <div class="col-md-12">
                            <div class="form-group @if($errors->has('type_color_class')) has-error @endif">
                                <label class="control-label p-b-10">{{ __('adboxes::admin.adboxes.label_color') }}:</label>
                                <div class="m-t-10">
                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-1" checked="checked" {{old('type_color_class') ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-1">{{ __('adboxes::admin.adboxes.label_color_1') }}</label>
                                        </div>
                                    </div>

                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-2" {{old('type_color_class') ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-2">{{ __('adboxes::admin.adboxes.label_color_2') }}</label>
                                        </div>
                                    </div>

                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-3" {{old('type_color_class') ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-3">{{ __('adboxes::admin.adboxes.label_color_3') }}</label>
                                        </div>
                                    </div>

                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-4" {{old('type_color_class') ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-4">{{ __('adboxes::admin.adboxes.label_color_4') }}</label>
                                        </div>
                                    </div>

                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-5" {{old('type_color_class') ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-5">{{ __('adboxes::admin.adboxes.label_color_5') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <div class="form-group col-lg-m-r-0 @if($errors->has('price')) has-error @endif">
                                <label class="control-label m-b-10">{{ __('admin.common.price') }}:</label>
                                <input class="form-control" type="number" step="0.01" name="price" value="{{ old('price') }}">
                                @if($errors->has('price'))
                                    <span class="help-block">{{ trans($errors->first('price')) }}</span>
                                @endif
                                <div class="col-md-12 m-t-10 p-l-0">
                                    <div class="pretty p-default p-square">
                                        <input type="checkbox" name="from_price" class="tooltips" data-toggle="tooltip" data-placement="right" data-original-title="{{ __('admin.common.activate_deactivate_from_price') }}" data-trigger="hover"/>
                                        <div class="state p-primary">
                                            <label>{{ __('admin.common.activate_from_price') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-lg-m-r-0 @if($errors->has('new_price')) has-error @endif">
                                <label class="control-label m-b-10">{{ __('admin.common.new_price') }}:</label>
                                <input class="form-control" type="number" step="0.01" name="new_price" value="{{ old('new_price') }}">
                                @if($errors->has('new_price'))
                                    <span class="help-block">{{ trans($errors->first('new_price')) }}</span>
                                @endif
                                <div class="col-md-12 m-t-10 p-l-0">
                                    <div class="pretty p-default p-square">
                                        <input type="checkbox" name="from_new_price" class="tooltips" data-toggle="tooltip" data-placement="right" data-original-title="{{ __('admin.common.activate_deactivate_from_price') }}" data-trigger="hover"/>
                                        <div class="state p-primary">
                                            <label>{{ __('admin.common.activate_from_price') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xs-12">
                            <div class="form-group @if($errors->has('date_from_to')) has-error @endif">
                                <label class="control-label m-b-10">{{ __('adboxes::admin.adboxes.for_period') }}:</label>
                                <div class="input-group m-b-10">
                                    <div class="input-group-addon">{{ __('adboxes::admin.adboxes.from_date') }}</div>
                                    <input type="text" class="form-control" value="" name="from_date" id="dpd1" autocomplete="off">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">{{ __('adboxes::admin.adboxes.to_date') }}</div>
                                    <input type="text" class="form-control" value="" name="to_date" id="dpd2" autocomplete="off">
                                </div>
                                @if($errors->has('date_from_to'))
                                    <span class="help-block">{{ trans($errors->first('date_from_to')) }}</span>
                                @endif
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12 col-xs-12">
                                <hr>
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-md-3">{{ __('admin.image') }}:</label>
                                    <div class="col-md-9">
                                        <input type="file" name="image" class="filestyle" data-buttonText="@lang('admin.browse_file')" data-iconName="fas fa-upload" data-buttonName="btn green" data-badge="true">
                                        <p class="help-block file-rules-0">{!! $fileRules[1] !!}</p>
                                        <p class="help-block file-rules-1 hidden">{!! $fileRules[1] !!}</p>
                                        <p class="help-block file-rules-2 hidden">{!! $fileRules[2] !!}</p>
                                        <p class="help-block file-rules-3 hidden">{!! $fileRules[3] !!}</p>
                                        <p class="help-block file-rules-4 hidden">{!! $fileRules[4] !!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <hr>
                            </div>
                            <div class="col-lg-6 col-xs-12">
                                <div class="form-group">
                                    <label class="control-label col-lg-6">{{ __('admin.active_visible_on_site') }}:</label>
                                    <div class="col-lg-6">
                                        <label class="switch pull-left">
                                            <input type="checkbox" name="active" class="success" data-size="small" checked {{(old('active') ? 'checked' : 'active')}}>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-xs-12">
                                <div>
                                    <label class="control-label">{{ __('admin.position_in_site') }}:</label>
                                    <p class="position-label"></p>
                                    <a href="#" class="btn btn-default" data-toggle="modal" data-target="#myModal">{{ __('admin.please_choose_position') }}</a>
                                    <p class="help-block">{{ __('admin.position_description') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-xs-12">
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-3 col-md-9">
                            <button type="submit" name="submitaddnew" value="submitaddnew" class="btn green saveplusbtn margin-bottom-10"> {{ __('admin.common.save_and_add_new') }}</button>
                            <button type="submit" name="submit" value="submit" class="btn save-btn margin-bottom-10"><i class="fas fa-save"></i> {{ __('admin.common.save') }}</button>
                            <a href="{{ route('admin.ad-boxes.index') }}" role="button" class="btn back-btn margin-bottom-10"><i class="fa fa-reply"></i> {{ __('admin.common.back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal -->
            <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">

                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close text-purple" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">{{ __('admin.choose_position') }}</h4>
                        </div>
                        <div class="modal-body">
                            <table class="table table-striped table-hover table-positions">
                                <tbody>
                                {{--                                @if(count($adBoxesAdminAll))--}}
                                {{--                                    @foreach($adBoxesAdminAll as $adBox)--}}
                                {{--                                        <tr class="pickPositionTr" data-position="{{$adBox->position}}">--}}
                                {{--                                            <td>{{$adBox->position}}</td>--}}
                                {{--                                            <td>{{$adBox->translations->firstWhere('language_id',$defaultLanguage->id)->title}}</td>--}}
                                {{--                                        </tr>--}}
                                {{--                                    @endforeach--}}
                                {{--                                    <tr class="pickPositionTr" data-position="{{$adBoxesAdminAll->last()->position+1}}">--}}
                                {{--                                        <td>{{$adBoxesAdminAll->last()->position+1}}</td>--}}
                                {{--                                        <td>--{{trans('admin.common.last_position')}}--</td>--}}
                                {{--                                    </tr>--}}
                                {{--                                @else--}}
                                {{--                                    <tr class="pickPositionTr" data-position="1">--}}
                                {{--                                        <td>1</td>--}}
                                {{--                                        <td>--{{trans('admin.common.last_position')}}--</td>--}}
                                {{--                                    </tr>--}}
                                {{--                                @endif--}}
                                </tbody>
                            </table>
                            <div class="form-actions">
                                <div class="row">
                                    <div class="col-md-offset-3 col-md-9">
                                        <a href="#" class="btn save-btn margin-bottom-10 accept-position-change" data-dismiss="modal"><i class="fas fa-save"></i> {{ __('admin.common.apply') }}</a>
                                        <a role="button" class="btn back-btn margin-bottom-10 cancel-position-change" current-position="{{ old('position') }}" data-dismiss="modal"><i class="fa fa-reply"></i> {{ __('admin.common.back') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
