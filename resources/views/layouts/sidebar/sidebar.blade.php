<aside>
    <div id="sidebar" class="nav-collapse">
        <!-- sidebar menu start-->
        <ul class="sidebar-menu">
            <li class="{{ active('home') }}">
                <a class="" href="{{ route('home') }}">
                    <i class="icon_house_alt"></i>
                    <span>Panou</span>
                </a>
            </li>

            @if(Auth::user()->hasRole('Admin'))

                <li class="{{ active('administrare-materiale') }}">
                    <a class="" href="{{ route('administrare-materiale') }}">
                        <i class="fa fa-cube"></i>
                        <span>Materiale</span>
                    </a>
                </li>

                <li class="sub-menu {{ active('creare_articol') }}">
                    <a href="javascript:;" class="">
                        <i class="icon_documents_alt"></i>
                        <span>Articole</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
                    </a>
                    <ul class="sub">
                        <li class="{{ active('creare_articol') }}"><a class="" href="{{ route('creare_articol') }}">Adaugare</a></li>
                        <li class="{{ active('informatii-rapoarte-articole') }}"><a class="" href="{{ route('informatii-rapoarte-articole') }}">Informatii & Rapoarte</a></li>
                    </ul>
                </li>

                <li class="{{ active('groups.index') }}">
                    <a class="" href="{{ route('groups.index') }}">
                        <i class="fa fa-cubes"></i>
                        <span>Grupuri</span>
                    </a>
                </li>

                <li class="sub-menu {{ active('lansare.index') }}">
                    <a href="javascript:;" class="">
                        <i class="fa fa-database"></i>
                        <span>Productie</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
                    </a>
                    <ul class="sub">
                        <li class="{{ active('lansare.index') }}"><a class="" href="{{ route('lansare.index') }}">Lansare</a></li>
                        <li class="{{ active('informatii-rapoarte-produse') }}"><a class="" href="{{ route('informatii-rapoarte-produse') }}">Informatii & Rapoarte</a></li>
                    </ul>
                </li>

                <li class="{{ active('administrare-pompe') }}">
                    <a class="" href="{{ route('administrare-pompe') }}">
                        <i class="fa fa-sitemap"></i>
                        <span>Pompe</span>
                    </a>
                </li>

                <li class="{{ active('administrare-personal') }}">
                    <a class="" href="{{ route('administrare-personal') }}">
                        <i class="fa fa-users"></i>
                        <span>Personal</span>
                    </a>
                </li>

                <li class="{{ active('administrare-rulaje-iesiri') }}">
                    <a class="" href="{{ route('administrare-rulaje-iesiri') }}">
                        <i class="fa fa-arrow-circle-o-up"></i>
                        <span>Rulaje Iesiri</span>
                    </a>
                </li>

                <li class="{{ active('raportare-zilnica') }}">
                    <a class="" href="{{ route('raportare-zilnica') }}">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        <span>Raportare Zilnica</span>
                    </a>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;" class="">
                        <i class="fa fa-adn"></i>
                        <span>Administrare</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
                    </a>
                    <ul class="sub">
                        <li class="{{ active('adaugare-utilizatori') }}"><a class="" href="{{ route('adaugare-utilizatori') }}">Adaugare Utilizatori</a></li>
                        <li class="{{ active('acordare-permisii') }}"><a class="" href="{{ route('acordare-permisii') }}">Permisii</a></li>
						<li class="{{ active('backup.index') }}"><a class="" href="{{ route('backup.index') }}">Backup</a></li>
                    </ul>
                </li>

            @elseif(Auth::user()->hasRole('Ingineri'))
                <li class="{{ active('administrare-materiale') }}">
                    <a class="" href="{{ route('administrare-materiale') }}">
                        <i class="fa fa-cubes"></i>
                        <span>Materiale</span>
                    </a>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;" class="">
                        <i class="icon_documents_alt"></i>
                        <span>Articole</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
                    </a>
                    <ul class="sub">
                        <li class="{{ active('creare_articol') }}"><a class="" href="{{ route('creare_articol') }}">Adaugare</a></li>
                        <li class="{{ active('informatii-rapoarte-articole') }}"><a class="" href="{{ route('informatii-rapoarte-articole') }}">Informatii & Rapoarte</a></li>
                    </ul>
                </li>

				<li class="{{ active('administrare-materiale') }}">
                    <a class="" href="{{ route('groups.index') }}">
                        <i class="fa fa-cubes"></i>
                        <span>Grupuri</span>
                    </a>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;" class="">
                        <i class="fa fa-database"></i>
                        <span>Productie</span>
                        <span class="menu-arrow arrow_carrot-right"></span>
                    </a>
                    <ul class="sub">
                        <li class="{{ active('lansare.index') }}"><a class="" href="{{ route('lansare.index') }}">Lansare</a></li>
                        <li class="{{ active('informatii-rapoarte-produse') }}"><a class="" href="{{ route('informatii-rapoarte-produse') }}">Informatii & Rapoarte</a></li>
                    </ul>
                </li>

                <li class="{{ active('administrare-pompe') }}">
                    <a class="" href="{{ route('administrare-pompe') }}">
                        <i class="fa fa-sitemap"></i>
                        <span>Pompe</span>
                    </a>
                </li>

                <li class="{{ active('administrare-personal') }}">
                    <a class="" href="{{ route('administrare-personal') }}">
                        <i class="fa fa-users"></i>
                        <span>Personal</span>
                    </a>
                </li>

                <li class="{{ active('administrare-rulaje-iesiri') }}">
                    <a class="" href="{{ route('administrare-rulaje-iesiri') }}">
                        <i class="fa fa-arrow-circle-o-up"></i>
                        <span>Rulaje Iesiri</span>
                    </a>
                </li>

                <li class="{{ active('raportare-zilnica') }}">
                    <a class="" href="{{ route('raportare-zilnica') }}">
                        <i class="fa fa-file-text-o" aria-hidden="true"></i>
                        <span>Raportare Zilnica</span>
                    </a>
                </li>

            @elseif(Auth::user()->hasRole('HR'))
                <li class="{{ active('administrare-personal') }}">
                    <a class="" href="{{ route('administrare-personal') }}">
                        <i class="fa fa-users"></i>
                        <span>Personal</span>
                    </a>
                </li>

            @elseif(Auth::user()->hasRole('Magazie'))
                <li class="{{ active('administrare-materiale') }}">
                    <a class="" href="{{ route('administrare-materiale') }}">
                        <i class="fa fa-cubes"></i>
                        <span>Materiale</span>
                    </a>
                </li>

                <li class="{{ active('administrare-rulaje-iesiri') }}">
                    <a class="" href="{{ route('administrare-rulaje-iesiri') }}">
                        <i class="fa fa-arrow-circle-o-up"></i>
                        <span>Rulaje Iesiri</span>
                    </a>
                </li>
            @endif

        </ul>
        <!-- sidebar menu end-->
    </div>
</aside>
