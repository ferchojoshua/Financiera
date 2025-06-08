{{-- 
  Este es un archivo wrapper que determinará qué layout usar basado en la variable _layout
  que puede ser pasada a la vista o configurada por el middleware.
--}}

@if(isset($_layout))
    @extends($_layout)
@else
    @extends('layouts.app')
@endif

@section('content')
    @yield('app_content')
@endsection 