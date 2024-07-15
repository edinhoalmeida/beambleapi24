<!-- jQuery -->
<script src="{{ asset('team/adminlte/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('team/adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>


<!-- DataTables  & Plugins -->
<script src="{{ asset('team/adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('team/adminlte/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>


<!-- AdminLTE App -->
<script src="{{ asset('team/adminlte/plugins/jquery-validation/jquery.validate.min.js') }}"></script>

<script src="{{ asset('team/adminlte/plugins/jquery-validation/additional-methods.min.js') }}"></script>

<script src="{{ asset('team/adminlte/js/adminlte.min.js') }}"></script>

<script src="{{ asset('team/js/team.js') }}"></script>

<!-- Page specific script -->
@yield('footer_scripts')

@if(config('app.env') == 'local')
    <script src="http://localhost:35729/livereload.js"></script>
@endif
</body>
</html>
