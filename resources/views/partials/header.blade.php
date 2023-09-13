<style>
    .visibility{
        visibility: hidden;
    }
</style>
<nav class="main-header navbar navbar-expand {{ config('admin.theme.navbar') }}">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
{{--        <li class="nav-item d-none d-sm-inline-block">--}}
{{--            <a href="" class="nav-link">Home</a>--}}
{{--        </li>--}}

        {!! Admin::getNavbar()->render('left') !!}
    </ul>

    <!-- SEARCH FORM -->
{{--    <form class="form-inline ml-3">--}}
{{--        <div class="input-group input-group-sm">--}}
{{--            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">--}}
{{--            <div class="input-group-append">--}}
{{--                <button class="btn btn-navbar" type="submit">--}}
{{--                    <i class="fas fa-search"></i>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </form>--}}

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">

        {!! Admin::getNavbar()->render() !!}

        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-user"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <a href="{{ admin_url('auth/logout') }}" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> {{ admin_trans('admin.logout') }}
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ admin_url('auth/setting') }}" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> {{ admin_trans('admin.setting') }}
                </a>
                <div class="dropdown-divider"></div>
                <div class="duty-select" onmouseenter="show(this)" onmouseleave="hide(this)">
                    @foreach (OrgRbac::duty() as $key=>$duty)
                        @if (!$duty['selected'])
                            <a class="dropdown-item" hidden onmouseenter="dutySelectShow(this)" onclick="dutySelectSubmit(this)">
                                <i class="fas fa-exchange-alt mr-2 visibility"></i>
                                <lable data-value="{{$duty['id']}}">{{$duty['name']}}</lable>
                            </a>
                        @else
                            <a class="dropdown-item" onmouseenter="dutySelectShow(this)" onclick="dutySelectSubmit(this)">
                                <i class="fas fa-exchange-alt mr-2"></i>
                                <lable data-value="{{$duty['id']}}">{{$duty['name']}}</lable>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </li>
{{--        <li class="nav-item">--}}
{{--            <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">--}}
{{--                <i class="fas fa-th-large"></i>--}}
{{--            </a>--}}
{{--        </li>--}}
    </ul>
</nav>
<script>
    var dutySelect;
    var dutyHiddenSelect;
    function show(that) {
        dutyHiddenSelect = $(that).find('a:hidden')
        dutySelect = $(that).find('a:visible')
        console.log(dutySelect)
        dutyHiddenSelect.removeAttr("hidden")
    }
    function hide(that) {
        dutyHiddenSelect.attr("hidden",true)
        $(that).find('.fa-exchange-alt').removeClass('visibility').addClass('visibility')
        dutySelect.find('.fa-exchange-alt').removeClass('visibility')
    }
    function dutySelectShow(that) {
        $(that).parents().find('.fa-exchange-alt').removeClass('visibility').addClass('visibility')
        $(that).find('.fa-exchange-alt').removeClass('visibility')
    }

    function dutySelectSubmit(that) {
        let dutyId = $(that).find('lable').data('value')
        $.ajax({
            url: "{{url("/admin/auth/users/dutySelect")}}",
            type: 'post',
            data: {
                "duty_id":dutyId
            },
            success: function (res) {
                location.reload()
            }
        });
    }

</script>
