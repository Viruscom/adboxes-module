@extends('layouts.admin.app')
@section('styles')
    <link href="{{ asset('admin/assets/css/select2.min.css') }}" rel="stylesheet"/>
@endsection
@section('scripts')
    <script src="{{ asset('admin/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/bootstrap-confirmation.js') }}"></script>
    <script>
        $('[data-toggle=confirmation]').confirmation({
            rootSelector: '[data-toggle=confirmation]',
            container: 'body',
        });
        $(".select2").select2({language: "bg"});

        $(document).ready(function () {
            $('[data-toggle="popover"]').popover({
                placement: 'auto',
                trigger: 'hover',
                html: true
            });
        });
    </script>
@endsection
@section('content')
    @include('adboxes::admin.breadcrumbs')
    @include('admin.notify')
    @include('admin.partials.index.top_search_with_mass_buttons', ['mainRoute' => Request::segment(2)])
    @include('adboxes::admin.partials.ad_boxes_types.waiting_action')
    @include('adboxes::admin.partials.ad_boxes_types.first')
    @include('adboxes::admin.partials.ad_boxes_types.second')
    @include('adboxes::admin.partials.ad_boxes_types.third')
    @include('adboxes::admin.partials.ad_boxes_types.fourth')
@endsection
