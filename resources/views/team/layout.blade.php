<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Beamble - Backoffice</title>


    @stack('before-styles')
    <link href="{{ asset('assets/team/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/team/css/team.css') }}" rel="stylesheet">
    @stack('after-styles')


  </head>

  <body>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
      <a class="navbar-brand" href="#">Beamble BackOffice</a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      @include('team.partials.menu')

    </nav>

    <main role="main" class="container">

      @include('team.partials.messages')
      
      @yield('content')
    </main><!-- /.container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="{{ asset('assets/team/js/jquery-slim.min.js') }}"><\/script>')</script>
    <script src="{{ asset('assets/team/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/team/js/bootstrap.min.js') }}"></script>
    @stack('after-scripts')
  </body>
</html>
