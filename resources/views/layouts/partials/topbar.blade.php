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
                    <div class="topbar-item">
                         <a href="{{ route('clientes.birthdays') }}" class="topbar-button position-relative">
                              <iconify-icon icon="solar:cake-outline" class="fs-22 align-middle"></iconify-icon>
                              @if(($todayBirthdayCount ?? 0) > 0)
                                   <span class="position-absolute top-0 start-100 topbar-badge fs-10 translate-middle badge bg-danger rounded-pill">
                                        {{ $todayBirthdayCount }}
                                   </span>
                              @endif
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

@once
    <script>
        (() => {
            if (window.__appManualSidebarToggleInitialized) {
                return;
            }
            window.__appManualSidebarToggleInitialized = true;

            const MOBILE_BREAKPOINT = 1140;
            const html = document.documentElement;
            if (!html) {
                return;
            }

            const readSizeFromConfig = (config) => {
                if (!config || typeof config !== 'object') {
                    return null;
                }
                const size = config.menu && config.menu.size;
                return typeof size === 'string' && size !== 'hidden' ? size : null;
            };

            const resolveDefaultSize = () => {
                const attributeValue = html.getAttribute('data-sidebar-size');
                if (attributeValue && attributeValue !== 'hidden') {
                    return attributeValue;
                }

                const inlineDefault = html.dataset.defaultSidebarSize;
                if (inlineDefault && inlineDefault !== 'hidden') {
                    return inlineDefault;
                }

                return (
                    readSizeFromConfig(window.config) ||
                    readSizeFromConfig(window.defaultConfig) ||
                    'default'
                );
            };

            let lastNonHiddenSize = resolveDefaultSize();
            let backdropElement = null;

            const syncLastKnownSize = () => {
                const current = html.getAttribute('data-sidebar-size');
                if (current && current !== 'hidden') {
                    lastNonHiddenSize = current;
                }
            };

            const isMobileView = () => window.innerWidth <= MOBILE_BREAKPOINT;

            const removeBackdrop = () => {
                if (!backdropElement) {
                    return;
                }
                if (backdropElement.parentNode) {
                    backdropElement.parentNode.removeChild(backdropElement);
                }
                backdropElement = null;
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            };

            const closeSidebar = () => {
                syncLastKnownSize();
                html.setAttribute('data-sidebar-size', 'hidden');
                html.classList.remove('sidebar-enable');
                removeBackdrop();
            };

            const ensureBackdrop = () => {
                if (backdropElement) {
                    return backdropElement;
                }
                const element = document.createElement('div');
                element.className = 'offcanvas-backdrop fade show';
                document.body.appendChild(element);
                document.body.style.overflow = 'hidden';
                if (window.innerWidth > 1040) {
                    document.body.style.paddingRight = '15px';
                }
                element.addEventListener('click', () => {
                    closeSidebar();
                });
                backdropElement = element;
                return backdropElement;
            };

            const openSidebar = () => {
                if (isMobileView()) {
                    html.setAttribute('data-sidebar-size', 'hidden');
                    html.classList.add('sidebar-enable');
                    ensureBackdrop();
                    return;
                }

                const targetSize = lastNonHiddenSize || resolveDefaultSize();
                html.setAttribute('data-sidebar-size', targetSize);
                html.classList.add('sidebar-enable');
                removeBackdrop();
            };

            const toggleSidebar = () => {
                if (isMobileView()) {
                    if (html.classList.contains('sidebar-enable')) {
                        closeSidebar();
                    } else {
                        syncLastKnownSize();
                        openSidebar();
                    }
                    return;
                }

                const current = html.getAttribute('data-sidebar-size');
                if (!current || current === 'hidden') {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            };

            const handleResize = () => {
                if (isMobileView()) {
                    html.setAttribute('data-sidebar-size', 'hidden');
                    if (html.classList.contains('sidebar-enable')) {
                        ensureBackdrop();
                    } else {
                        removeBackdrop();
                    }
                    return;
                }

                removeBackdrop();
                const current = html.getAttribute('data-sidebar-size');
                if (current && current !== 'hidden') {
                    lastNonHiddenSize = current;
                } else {
                    html.setAttribute('data-sidebar-size', lastNonHiddenSize || resolveDefaultSize());
                }
            };

            const handleKeydown = (event) => {
                if (event.key === 'Escape' && html.classList.contains('sidebar-enable') && isMobileView()) {
                    closeSidebar();
                }
            };

            const bindButtons = () => {
                const buttons = document.querySelectorAll('.button-toggle-menu');
                if (!buttons.length) {
                    return false;
                }

                buttons.forEach((button) => {
                    button.dataset.manualSidebarToggle = '1';
                    button.addEventListener('click', (event) => {
                        event.preventDefault();
                        event.stopImmediatePropagation();
                        toggleSidebar();
                    });
                });

                return true;
            };

            const init = () => {
                if (!document.querySelector('.app-sidebar')) {
                    return;
                }

                if (!bindButtons()) {
                    return;
                }

                syncLastKnownSize();
                handleResize();
                window.addEventListener('resize', handleResize);
                document.addEventListener('keydown', handleKeydown);
            };

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', init);
            } else {
                init();
            }
        })();
    </script>
@endonce

