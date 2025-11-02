<ul class="nav-tabs">
  <li class="nav-tab">
    <a @if(Route::is('admin.dashboard')) class="active" @endif href="{{route('admin.dashboard')}}">Dashboard</a>
  </li>
  <li class="nav-tab">
    <a @if(Route::is('admin.eventos')) class="active" @endif href="{{route('admin.eventos')}}">Eventos</a>
  </li>
  <li class="nav-tab">
    <a @if(Route::is('admin.noticias')) class="active" @endif href="{{route('admin.noticias')}}">Noticias</a>
  </li>
  <li class="nav-tab">
    <a @if(Route::is('admin.ofertas')) class="active" @endif href="{{route('admin.ofertas')}}">Bolsa de Empleo</a>
  </li>
  <li class="nav-tab">
    <a @if(Route::is('admin.documentos')) class="active" @endif href="{{route('admin.documentos')}}">Documentos</a>
  </li>
  <li class="nav-tab">
    <a @if(Route::is('admin.usuarios')) class="active" @endif href="{{route('admin.usuarios')}}">Usuarios</a>
  </li>
</ul>