
<li class="dd-item" data-id="{{ $branch[$keyName] }}" >
    <div class="dd-handle">
        {!! $branchCallback($branch) !!}
        <span class="float-right dd-nodrag">
            @if(isset($actions[$branch['type']]))
                @foreach($actions[$branch['type']] AS $action)
                    {!! $action->render($branch) !!}
                @endforeach
            @endif
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
