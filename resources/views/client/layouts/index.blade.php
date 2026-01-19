<!DOCTYPE html>
<html lang="en">

@include('client.layouts.head')
{{-- end head --}}

<body>

    <!-- ***** Preloader Start ***** -->
    @include('client.layouts.preloader')
    <!-- ***** Preloader End ***** -->


    <!-- ***** Header Area Start ***** -->
    @include('client.layouts.header')
    <!-- Header Area End -->
    <!-- ***** Header Area End ***** -->

    <!-- ***** Main Banner Area Start ***** -->
    @include('client.layouts.banner')

    <!-- ***** Main Banner Area End ***** -->

    <!-- ***** Men Area Starts ***** -->
    {{-- đây là 1 danh mục --}}
    @include('client.layouts.men')

    <!-- ***** Men Area Ends ***** -->

    <!-- ***** Women Area Starts ***** -->
    @include('client.layouts.women')

    <!-- ***** Women Area Ends ***** -->

    <!-- ***** Kids Area Starts ***** -->
    @include('client.layouts.kids')

    <!-- ***** Kids Area Ends ***** -->

    <!-- ***** Explore Area Starts ***** -->
    @include('client.layouts.explore')
    <!-- ***** Explore Area Ends ***** -->

    <!-- ***** Social Area Starts ***** -->
    @include('client.layouts.social')

    <!-- ***** Social Area Ends ***** -->

    <!-- ***** Subscribe Area Starts ***** -->
    {{-- @include('client.layouts.subscribe') --}}

    <!-- ***** Subscribe Area Ends ***** -->

    <!-- ***** Footer Start ***** -->
    @include('client.layouts.footer')
    {{-- end ***** Footer End ***** --> --}}

    @include('client.layouts.scripts')
</body>

</html>
