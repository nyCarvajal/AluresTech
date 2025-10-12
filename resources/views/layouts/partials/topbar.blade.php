<header class="app-topbar">
     <div class="container-fluid">
          <div class="navbar-header">
               <div class="d-flex align-items-center gap-2">
                    <!-- Menu Toggle Button -->
                    <div class="topbar-item">
                         <button type="button" class="button-toggle-menu topbar-button">
                              <iconify-icon icon="solar:hamburger-menu-outline"
                                   class="fs-24 align-middle"></iconify-icon>
                         </button>
                    </div>

                    <!-- App Search-->
                    <form class="app-search d-none d-md-block me-auto">
                         <div class="position-relative">
                              <input type="search" class="form-control" placeholder="admin,widgets..."
                                   autocomplete="off" value="">
                              <iconify-icon icon="solar:magnifer-outline" class="search-widget-icon"></iconify-icon>
                         </div>
                    </form>
               </div>

               <div class="d-flex align-items-center gap-2">
                    <!-- Theme Color (Light/Dark) -->
                    <div class="topbar-item">
                         <button type="button" class="topbar-button position-relative">
						  <a href="{{ route('reservas.calendar') }}" class="fs-22 align-middle">
                              <i class="bx bx-calendar"></i>                                
                              
                         </a>
                              
                         </button>
                    </div>
                    <div class="topbar-item">
                         <a href="{{ route('reservas.pending') }}" class="topbar-button position-relative">
                              <iconify-icon icon="solar:bell-bing-outline" class="fs-22 align-middle"></iconify-icon>
                              <span class="position-absolute top-0 start-100 topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">
                                   {{ $pendingReservationsCount ?? 0 }}
                              </span>
                         </a>
                    </div>

                    <!-- User -->
                    <div class="dropdown topbar-item">
                         <a type="button" class="topbar-button" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                              aria-haspopup="true" aria-expanded="false">
                              <span class="d-flex align-items-center">
                                   <img class="rounded-circle" width="32" src="/images/users/avatar-1.jpg"
                                        alt="avatar-3">
                              </span>
                         </a>
                         <div class="dropdown-menu dropdown-menu-end">
                              <!-- item-->
                              <h6 class="dropdown-header">¡Bienvenido!</h6>

                              <a class="dropdown-item" href="#">
                                   <iconify-icon icon="solar:user-outline"
                                        class="align-middle me-2 fs-18"></iconify-icon><span class="align-middle">Mi
                                        Cuenta</span>
                              </a>

                              <a class="dropdown-item" href="#">
                                   <iconify-icon icon="solar:wallet-outline"
                                        class="align-middle me-2 fs-18"></iconify-icon><span
                                        class="align-middle">Pagos</span>
                              </a>
                              <a class="dropdown-item" href="#">
                                   <iconify-icon icon="solar:help-outline"
                                        class="align-middle me-2 fs-18"></iconify-icon><span
                                        class="align-middle">Ayuda</span>
                              </a>
                              

                              <div class="dropdown-divider my-1"></div>

                             <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<a class="dropdown-item text-danger"
   href="#"
   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
   <iconify-icon icon="solar:logout-3-outline"
                  class="align-middle me-2 fs-18"></iconify-icon>
   <span class="align-middle">Logout</span>
</a>

                         </div>
                    </div>
               </div>
          </div>
     </div>
</header>