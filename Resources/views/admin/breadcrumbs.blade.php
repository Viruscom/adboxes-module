<div class="breadcrumbs">
    <ul>
        <li>
            <a href="{{ route('admin.index') }}"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('admin.ad-boxes.index') }}" class="text-black">@lang('adboxes::admin.adboxes.index')</a>
        </li>
        @if(url()->current() === route('admin.ad-boxes.index'))

        @elseif(url()->current() === route('admin.ad-boxes.create'))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.ad-boxes.create') }}" class="text-purple">@lang('adboxes::admin.adboxes.create')</a>
            </li>
        @elseif(url()->current() === route('admin.ad-boxes.edit', ['id' => Request::segment(3)]))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.ad-boxes.edit', ['id' => Request::segment(3)]) }}" class="text-purple">@lang('adboxes::admin.adboxes.edit')</a>
            </li>
        @elseif(url()->current() === route('admin.ad-boxes.edit-button', ['adBoxType' => Request::segment(3)]))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.ad-boxes.edit-button', ['adBoxType' => Request::segment(3)]) }}" class="text-purple">@lang('adboxes::admin.adboxes.edit_button')</a>
            </li>
        @endif
    </ul>
</div>
