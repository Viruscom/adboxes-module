<div class="row">
    <div class="col-xs-12">
        <h3>@lang('adboxes::admin.adboxes.index'): @lang('adboxes::admin.ad_boxes_type_2')</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                <th class="width-2-percent"></th>
                <th class="width-2-percent">@lang('adboxes::admin.number')</th>
                <th class="width-130">@lang('adboxes::admin.type')</th>
                <th>@lang('adboxes::admin.title')</th>
                <th class="width-220 text-right">@lang('adboxes::admin.actions')</th>
                </thead>
                <tbody>
                <?php $i = 1; ?>
                @forelse ($adBoxesAdminAll[2] as $adBox)
                    <tr class="t-row row-{{$adBox->id}}" data-toggle="popover" data-content='<img class="thumbnail img-responsive" src="{{ $adBox->getFileUrl() }}"/>'>
                        <td class="width-2-percent">
                            <div class="pretty p-default p-square">
                                <input type="checkbox" class="checkbox-row" name="check[]" value="{{$adBox->id}}"/>
                                <div class="state p-primary">
                                    <label></label>
                                </div>
                            </div>
                        </td>
                        <td class="width-2-percent">{{$i}}</td>
                        <td><label class="label btn-light-purple">@lang('adboxes::admin.ad_boxes_type_2')</label></td>
                        <td>{{ $adBox->title }}</td>
                        <td class="pull-right">
                            @include('admin.partials.index.action_buttons', ['mainRoute' => Request::segment(2), 'models' => $adBoxesAdminAll[2], 'model' => $adBox, 'showInPublicModal' => false])
                        </td>
                    </tr>
                    <tr class="t-row-details row-{{$adBox->id}}-details hidden">
                        <td colspan="2"></td>
                        <td colspan="2">
                            <table class="table-details">
                                <tbody>
                                <tr>
                                    <td>
                                            <?php $l = 0; ?>
                                        @foreach($languages as $language)
                                                <?php
                                                $adTrans = $adBox->translate($language->code);
                                                if (is_null($adTrans)) {
                                                    continue;
                                                }
                                                ?>
                                            @if($l <= 3)
                                                <p>
                                                    <span>{{ __('admin.common.link') }} ({{$language->code}}): </span>
                                                    <span>
													<a href="{{ is_null($adTrans->url) ? "":(($adTrans->external_url) ? $adTrans->url : url($adTrans->url)) }}" class="text-purple" target="_blank">{{ is_null($adTrans->url) ? "":(($adTrans->external_url) ? $adTrans->url : url($adTrans->url)) }}</a>
												</span>
                                                </p>
                                            @endif
                                                <?php $l++; ?>
                                        @endforeach
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="width-220">
                            <img class="thumbnail img-responsive" src="{{ $adBox->getFileUrl() }}"/>
                        </td>
                    </tr>
                        <?php $i++; ?>
                @empty
                    <tr>
                        <td colspan="5" class="no-table-rows">@lang('adboxes::admin.no_fourth_type_ad_boxes')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <a href="{{ route('admin.ad-boxes.edit-button', ['adBoxType' => 2]) }}" class="btn btn-light-green">{!! (isset($adBoxButtons[1]) && $adBoxButtons[1]->title !='') ? $adBoxButtons[1]->title : trans('adboxes::admin.default_button_name_2')  !!}</a>
    </div>
</div>
