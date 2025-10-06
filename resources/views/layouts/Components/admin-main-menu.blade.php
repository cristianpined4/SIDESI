<ul class="nav-tabs">
  <li class="nav-tab">
    <a @if(Route::is('admin.dashboard')) class="active" @endif href="{{route('admin.dashboard')}}">Dashboard</a>
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