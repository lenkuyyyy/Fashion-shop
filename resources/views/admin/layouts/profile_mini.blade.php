 <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                {{-- <img
             
                   src="{{ asset('dist/assets/img/user2-160x160.jpg')}}"
                  class="user-image rounded-circle shadow"
                  alt="User Image"
                /> --}}
                <span class="d-none d-md-inline">Admin</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  {{-- <img
                   
                     src="{{ asset('dist/assets/img/user2-160x160.jpg')}}"
                    class="rounded-circle shadow"
                    alt="User Image"
                  /> --}}
                  
                  <p>
                    Admin - Web Developer
                    <small>Team member HN447 since Nov. 2025 by</small>
                    <small>1. Bùi Văn Dương</small>
                    <small>2. Nguyễn Công Minh</small>
                    <small>3. Trần Đức Lương</small>
                    <small>4. Nguyễn Thị Thảo Uyên</small>
                    <small>5. Vũ Tiến Dũng</small>
                    <small>6. Lê Văn Khang</small>
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Body-->
                {{-- <li class="user-body">
                  <!--begin::Row-->
                  <div class="row">
                    <div class="col-4 text-center"><a href="#">Followers</a></div>
                    <div class="col-4 text-center"><a href="#">Sales</a></div>
                    <div class="col-4 text-center"><a href="#">Friends</a></div>
                  </div>
                  <!--end::Row-->
                </li> --}}
                <!--end::Menu Body-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                  <a href="{{ route('logout') }}" class="btn btn-default btn-flat float-end">Đăng xuất</a>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>