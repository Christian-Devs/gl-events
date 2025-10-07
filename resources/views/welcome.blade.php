<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="{{ asset('backend/img/logo/logo.png') }}" rel="icon">
    <title>@{{ cfg('company_name', 'BMS Tool') }}</title>
    <link rel="stylesheet" href="{{ mix('css/app.css') }}" type="text/css">
    <link href="{{ asset('backend/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('backend/css/ruang-admin.min.css') }}" rel="stylesheet">
</head>

<body id="page-top">
    <div id="app">
        <div id="wrapper">
            <!-- Sidebar -->
            <nav id="sidebar"
                v-show="$route.path === '/' || $route.path === '/register' || $route.path === '/forgotPassword' ? false : true">
                <ul class="navbar-nav sidebar sidebar-light accordion" id="accordionSidebar">
                    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                        <div class="sidebar-brand-icon">
                            <img src="{{ asset('backend/img/logo/logo.png') }}">
                        </div>
                        <div class="sidebar-brand-text mx-3">@{{ cfg('company_name', 'BMS Tool') }}</div>
                    </a>
                    <hr class="sidebar-divider my-0">
                    <li class="nav-item">
                        <router-link class="nav-link active" to="/home">
                            <i class="fas fa-fw fa-tachometer-alt"></i>
                            <span>Dashboard</span></router-link>
                    </li>
                    <hr class="sidebar-divider">
                    <li class="nav-item" v-if="can(['admin', 'manager'])">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Employees"
                            aria-expanded="true" aria-controls="Employees">
                            <i class="far fa-fw fa-window-maximize"></i>
                            <span>Employees</span>
                        </a>
                        <div id="Employees" class="collapse" aria-labelledby="headingBootstrap"
                            data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <router-link class="collapse-item" to="/add-employee">Add Employee</router-link>
                                <router-link class="collapse-item" to="/employees">All Employees</router-link>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item" v-if="can(['admin', 'manager'])">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Suppliers"
                            aria-expanded="true" aria-controls="Suppliers">
                            <i class="far fa-fw fa-window-maximize"></i>
                            <span>Suppliers</span>
                        </a>
                        <div id="Suppliers" class="collapse" aria-labelledby="headingBootstrap"
                            data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <router-link class="collapse-item" to="/add-supplier">Add Supplier</router-link>
                                <router-link class="collapse-item" to="/suppliers">All Suppliers</router-link>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item" v-if="can(['admin', 'manager', 'staff'])">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Quotations"
                            aria-expanded="true" aria-controls="Quotations">
                            <i class="far fa-fw fa-window-maximize"></i>
                            <span>Quotations</span>
                        </a>
                        <div id="Quotations" class="collapse" aria-labelledby="headingBootstrap"
                            data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <router-link class="collapse-item" to="/add-quote">Add Quote</router-link>
                                <router-link class="collapse-item" to="/quotes">All Quotes</router-link>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item" v-if="can(['admin', 'manager', 'staff'])">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Jobcards"
                            aria-expanded="true" aria-controls="Jobcards">
                            <i class="far fa-fw fa-window-maximize"></i>
                            <span>Job Cards</span>
                        </a>
                        <div id="Jobcards" class="collapse" aria-labelledby="headingBootstrap"
                            data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <router-link class="collapse-item" to="/add-jobcard">Add Job Card</router-link>
                                <router-link class="collapse-item" to="/jobcards">All Job Cards</router-link>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item" v-if="can(['admin', 'manager', 'staff'])">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Invoices"
                            aria-expanded="true" aria-controls="Invoices">
                            <i class="far fa-fw fa-window-maximize"></i>
                            <span>Invoices</span>
                        </a>
                        <div id="Invoices" class="collapse" aria-labelledby="headingBootstrap"
                            data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <router-link class="collapse-item" to="/add-invoice">Add Invoice</router-link>
                                <router-link class="collapse-item" to="/invoices">All Invoices</router-link>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item" v-if="can(['admin'])">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Salaries"
                            aria-expanded="true" aria-controls="Invoices">
                            <i class="far fa-fw fa-window-maximize"></i>
                            <span>Salaries</span>
                        </a>
                        <div id="Salaries" class="collapse" aria-labelledby="headingBootstrap"
                            data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <router-link class="collapse-item" to="/add-salary">Add Salary</router-link>
                                <router-link class="collapse-item" to="/salaries">All Salaries</router-link>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item" v-if="can(['admin', 'manager'])">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Payments"
                            aria-expanded="true" aria-controls="Payments">
                            <i class="far fa-fw fa-window-maximize"></i>
                            <span>Payments</span>
                        </a>
                        <div id="Payments" class="collapse" aria-labelledby="headingBootstrap"
                            data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <router-link class="collapse-item" to="/payments">All Payments</router-link>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item" v-if="can(['admin'])">
                        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Reports"
                            aria-expanded="true" aria-controls="Payments">
                            <i class="far fa-fw fa-window-maximize"></i>
                            <span>Reports</span>
                        </a>
                        <div id="Reports" class="collapse" aria-labelledby="headingBootstrap"
                            data-parent="#accordionSidebar">
                            <div class="bg-white py-2 collapse-inner rounded">
                                <router-link class="collapse-item" to="/quote-report">Quotes Report</router-link>
                                <router-link class="collapse-item" to="/invoice-report">Invoices Report</router-link>
                                <router-link class="collapse-item" to="/payment-report">Payments Report</router-link>
                                <router-link class="collapse-item" to="/jobcard-report">Jobcards Report</router-link>
                                <router-link class="collapse-item" to="/salary-report">Salaries Report</router-link>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item" v-if="can(['admin'])">
                        <router-link class="nav-link" to="/system-settings">
                            <i class="fas fa-cogs"></i>
                            <span>System Settings</span>
                        </router-link>
                    </li>

                    <hr class="sidebar-divider">
                    <div class="version" id="version-ruangadmin"></div>
                </ul>
            </nav>
            <!-- Sidebar -->

            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <!-- TopBar -->
                    <nav v-show="$route.path === '/' || $route.path === '/register' || $route.path === '/forgotPassword' ? false : true"
                        class="navbar navbar-expand navbar-light bg-navbar topbar mb-4 static-top" id="topbar">
                        <button id="sidebarToggleTop" class="btn btn-link rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                        <ul class="navbar-nav ml-auto">
                            <div class="topbar-divider d-none d-sm-block"></div>
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <img class="img-profile rounded-circle"
                                        :src="cfg('company_logo_url') || '{{ asset('backend/img/boy.png') }}'"
                                        style="max-width:36px">
                                    <span class="ml-2 d-none d-lg-inline text-white small">@{{ userName() }}</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userMenu">
                                    <router-link class="dropdown-item" to="/profile">
                                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Profile
                                    </router-link>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="#" @click.prevent="logout">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i> Logout
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    <!-- Topbar -->

                    <!-- Container Fluid-->
                    <div class="container-fluid" id="container-wrapper">
                        <router-view></router-view>
                    </div>
                    <!---Container Fluid-->
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script src="{{ mix('js/app.js') }}"></script>

</body>

</html>