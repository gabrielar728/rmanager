<header class="header dark-bg">
    <div class="toggle-nav">
        <div class="icon-reorder tooltips" data-original-title="Toggle Navigation" data-placement="bottom"><i class="icon_menu"></i></div>
    </div>

    <a href="{{ route('home') }}" class="logo">r<span class="lite">MANAGER</span></a>

    <div class="top-nav notification-row">
        <ul class="nav pull-right top-menu">

            <!-- user login dropdown start-->
            @guest
                <li><a href="{{ route('login') }}">Login</a></li>
                <li><a href="{{ route('register') }}">Register</a></li>
                @else
                    @if(Auth::guard('web')->check())
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                <span class="username">{{ Auth::user()->last_name }} {{ Auth::user()->first_name }}</span>
                                <b class="caret"></b>
                            </a>

                            <ul class="dropdown-menu extended logout">
                                <div class="log-arrow-up"></div>

                                <li>
                                    <a href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();"><i class="icon_key_alt"></i> Delogare</a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                @endguest

            <!-- user login dropdown end -->
        </ul>
        <!-- notificatoin dropdown end-->
    </div>
</header>

<style>
    .dropdown-menu.extended.logout > li:last-child > a:hover > i {
        background-color: #FFFFFF !important;
    }
</style>

