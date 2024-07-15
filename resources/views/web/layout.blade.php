<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Beamble - Website</title>


    @stack('before-styles')
    <link href="{{ asset('assets/web/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel='stylesheet' href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css'>
    <link href="{{ asset('assets/web/css/web.css') }}" rel="stylesheet">
    @stack('after-styles')


  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="#">Beamble Website</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      @if(Gate::allows('isClient'))

        @include('web.partials.menu')

      @elseif(Gate::allows('isBeamer'))

        @include('web.partials.menu')

      @else

        @include('web.partials.menu_public')

      @endif

    </nav>

    <main role="main" class="container">

      @include('web.partials.messages')
      
      @yield('content')
    </main><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="{{ asset('assets/web/js/libs/jquery-slim.min.js') }}"><\/script>')</script>
    <script src="{{ asset('assets/web/js/libs/popper.min.js') }}"></script>
    <script src="{{ asset('assets/web/js/libs/bootstrap.min.js') }}"></script>
    
    @stack('after-scripts')
    
  </body>
</html>
