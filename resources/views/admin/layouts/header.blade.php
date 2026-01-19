  <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
           
          </ul>
          <!--end::Start Navbar Links-->
          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::Navbar Search-->
           {{-- @include('admin.layouts.search')
            <!--end::Navbar Search-->
            <!--begin::Messages Dropdown Menu-->
            @include('admin.layouts.messages')
            <!--end::Messages Dropdown Menu-->
            <!--begin::Notifications Dropdown Menu-->
            @include('admin.layouts.notifications')
            <!--end::Notifications Dropdown Menu-->
            <!--begin::User Menu Dropdown--> --}}
           @include('admin.layouts.profile_mini')
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>