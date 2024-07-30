@extends('layouts.admin.app')
@section('styles')
    <link href="{{ asset('admin/assets/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/js/select2.min.js') }}"></script>
    <script>
        $(".select2").select2({language: "bg"});

        $(document).ready(function () {
            @foreach($languages as $language)
            $('input[name="external_url_{{$language->code}}"]').on('click', function () {
                adBoxExternalLinkToggle($(this), '{{$language->code}}');
            });
            @endforeach
            function adBoxExternalLinkToggle(el, languageCode) {
                let select2Tag  = $('.select2-' + languageCode);
                let inputUrlTag = $('input[name="url_' + languageCode + '"]');

                if (el.val() == "on" && select2Tag.parents('.form-group').hasClass('hidden')) {
                    select2Tag.removeClass('hidden').removeAttr('disabled');
                    select2Tag.parents('.form-group').removeClass('hidden');
                    inputUrlTag.parent().addClass('hidden');
                } else {
                    select2Tag.addClass('hidden').attr('disabled', 'disabled');
                    select2Tag.parents('.form-group').addClass('hidden');
                    inputUrlTag.parent().removeClass('hidden');
                    inputUrlTag.val('');
                }
            }
        });
    </script>
@endsection
@section('content')
    @include('adboxes::admin.breadcrumbs')
    @include('admin.notify')
    <form class="my-form" action="{{ route('admin.ad-boxes.update-button', ['adBoxType' => $adBoxButton->ad_box_type]) }}" method="POST" data-form-type="store" enctype="multipart/form-data">
        <div class="col-xs-12 p-0">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    <a href="{{ route('admin.ad-boxes.index') }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <ul class="nav nav-tabs">
                    @foreach($languages as $language)
                        <li @if($language->code == config('default.app.language.code')) class="active" @endif><a data-toggle="tab" href="#{{$language->code}}">{{$language->code}} <span class="err-span-{{$language->code}} hidden text-purple"><i class="fas fa-exclamation"></i></span></a></li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($languages as $language)
                            <?php
                            $langTitle        = 'title_' . $language->code;
                            $langVisible      = 'visible_' . $language->code;
                            $langLink         = 'url_' . $language->code;
                            $langExternalUrl  = 'external_url_' . $language->code;
                            $adboxTranslation = $adBoxButton->where('ad_box_type', $adBoxButton->ad_box_type)->where('locale', $language->code)->first();
                            ?>
                        <div id="{{$language->code}}" class="tab-pane fade in @if($language->code == config('default.app.language.code')) active @endif">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group @if($errors->has($langTitle)) has-error @endif">
                                        <label class="control-label p-b-10"><span class="text-purple">* </span>{{ __('admin.title') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                        <input class="form-control" type="text" name="{{$langTitle}}" value="{{ old($langTitle) ?: (!is_null($adboxTranslation) ? $adboxTranslation->title : '') }}" required>
                                        @if($errors->has($langTitle))
                                            <span class="help-block">{{ trans($errors->first($langTitle)) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @include('admin.partials.on_edit.form_fields.link_input', ['model'=> $adBoxButton, 'fieldName' => 'url_' . $language->code, 'label' => trans('admin.common.link'), 'required' => true])
                            @include('admin.partials.on_edit.internal_link_select', ['fieldName' => 'url_' . $language->code, 'model' => $adboxTranslation])

                            <div class="row">
                                <div class="col-lg-6 col-xs-12">
                                    <div class="form-group m-t-10">
                                        <label class="control-label col-lg-6 text-right p-t-7 p-l-0">{{ __('admin.common.external_link') }} (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                        <div class="col-lg-6 p-l-0">
                                            <label class="switch pull-left">
                                                <input type="checkbox" name="{{$langExternalUrl}}" class="success" data-size="small" {{(old($langExternalUrl) || (!is_null($adboxTranslation) && $adboxTranslation->external_url == 1) ? 'checked' : 'active')}}>
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
                            <button type="submit" name="submit" value="submit" class="btn save-btn margin-bottom-10"><i class="fas fa-save"></i> {{ __('admin.common.save') }}</button>
                            <a href="{{ url()->previous() }}" role="button" class="btn back-btn margin-bottom-10"><i class="fa fa-reply"></i> {{ __('admin.common.back') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
