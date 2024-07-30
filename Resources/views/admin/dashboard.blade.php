<div class="row">
    <div class="col-md-12">
        <h4 class="dashboard-caption">@lang('adboxes::admin.adboxes.index')</h4>
    </div>
    @if(!is_null($adBoxesAdminAll))
        <div class="col-md-3 col-xs-12">
            <div class="panel panel-default p-0">
                <div class="panel-heading">@lang('adboxes::admin.ad_boxes_type_1')</div>
                <div class="panel-body">
                    <div class="">
                        @if($adBoxesAdminAll[1]->count())
                            <div class="d-flex">
                                <div>Общ брой:</div>
                                <div>{{ $adBoxesAdminAll[1]->count() }}</div>
                            </div>
                            <div class="d-flex">
                                <div>Активни:</div>
                                <div>15</div>
                            </div>
                            <div class="d-flex">
                                <div>Очакващи превод:</div>
                                <div>2</div>
                            </div>
                            <div class="d-flex">
                                <div>Последна промяна:</div>
                                <div>10.03.2022 / Християн Чобанов</div>
                            </div>
                        @else
                            <div class="alert alert-danger">Няма добавени рекламни карета</div>
                        @endif

                        <div><a href="#">Виж история на промените</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-xs-12">
            <div class="panel panel-default p-0">
                <div class="panel-heading">@lang('adboxes::admin.ad_boxes_type_2')</div>
                <div class="panel-body">
                    <div class="">
                        @if($adBoxesAdminAll[2]->count())
                            <div class="d-flex">
                                <div>Общ брой:</div>
                                <div>{{ $adBoxesAdminAll[2]->count() }}</div>
                            </div>
                            <div class="d-flex">
                                <div>Активни:</div>
                                <div>15</div>
                            </div>
                            <div class="d-flex">
                                <div>Очакващи превод:</div>
                                <div>2</div>
                            </div>
                            <div class="d-flex">
                                <div>Последна промяна:</div>
                                <div>10.03.2022 / Християн Чобанов</div>
                            </div>
                        @else
                            <div class="alert alert-danger">Няма добавени рекламни карета</div>
                        @endif

                        <div><a href="#">Виж история на промените</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-xs-12">
            <div class="panel panel-default p-0">
                <div class="panel-heading">@lang('adboxes::admin.ad_boxes_type_3')</div>
                <div class="panel-body">
                    <div class="">
                        @if($adBoxesAdminAll[3]->count())
                            <div class="d-flex">
                                <div>Общ брой:</div>
                                <div>{{ $adBoxesAdminAll[3]->count() }}</div>
                            </div>
                            <div class="d-flex">
                                <div>Активни:</div>
                                <div>15</div>
                            </div>
                            <div class="d-flex">
                                <div>Очакващи превод:</div>
                                <div>2</div>
                            </div>
                            <div class="d-flex">
                                <div>Последна промяна:</div>
                                <div>10.03.2022 / Християн Чобанов</div>
                            </div>
                        @else
                            <div class="alert alert-danger">Няма добавени рекламни карета</div>
                        @endif

                        <div><a href="#">Виж история на промените</a></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-xs-12">
            <div class="panel panel-default p-0">
                <div class="panel-heading">@lang('adboxes::admin.ad_boxes_type_4')</div>
                <div class="panel-body">
                    <div class="">
                        @if($adBoxesAdminAll[4]->count())
                            <div class="d-flex">
                                <div>Общ брой:</div>
                                <div>{{ $adBoxesAdminAll[4]->count() }}</div>
                            </div>
                            <div class="d-flex">
                                <div>Активни:</div>
                                <div>15</div>
                            </div>
                            <div class="d-flex">
                                <div>Очакващи превод:</div>
                                <div>2</div>
                            </div>
                            <div class="d-flex">
                                <div>Последна промяна:</div>
                                <div>10.03.2022 / Християн Чобанов</div>
                            </div>
                        @else
                            <div class="alert alert-danger">Няма добавени рекламни карета</div>
                        @endif

                        <div><a href="#">Виж история на промените</a></div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="col-md-12 alert alert-danger">Няма добавени рекламни карета</div>
    @endif

</div>
