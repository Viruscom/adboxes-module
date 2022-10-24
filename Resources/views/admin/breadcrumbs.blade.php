<div class="breadcrumbs">
    <ul>
        <li>
            <a href="{{ route('admin.index') }}"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('ad-boxes') }}" class="text-black">@lang('adboxes::admin.adboxes.index')</a>
        </li>
        @if(url()->current() === route('ad-boxes.create'))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('ad-boxes.create') }}" class="text-purple">@lang('adboxes::admin.adboxes.create')</a>
            </li>
{{--        @elseif(url()->current() === route('ad-boxes.edit'))--}}
{{--            <li>--}}
{{--                <i class="fa fa-angle-right"></i>--}}
{{--                <a href="{{ route('ad-boxes.edit') }}" class="text-purple">@lang('adboxes::admin.adboxes.edit')</a>--}}
{{--            </li>--}}
        @endif
    </ul>
</div>
