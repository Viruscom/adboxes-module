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
                let select   = $('.select2-' + languageCode + '');
                let inputUrl = $('input[name="url_' + languageCode + '"]');
                if (el.val() == "on" && $('.select2-' + languageCode).parents('.form-group').hasClass('hidden')) {
                    select.removeClass('hidden').removeAttr('disabled');
                    select.parents('.form-group').removeClass('hidden');
                    inputUrl.parent().addClass('hidden');
                } else {
                    select.addClass('hidden').attr('disabled', 'disabled');
                    select.parents('.form-group').addClass('hidden');
                    inputUrl.parent().removeClass('hidden');
                    inputUrl.val('');
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
    <form class="my-form" action="{{ route('admin.ad-boxes.update', ['id'=>$adBox->id]) }}" method="POST" data-form-type="store" enctype="multipart/form-data">
        <div class="col-xs-12 p-0">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="position" value="{{(old('position')) ?: $adBox->position}}">
            <input type="hidden" name="type" value="{{(old('type')) ?: $adBox->type}}">

            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    @if (!$adBox->isWaitingAction())
                        <a href="{{ route('admin.ad-boxes.return-to-waiting', ['id'=>$adBox->id]) }}" role="button" class="btn btn-lg btn yellow margin-bottom-10 tooltips" style="padding: 8px 10px;" data-toggle="tooltip" data-placement="left" data-original-title="{{ __('adboxes::admin.return_to_waiting') }}"><img src="{{ asset('admin/assets/images/back_to_wait.svg') }}" width="23px"></a>
                    @endif
                    <a href="{{ route('admin.ad-boxes.index') }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
        </div>
        @if($adBox->isWaitingAction())
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label page-label m-r-5 p-t-7"><span class="text-purple">* </span>{{ __('adboxes::admin.adboxes.show_in_adboxes') }}:</label>
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
                        <li @if($language->code == config('default.app.admin_language.code')) class="active" @endif><a data-toggle="tab" href="#{{$language->code}}">{{$language->code}} <span class="err-span-{{$language->code}} hidden text-purple"><i class="fas fa-exclamation"></i></span></a></li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($languages as $language)
                            <?php
                            $adTrans        = $adBox->translate($language->code);
                            $langShortDescr = 'short_description_' . $language->code;
                            ?>

                        <div id="{{$language->code}}" class="tab-pane fade in @if($language->code == config('default.app.language.code')) active @endif">
                            <div class="row">
                                <div class="col-md-6">
                                    @include('admin.partials.on_edit.form_fields.input_text', ['model'=> $adTrans, 'fieldName' => 'title_' . $language->code, 'label' => trans('admin.title'), 'required' => true])
                                </div>

                                <div class="col-md-6">
                                    @include('admin.partials.on_edit.form_fields.input_text', ['model'=> $adTrans, 'fieldName' => 'label_' . $language->code, 'label' => trans('admin.label'), 'required' => false])
                                </div>
                            </div>

                            <div class="form-group @if($errors->has($langShortDescr)) has-error @endif">
                                <label class="control-label p-b-10">{{ __('admin.description') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <textarea name="{{$langShortDescr}}" class="form-control" rows="3">{{ old($langShortDescr) ?: (!is_null($adTrans) ? $adTrans->short_description: '') }}</textarea>
                                @if($errors->has($langShortDescr))
                                    <span class="help-block">{{ trans($errors->first($langShortDescr)) }}</span>
                                @endif
                            </div>

                            @include('admin.partials.on_edit.form_fields.link_input', ['model'=> $adBox, 'fieldName' => 'url_' . $language->code, 'label' => trans('admin.common.link'), 'required' => true])
                            @include('admin.partials.on_edit.internal_link_select', ['fieldName' => 'url_' . $language->code, 'model' => $adTrans])

                            <div class="row">
                                @include('admin.partials.on_edit.external_link_checkbox', ['fieldName' => 'external_url_' . $language->code, 'model' => $adBox])
                                @include('admin.partials.on_edit.show_in_language_visibility_checkbox', ['fieldName' => 'visible_' . $language->code, 'model' => $adBox])
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
                                <div class="m-b-10">
                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-1" {{( (old('type_color_class') == $adBox->type_color_class || $adBox->type_color_class == 'ad-box-type-color-1' )) ? 'checked': '' }}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-1">{{ __('adboxes::admin.adboxes.label_color_1') }}</label>
                                        </div>
                                    </div>

                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-2" {{( (old('type_color_class') == $adBox->type_color_class || $adBox->type_color_class == 'ad-box-type-color-2' )) ? 'checked': '' }}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-2">{{ __('adboxes::admin.adboxes.label_color_2') }}</label>
                                        </div>
                                    </div>

                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-3" {{( (old('type_color_class') == $adBox->type_color_class || $adBox->type_color_class == 'ad-box-type-color-3' )) ? 'checked': '' }}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-3">{{ __('adboxes::admin.adboxes.label_color_3') }}</label>
                                        </div>
                                    </div>

                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-4" {{( (old('type_color_class') == $adBox->type_color_class || $adBox->type_color_class == 'ad-box-type-color-4' )) ? 'checked': '' }}>
                                        <div class="state p-primary-o">
                                            <label class="ad-box-type-color-4">{{ __('adboxes::admin.adboxes.label_color_4') }}</label>
                                        </div>
                                    </div>

                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="type_color_class" value="ad-box-type-color-5" {{( (old('type_color_class') == $adBox->type_color_class || $adBox->type_color_class == 'ad-box-type-color-5' )) ? 'checked': '' }}>
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
                                <input class="form-control" type="number" step="0.01" name="price" value="{{ (old('price')) ?: $adBox->price }}">
                                @if($errors->has('price'))
                                    <span class="help-block">{{ trans($errors->first('price')) }}</span>
                                @endif
                                <div class="col-md-12 m-t-10 p-l-0">
                                    <div class="pretty p-default p-square">
                                        <input type="checkbox" name="from_price" class="tooltips" data-toggle="tooltip" data-placement="right" data-original-title="{{ __('admin.common.activate_deactivate_from_price') }}" data-trigger="hover" {{ ($adBox->from_price) ? 'checked' : '' }}/>
                                        <div class="state p-primary">
                                            <label>{{ __('admin.common.activate_from_price') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-m-r-0 @if($errors->has('new_price')) has-error @endif">
                                <label class="control-label m-b-10">{{ __('admin.common.new_price') }}:</label>
                                <input class="form-control" type="number" step="0.01" name="new_price" value="{{ (old('new_price')) ?: $adBox->new_price }}">
                                @if($errors->has('new_price'))
                                    <span class="help-block">{{ trans($errors->first('new_price')) }}</span>
                                @endif
                                <div class="col-md-12 m-t-10 p-l-0">
                                    <div class="pretty p-default p-square">
                                        <input type="checkbox" name="from_new_price" class="tooltips" data-toggle="tooltip" data-placement="right" data-original-title="{{ __('admin.common.activate_deactivate_from_price') }}" data-trigger="hover" {{ ($adBox->from_new_price) ? 'checked' : '' }}/>
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
                                    <input type="text" class="form-control" value="{{ old('from_date') ?: $adBox->from_date }}" name="from_date" id="dpd1" autocomplete="off">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">{{ __('adboxes::admin.adboxes.to_date') }}</div>
                                    <input type="text" class="form-control" value="{{ old('to_date') ?: $adBox->to_date }}" name="to_date" id="dpd2" autocomplete="off">
                                </div>
                                @if($errors->has('date_from_to'))
                                    <span class="help-block">{{ trans($errors->first('date_from_to')) }}</span>
                                @endif
                            </div>
                        </div>

                        @if (!$adBox->isWaitingAction())
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <hr>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">{{ __('admin.image') }}:</label>
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
                                                    <img class="thumbnail img-responsive" src="{{ $adBox->getFileUrl() }}" width="300" alt="{{ __('admin.image') }}"/>
                                                    @if ($adBox->existsFile($adBox->filename))
                                                        <a href="{{ route('admin.ad-boxes.delete-image', ['id'=>$adBox->id]) }}" class="btn red"><i class="fas fa-trash-alt"></i> {{ __('admin.delete_image') }}</a>
                                                    @endif
                                                @else
                                                    <img class="thumbnail img-responsive" src="{{ $adBox->getFileUrl() }}" width="300"/>
                                                @endif
                                                <div class="default-img-path hidden">{{ $adBox->getFileUrl() }}</div>
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
                                        <label class="control-label">{{ __('admin.position_in_site') }}:</label>
                                        <p class="position-label"> № {{ $adBox->position }}</p>
                                        <a href="#" class="btn btn-default" data-toggle="modal" data-target="#myModal">{{ __('admin.please_choose_position') }}</a>
                                        <p class="help-block">{{ __('admin.position_description') }}</p>
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
                                <h4 class="modal-title">{{ __('admin.choose_position') }}</h4>
                            </div>
                            <div class="modal-body">
                                <table class="table table-striped table-hover table-positions">
                                    <tbody>
                                    @if(!is_null($adBoxesAdminAll) && count($adBoxesAdminAll))
                                        @foreach($adBoxesAdminAll[$adBox->type] as $adBox)
                                            <tr class="pickPositionTr" data-position="{{$adBox->position}}">
                                                <td>{{$adBox->position}}</td>
                                                <td>
                                                    {{$adBox->translate('bg')->title}}
                                                    @if($loop->last)
                                                        <p class="modal-last-position">{{ trans('admin.common.last_position') }}</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="pickPositionTr" data-position="1">
                                            <td>1</td>
                                            <td>--{{trans('admin.common.last_position')}}--</td>
                                        </tr>
                                    @endif
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
            @endif
        </div>
    </form>
@endsection
