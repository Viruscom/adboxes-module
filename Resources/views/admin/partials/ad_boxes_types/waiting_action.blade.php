<div class="row">
    <div class="col-xs-12">
        <h3>@lang('adboxes::admin.adboxes.index'): @lang('adboxes::admin.ad_boxes_type_waiting_action')</h3>
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
                @forelse ($adBoxesAdminAll['waitingAction'] as $adBox)
                    <tr class="t-row row-{{$adBox->id}}">
                        <td class="width-2-percent">
                            <div class="pretty p-default p-square">
                                <input type="checkbox" class="checkbox-row" name="check[]" value="{{$adBox->id}}"/>
                                <div class="state p-primary">
                                    <label></label>
                                </div>
                            </div>
                        </td>
                        <td class="width-2-percent">{{$i}}</td>
                        <td><label class="label label-default">@lang('adboxes::admin.ad_boxes_type_waiting_action')</label></td>
                        <td>
                            {{ $adBox->title }}
                        </td>
                        <td class="pull-right">
                            <a href="{{ route('admin.ad-boxes.edit', ['id' => $adBox->id]) }}" class="btn green" role="button"><i class="fas fa-pencil-alt"></i></a>
                            <a href="{{ route('admin.ad-boxes.delete', ['id' => $adBox->id]) }}" class="btn red btn-delete-confirm tooltips" data-toggle="tooltip" data-placement="auto" title="" data-original-title="{{ __('admin.delete') }}"><i class="fas fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    <tr class="t-row-details row-{{$adBox->id}}-details hidden">
                        <td colspan="2"></td>
                        <td colspan="2"></td>
                        <td class="width-220"></td>
                    </tr>
                        <?php $i++; ?>
                @empty
                    <tr>
                        <td colspan="5" class="no-table-rows">@lang('adboxes::admin.no_waiting_action_ad_boxes')</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
