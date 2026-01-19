<!DOCTYPE html>
<html lang="en">

   @include('client.layouts.head')
    {{-- end head --}}
    <body class="d-flex flex-column min-vh-100">
    <!-- ***** Header Area Start ***** -->
    @include('client.layouts.header')
    <!-- Header Area End -->    
    <!-- ***** Header Area End ***** -->
    <main class="flex-fill">
        <div class="container mt-5 pt-5">
            @yield('content')
        </div>
    </main>
    
    <!-- ***** Footer Start ***** -->
    @include('client.layouts.footer')
  
    
    @include('client.layouts.scripts')
    <!-- ***** Footer End ***** -->
    @yield('scripts')
    <script src="{{ asset('assets/js/cart.js') }}"></script>
  </body>
</html>