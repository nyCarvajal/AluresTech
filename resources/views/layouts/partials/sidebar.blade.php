@php
    $sidebarStylistLabels = \App\Support\RoleLabelResolver::forStylist();
    $sidebarStylistLabelSingular = $sidebarStylistLabels['singular'];
@endphp

<div class="app-sidebar">
     <!-- Sidebar Logo -->
     <div class="logo-box">
          <a href="{{route('dashboard') }}" class="logo-dark">
               <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
               <img src="/images/logodarkal.png" class="logo-lg" alt="logo dark">
          </a>

          <a href="" class="logo-light">
               <img src="/images/logo-sm.png" class="logo-sm" alt="logo sm">
               <img src="/images/logo-light.png" class="logo-lg" alt="logo light">
          </a>
     </div>

     <div class="scrollbar" data-simplebar>

          <ul class="navbar-nav" id="navbar-nav">

               <li class="menu-title">Menu...</li>

               <li class="nav-item">
                    <a class="nav-link" href="{{route('dashboard') }}">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:home-3-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Inicio </span>
                         
                    </a>
               </li>
			    <li class="nav-item">
                    <a class="nav-link" href="{{ route('ventas.index') }}">
                         <span class="nav-icon">
                              <iconify-icon icon="solar:cart-large-4-bold-duotone"></iconify-icon>
                         </span>
                         <span class="nav-text"> Facturación </span>
                         
                    </a>
               </li>
			    <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarAuthentication" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarAuthentication">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:user-3-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Clientes </span>
                    </a>
                    <div class="collapse" id="sidebarAuthentication">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('clientes.create') }}">Crear</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('clientes.index') }}">Listar</a>
                              </li>
                            
                         </ul>
                    </div>
               </li>
			   
               <li class="nav-item">
                    <a class="nav-link" href="{{ route('reservas.calendar') }}">
                         <span class="nav-icon">
                              <i class="bx  bx-calendar-alt"  ></i> </span>
                         <span class="nav-text"> Citas </span>
                    </a>
               </li>
			     <li class="nav-item">
                    <a class="nav-link" href="{{ route('items.index') }}">
                         <span class="nav-icon">
						 <i class="bx bx-dock-left"></i> 
                               </span>
                         <span class="nav-text"> Productos/Servicios </span>
                    </a>
               </li>
			   
			   
			   <li class="menu-title">Configuración</li>

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarLayouts" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarLayouts">
                         <span class="nav-icon">
                             <i class='bx  bx-cog'  ></i> 
                         </span>
                         <span class="nav-text"> Configuración </span>
                    </a>
                    <div class="collapse" id="sidebarLayouts">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('tipocitas.index') }}">
                                        Tipos de Citas</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('tipo-identificaciones.index') }}">
                                        Tipos de identificaciones</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('areas.index') }}">
                                        Listar áreas</a>
                              </li>

                         </ul>
                    </div>
               </li>
			   <li class="nav-item">
                    <a class="nav-link" href="{{ route('peluquerias.edit') }}">
                         <span class="nav-icon">
						 <i class="bx bx-pencil"></i>
                               </span>
                         <span class="nav-text"> Personalizar Peluqueria  </span>
                    </a>
               </li>
			   

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarAuthentication" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarAuthentication">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:user-3-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Usuarios </span>
                    </a>
                    <div class="collapse" id="sidebarAuthentication">
                         <ul class="nav sub-navbar-nav">
						 
							<li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('users.admins.create') }}">Crea Usuario</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('users.trainers.create') }}">Crea {{ $sidebarStylistLabelSingular }}</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('users.index') }}">Listar Usuarios</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route('peluquerias.edit') }}#role-labels">
                                        Renombrar {{ $sidebarStylistLabelPlural }}
                                   </a>
                              </li>


                         </ul>
                    </div>
               </li>

               

               <li class="menu-title">Administración</li>

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarBaseUI" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarBaseUI">
                         <span class="nav-icon"><i class="bx bx-money-withdraw"></i></span>
                         <span class="nav-text"> Caja </span>
                    </a>
                    <div class="collapse" id="sidebarBaseUI">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('cajas.index') }}">Caja de hoy</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('cajas.index') }}">Listar Cajas</a>
                              </li>
							  <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('pagos.index') }}">Pagos</a>
                              </li>
							  <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('salidas.index') }}">Gastos</a>
                              </li>
                              
                         </ul>
                    </div>
               </li>

               <li class="nav-item">
                    <a class="nav-link" href="{{ route('pages.charts') }}">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:chart-bar-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Informes Administrativos </span>
                    </a>
               </li>
			   <li class="nav-item">
                    <a class="nav-link" href="{{ route ('proveedores.index') }}">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:chart-bar-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Proveedores </span>
                    </a>
               </li>

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarForms" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarForms">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:box-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Salidas </span>
                    </a>
                    <div class="collapse" id="sidebarForms">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('salidas.index') }}">Listar</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('salidas.create') }}">Crear</a>
                              </li>
                             
                         </ul>
                    </div>
               </li>
