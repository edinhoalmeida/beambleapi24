<script src="{{ asset('webview/assets/js/webview.js') }}"></script>

<!-- Page specific script -->
@yield('footer_scripts')

@if(config('app.env') == 'local')
    <script src="http://localhost:35729/livereload.js"></script>
@endif
</body>
</html>
