<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="./">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </div>

        <div class="row mb-3">
            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Costs (2024)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R6 567 523.83</div>
                                <div class="mt-2 mb-0 text-muted text-xs">
                                    <span class="text-danger mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span>
                                    <span>Since last year</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-shopping-cart fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Earnings (Annual) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Total Profit (2024)</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">R12 825 342.32</div>
                                <div class="mt-2 mb-0 text-muted text-xs">
                                    <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span>
                                    <span>Since last year</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- New User Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Jobcards to Date</div>
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">366</div>
                                <div class="mt-2 mb-0 text-muted text-xs">
                                    <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 20.4%</span>
                                    <span>Since last month</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-uppercase mb-1">Pending Requests</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                                <div class="mt-2 mb-0 text-muted text-xs">
                                    <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 1.10%</span>
                                    <span>Since yesterday</span>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-comments fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Example -->
            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Work In Progress</h6>
                        <router-link class="m-0 float-right btn btn-danger btn-sm" to="/jobcards">View More <i
                                class="fas fa-chevron-right"></i></router-link>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <tr>
                                    <th>Job Number</th>
                                    <th>Stand Name</th>
                                    <th>Show Name</th>
                                    <th>Status</th>
                                    <th>DTP</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#">QT0057642</a></td>
                                    <td>Medical Conepts</td>
                                    <td>Hospital Event</td>
                                    <td><span class="badge badge-success">Delivered</span></td>
                                    <td>Isaac Faranando</td>
                                    <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td><a href="#">QT0057673</a></td>
                                    <td>HQ Mount THCI</td>
                                    <td>Solar 2025</td>
                                    <td><span class="badge badge-warning">Waiting for Approval</span></td>
                                    <td>McFadden Barley</td>
                                    <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td><a href="#">QT0057805</a></td>
                                    <td>SARCDA / SEDFA</td>
                                    <td>SARCDA 2025</td>
                                    <td><span class="badge badge-danger">Waiting for Artwork</span></td>
                                    <td>Shameemah Barnett</td>
                                    <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td><a href="#">QT0058014</a></td>
                                    <td>Huawei - Office 24</td>
                                    <td>Solar 2025</td>
                                    <td><span class="badge badge-info">Printing</span></td>
                                    <td>Lovejoy Mashiri</td>
                                    <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>
                                </tr>
                                <tr>
                                    <td><a href="#">QT0057916</a></td>
                                    <td>Infinite Energy Storage</td>
                                    <td>Solar 2025</td>
                                    <td><span class="badge badge-success">Finishing</span></td>
                                    <td>Shameemah Barnett</td>
                                    <td><a href="#" class="btn btn-sm btn-primary">Detail</a></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer"></div>
                </div>
            </div>
            <!-- Pie Chart -->
            <div class="col-xl-4 col-lg-5">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Month to Date</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle btn btn-primary btn-sm" href="#" role="button"
                                id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Month <i class="fas fa-chevron-down"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">Select Periode</div>
                                <a class="dropdown-item" href="#">Today</a>
                                <a class="dropdown-item" href="#">Week</a>
                                <a class="dropdown-item active" href="#">Month</a>
                                <a class="dropdown-item" href="#">This Year</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="small text-gray-500">Fascia Names
                                <div class="small float-right"><b>600 of 800 Items</b></div>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 80%"
                                    aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="small text-gray-500">(UV) Backlit Fabric Banners
                                <div class="small float-right"><b>500 of 800 Items</b></div>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 70%"
                                    aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="small text-gray-500">(Dye Sub) Fabric Banners
                                <div class="small float-right"><b>455 of 800 Items</b></div>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 55%"
                                    aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="small text-gray-500">Vinyl Printing (UV)
                                <div class="small float-right"><b>400 of 800 Items</b></div>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 50%"
                                    aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="small text-gray-500">Vinyl Printing (Laminated)
                                <div class="small float-right"><b>200 of 800 Items</b></div>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 30%"
                                    aria-valuenow="30" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a class="m-0 small text-primary card-link" href="#">View More <i
                                class="fas fa-chevron-right"></i></a>
                    </div>
                </div>
            </div>
            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Graphics - P&L Tracking</h6>
                        <div class="dropdown no-arrow">
                            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                aria-labelledby="dropdownMenuLink">
                                <div class="dropdown-header">Dropdown Header:</div>
                                <a class="dropdown-item" href="#">Action</a>
                                <a class="dropdown-item" href="#">Another action</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#">Something else here</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div>
                            <BarChart/>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Message From Customer-->
            <div class="col-xl-4 col-lg-5 ">
                <div class="card">
                    <div class="card-header py-4 bg-primary d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-light">Message From AE/PM</h6>
                    </div>
                    <div>
                        <div class="customer-message align-items-center">
                            <a class="font-weight-bold" href="#">
                                <div class="text-truncate message-title">JEC Counter</div>
                                <div class="small text-gray-500 message-time font-weight-bold">Isaac Faranando · 15:51</div>
                            </a>
                        </div>
                        <div class="customer-message align-items-center">
                            <a href="#">
                                <div class="text-truncate message-title">Network Issues
                                </div>
                                <div class="small text-gray-500 message-time">Seema Ramnath · 12:38</div>
                            </a>
                        </div>
                        <div class="customer-message align-items-center">
                            <a class="font-weight-bold" href="#">
                                <div class="text-truncate message-title">Rand Show Meal Vouchers
                                </div>
                                <div class="small text-gray-500 message-time font-weight-bold">Saud Khan, Pregasen Francis · Sun 15:10</div>
                            </a>
                        </div>
                        <div class="customer-message align-items-center">
                            <a class="font-weight-bold" href="#">
                                <div class="text-truncate message-title">Meeting - Leshem's Office
                                </div>
                                <div class="small text-gray-500 message-time font-weight-bold">Leshem Naidoo · Wed 26/03</div>
                            </a>
                        </div>
                        <div class="card-footer text-center">
                            <a class="m-0 small text-primary card-link" href="#">View More <i
                                    class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--Row-->

    </div>
</template>


<script type="text/javascript">
import BarChart from './charts/BarChart.vue';


export default {
    components: { BarChart },
    created() {
        if (!User.loggedIn()) {
            this.$router.push({ name: '/' })
        }
    },
}
</script>


<style type="text/css"></style>