<?php /**			   <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarError" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarError">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:bug-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Error Pages</span>
                    </a>
                    <div class="collapse" id="sidebarError">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['pages','404']) }}">Pages 404</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['pages','404-alt']) }}">Pages 404 Alt</a>
                              </li>
							   <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['forms','validation']) }}">Validation</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['forms','fileuploads']) }}">File Upload</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['forms','editors']) }}">Editors</a>
                              </li>
							  <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','avatar']) }}">Avatar</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','badge']) }}">Badge</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','breadcrumb']) }}">Breadcrumb</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','buttons']) }}">Buttons</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','card']) }}">Card</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','carousel']) }}">Carousel</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','collapse']) }}">Collapse</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','dropdown']) }}">Dropdown</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','list-group']) }}">List Group</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','modal']) }}">Modal</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','tabs']) }}">Tabs</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','offcanvas']) }}">Offcanvas</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','pagination']) }}">Pagination</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','placeholders']) }}">Placeholders</a>
                              </li>
							   <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['auth','signup']) }}">Sign Up</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['auth','password']) }}">Reset Password</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['auth','lock-screen']) }}">Lock Screen</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','popovers']) }}">Popovers</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','progress']) }}">Progress</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','scrollspy']) }}">Scrollspy</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','spinners']) }}">Spinners</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','toasts']) }}">Toasts</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['ui','tooltips']) }}">Tooltips</a>
                              </li>
                         </ul>
                    </div>
               </li>

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarTables" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarTables">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:table-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Tables </span>
                    </a>
                    <div class="collapse" id="sidebarTables">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['tables','basic']) }}">Basic Tables</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['tables','gridjs']) }}">Grid Js</a>
                              </li>
                         </ul>
                    </div>
               </li>

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarIcons" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarIcons">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:dribbble-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Icons </span>
                    </a>
                    <div class="collapse" id="sidebarIcons">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['icons','boxicons']) }}">Boxicons</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['icons','solar']) }}">Solar Icons</a>
                              </li>
                         </ul>
                    </div>
               </li>

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarMaps" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarMaps">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:map-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Maps </span>
                    </a>
                    <div class="collapse" id="sidebarMaps">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['maps','google']) }}">Google Maps</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="{{ route ('second' , ['maps','vector']) }}">Vector Maps</a>
                              </li>
                         </ul>
                    </div>
               </li>

               

               <li class="nav-item">
                    <a class="nav-link menu-arrow" href="#sidebarMultiLevelDemo" data-bs-toggle="collapse" role="button"
                         aria-expanded="false" aria-controls="sidebarMultiLevelDemo">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:menu-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Menu Item </span>
                    </a>
                    <div class="collapse" id="sidebarMultiLevelDemo">
                         <ul class="nav sub-navbar-nav">
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link" href="javascript:void(0);">Menu Item 1</a>
                              </li>
                              <li class="sub-nav-item">
                                   <a class="sub-nav-link  menu-arrow" href="#sidebarItemDemoSubItem"
                                        data-bs-toggle="collapse" role="button" aria-expanded="false"
                                        aria-controls="sidebarItemDemoSubItem">
                                        <span> Menu Item 2 </span>
                                   </a>
                                   <div class="collapse" id="sidebarItemDemoSubItem">
                                        <ul class="nav sub-navbar-nav">
                                             <li class="sub-nav-item">
                                                  <a class="sub-nav-link" href="javascript:void(0);">Menu Sub item</a>
                                             </li>
                                        </ul>
                                   </div>
                              </li>
                         </ul>
                    </div>
               </li>

               <li class="nav-item">
                    <a class="nav-link disabled" href="javascript:void(0);">
                         <span class="nav-icon">
                              <iconify-icon icon="mingcute:close-circle-line"></iconify-icon>
                         </span>
                         <span class="nav-text"> Disable Item </span>
                    </a>
               </li>
			   */ ?>
          </ul>
     </div>
</div>
