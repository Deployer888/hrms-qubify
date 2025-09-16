 <!-- sidebar menu area start -->
 @php
     $usr = Auth::guard('admin')->user();
 @endphp
 <div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <h2 class="text-white">Admin</h2> 
            </a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">

                    {{-- @if ($usr->can('dashboard.view')) --}}
                    <li class="active">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-dashboard"></i><span>dashboard</span></a>
                        <ul class="collapse">
                            <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        </ul>
                    </li>
                    {{-- @endif --}}

                    {{-- @if ($usr->can('role.create') || $usr->can('role.view') ||  $usr->can('role.edit') ||  $usr->can('role.delete')) --}}
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                            Roles & Permissions
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.roles.create') || Route::is('admin.roles.index') || Route::is('admin.roles.edit') || Route::is('admin.roles.show') ? 'in' : '' }}">
                        {{-- @if ($usr->can('role.view')) --}}
                                <li class="{{ Route::is('admin.roles.index')  || Route::is('admin.roles.edit') ? 'active' : '' }}"><a href="{{ route('admin.roles.index') }}">All Roles</a></li>
                                {{-- @endif --}}
                                {{-- @if ($usr->can('role.create')) --}}
                                <li class="{{ Route::is('admin.roles.create')  ? 'active' : '' }}"><a href="{{ route('admin.roles.create') }}">Create Role</a></li>
                                {{-- @endif --}}
                        </ul>
                    </li>
                    {{-- @endif --}}

                    
                    {{-- @if ($usr->can('admin.create') || $usr->can('admin.view') ||  $usr->can('admin.edit') ||  $usr->can('admin.delete')) --}}
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            Admins
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.admins.create') || Route::is('admin.admins.index') || Route::is('admin.admins.edit') || Route::is('admin.admins.show') ? 'in' : '' }}">
                            
                            {{-- @if ($usr->can('admin.view')) --}}
                                <li class="{{ Route::is('admin.admins.index')  || Route::is('admin.admins.edit') ? 'active' : '' }}"><a href="{{ route('admin.admins.index') }}">All Admins</a></li>
                                {{-- @endif --}}

                            {{-- @if ($usr->can('admin.create')) --}}
                                <li class="{{ Route::is('admin.admins.create')  ? 'active' : '' }}"><a href="{{ route('admin.admins.create') }}">Create Admin</a></li>
                            {{-- @endif --}}
                        </ul>
                    </li>
                    {{-- @endif --}}

                    <!-- home options  -->
                    {{-- @if ($usr->can('industry.create') || $usr->can('industry.view') ||  $usr->can('industry.edit') ||  $usr->can('industry.delete')) --}}
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                            Home
                        </span></a>                        
                        <ul class="collapse {{ Route::is('admin.home-sliders.create') || Route::is('admin.home-sliders.index') || Route::is('admin.home-sliders.edit') || Route::is('admin.home-sliders.show') ? 'in' : '' }}">
                            {{-- @if ($usr->can('industry.view')) --}}
                                <li class="{{ Route::is('admin.home-sliders.index')  || Route::is('admin.home-sliders.edit') ? 'active' : '' }}"><a href="{{ route('admin.home-sliders.index') }}">Home Sliders</a></li>
                            {{-- @endif --}}
                            {{-- @if ($usr->can('industry.view')) --}}
                                <li class="{{ Route::is('admin.trusted-logos.index')  || Route::is('admin.trusted-logos.edit') ? 'active' : '' }}"><a href="{{ route('admin.trusted-logos.index') }}">Trusted Logos</a></li>
                            {{-- @endif --}}
                            {{-- @if ($usr->can('industry.view')) --}}
                                <li class="{{ Route::is('admin.home-roadmaps.index')  || Route::is('admin.home-roadmaps.edit') ? 'active' : '' }}"><a href="{{ route('admin.home-roadmaps.index') }}">Roadmap</a></li>
                            {{-- @endif --}}
                            <!-- <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                                    Home Sliders
                                </span></a>
                                <ul class="collapse {{ Route::is('admin.industry-lists.create') || Route::is('admin.industry-lists.index') || Route::is('admin.industry-lists.edit') || Route::is('admin.industry-lists.show') ? 'in' : '' }}">
                                    {{-- @if ($usr->can('industry.view')) --}}
                                        <li class="{{ Route::is('admin.industry-lists.index')  || Route::is('admin.industry-lists.edit') ? 'active' : '' }}"><a href="{{ route('admin.industry-lists.index') }}">All Industries</a></li>
                                    {{-- @endif --}}
                                    {{-- @if ($usr->can('industry.create')) --}}
                                        <li class="{{ Route::is('admin.industry-lists.create')  ? 'active' : '' }}"><a href="{{ route('admin.industry-lists.create') }}">Create Industry</a></li>
                                    {{-- @endif --}}
                                </ul>
                            </li> -->
                        </ul>
                    </li>
                    {{-- @endif --}}
                    <!-- end hone options  -->

                    {{-- @if ($usr->can('industry.create') || $usr->can('industry.view') ||  $usr->can('industry.edit') ||  $usr->can('industry.delete')) --}}
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                            Industries
                        </span></a>                        
                        <ul class="collapse {{ Route::is('admin.industries.create') || Route::is('admin.industries.index') || Route::is('admin.industries.edit') || Route::is('admin.industries.show') ? 'in' : '' }}">
                            {{-- @if ($usr->can('industry.view')) --}}
                                <li class="{{ Route::is('admin.industries.index')  || Route::is('admin.industries.edit') ? 'active' : '' }}"><a href="{{ route('admin.industries.index') }}">Industry Types</a></li>
                            {{-- @endif --}}
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                                    Industry List
                                </span></a>
                                <ul class="collapse {{ Route::is('admin.industry-lists.create') || Route::is('admin.industry-lists.index') || Route::is('admin.industry-lists.edit') || Route::is('admin.industry-lists.show') ? 'in' : '' }}">
                                    {{-- @if ($usr->can('industry.view')) --}}
                                        <li class="{{ Route::is('admin.industry-lists.index')  || Route::is('admin.industry-lists.edit') ? 'active' : '' }}"><a href="{{ route('admin.industry-lists.index') }}">All Industries</a></li>
                                    {{-- @endif --}}
                                    {{-- @if ($usr->can('industry.create')) --}}
                                        <li class="{{ Route::is('admin.industry-lists.create')  ? 'active' : '' }}"><a href="{{ route('admin.industry-lists.create') }}">Create Industry</a></li>
                                    {{-- @endif --}}
                                </ul>
                            </li>
                        </ul>
                    </li>
                    {{-- @endif --}}

                    {{-- @if ($usr->can('service.create') || $usr->can('service.view') ||  $usr->can('service.edit') ||  $usr->can('service.delete'))  --}}
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                            Services
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.services.create') || Route::is('admin.services.index') || Route::is('admin.services.edit') || Route::is('admin.services.show') ? 'in' : '' }}">
                            {{-- @if ($usr->can('service.view')) --}}
                                <li class="{{ Route::is('admin.services.index')  || Route::is('admin.services.edit') ? 'active' : '' }}"><a href="{{ route('admin.services.index') }}">All Services</a></li>
                            {{-- @endif --}}
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                                    Service List
                                </span></a>
                                <ul class="collapse {{ Route::is('admin.service-lists.create') || Route::is('admin.service-lists.index') || Route::is('admin.service-lists.edit') || Route::is('admin.service-lists.show') ? 'in' : '' }}">
                                    {{-- @if ($usr->can('industry.view')) --}}
                                        <li class="{{ Route::is('admin.service-lists.index')  || Route::is('admin.service-lists.edit') ? 'active' : '' }}"><a href="{{ route('admin.service-lists.index') }}">All Services</a></li>
                                    {{-- @endif --}}
                                    {{-- @if ($usr->can('industry.create')) --}}
                                        <li class="{{ Route::is('admin.service-lists.create')  ? 'active' : '' }}"><a href="{{ route('admin.service-lists.create') }}">Create Service</a></li>
                                    {{-- @endif --}}
                                </ul>
                            </li>
                        </ul>
                    </li>
                    {{-- @endif --}}

                    {{-- @if ($usr->can('technology.create') || $usr->can('technology.view') ||  $usr->can('technology.edit') ||  $usr->can('technology.delete')) --}}
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                            Technologies
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.technologies.create') || Route::is('admin.technologies.index') || Route::is('admin.technologies.edit') || Route::is('admin.technologies.show') ? 'in' : '' }}">
                            {{-- @if ($usr->can('technology.view')) --}}
                                <li class="{{ Route::is('admin.technologies.index')  || Route::is('admin.technologies.edit') ? 'active' : '' }}"><a href="{{ route('admin.technologies.index') }}">All Technologies Types</a></li>
                            {{-- @endif --}}
                            <li>
                                <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                                    Technology List
                                </span></a>
                                <ul class="collapse {{ Route::is('admin.technology-lists.create') || Route::is('admin.technology-lists.index') || Route::is('admin.technology-lists.edit') || Route::is('admin.technology-lists.show') ? 'in' : '' }}">
                                    {{-- @if ($usr->can('industry.view')) --}}
                                        <li class="{{ Route::is('admin.technology-lists.index')  || Route::is('admin.technology-lists.edit') ? 'active' : '' }}"><a href="{{ route('admin.technology-lists.index') }}">All Technology</a></li>
                                    {{-- @endif --}}
                                    {{-- @if ($usr->can('industry.create')) --}}
                                        <li class="{{ Route::is('admin.technology-lists.create')  ? 'active' : '' }}"><a href="{{ route('admin.technology-lists.create') }}">Create Technology</a></li>
                                    {{-- @endif --}}
                                </ul>
                            </li>
                        </ul>
                    </li>
                    {{-- @endif --}}

                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->