<footer class="main-footer">
    <strong>Copyright &copy; 2025 <a href="https://withmangg.my.id">withMangg</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.1.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->

<script src="{{ asset('/sw.js') }}"></script>
<script>
   if ("serviceWorker" in navigator) {
      // Register a service worker hosted at the root of the
      // site using the default scope.
      navigator.serviceWorker.register("/sw.js").then(
      (registration) => {
         console.log("Service worker registration succeeded:", registration);
      },
      (error) => {
         console.error(`Service worker registration failed: ${error}`);
      },
    );
  } else {
     console.error("Service workers are not supported.");
  }
</script>
<script src="{{ asset('plugins/jquery-ui/jquery-ui.min.js') }}" defer></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
<script src="{{ asset('plugins/toastr/toastr.min.js') }}" defer></script>
<script src="{{ asset('plugins/sparklines/sparkline.js') }}" defer></script>
<script src="{{ asset('plugins/jquery-knob/jquery.knob.min.js') }}" defer></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}" defer></script>
<script src="{{ asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}" defer></script>
<script src="{{ asset('plugins/summernote/summernote-bs4.min.js') }}" defer></script>
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}" defer></script>
<script src="{{ asset('dist/js/adminlte.js') }}" defer></script>
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('plugins/sweetalert2/sweetalert2.all.min.js')}}"></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script> --}}


@stack('scripts')
</body>
</html>
