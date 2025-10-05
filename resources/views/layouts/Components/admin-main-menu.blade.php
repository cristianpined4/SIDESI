<ul class="nav-tabs">
  <li class="nav-tab">
    <a @if(Route::is('dashboard-admin')) class="active" @endif href="{{route('dashboard-admin')}}">Dashboard</a>
  </li>
  <li class="nav-tab">
    <a>Usuarios</a>
  </li>
  <li class="nav-tab">
    <a>Reportes</a>
  </li>
  <li class="nav-tab">
    <a>Configuraciones</a>
  </li>
</ul>