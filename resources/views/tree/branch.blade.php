
<li class="dd-item" data-id="{{ $branch[$keyName] }}" >
    <div class="dd-handle">
        {!! $branchCallback($branch) !!}
        <span class="float-right dd-nodrag">
            @foreach($actions AS $action)
                {!! $action->render($branch) !!}
            @endforeach
        </span>
    </div>
    @if(isset($branch['children']))
    <ol class="dd-list">
        @foreach($branch['children'] as $branch)
            @include($branchView, $branch)
        @endforeach
    </ol>
    @endif
</li>
