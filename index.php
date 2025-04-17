<?php
session_start();
require_once 'functions.php';
if (!isset($_SESSION['user_id'])) {
   
    echo 'Purvo trq lognesh tarikat';

  // header('Location: login.php');
    exit;
}

$user = getUserProfile($_SESSION['user_id']);

// You might want to check if $user is null and handle that case accordingly
include 'components/header.php'; 
echo getHeader('Dashboard', 'dashboard');
if ($user === null) {
    echo 'Purvo trq lognesh tarikat';
    exit;
}
?>
    <!-- start page content wrapper-->
    <div class="page-content-wrapper">
      <!-- start page content-->
      <div class="page-content">

        <!--start breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
          <div class="breadcrumb-title pe-3">Dashboard</div>
          <div class="ps-3">
            <nav aria-label="breadcrumb">
              <ol class="breadcrumb mb-0 p-0 align-items-center">
                <li class="breadcrumb-item"><a href="javascript:;">
                    <ion-icon name="home-outline"></ion-icon>
                  </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">eCommerce</li>
              </ol>
            </nav>
          </div>
          <div class="ms-auto">
            <div class="btn-group">
              <button type="button" class="btn btn-outline-primary">Settings</button>
              <button type="button"
                class="btn btn-outline-primary split-bg-primary dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown"> <span class="visually-hidden">Toggle Dropdown</span>
              </button>
              <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg-end"> <a class="dropdown-item"
                  href="javascript:;">Action</a>
                <a class="dropdown-item" href="javascript:;">Another action</a>
                <a class="dropdown-item" href="javascript:;">Something else here</a>
                <div class="dropdown-divider"></div> <a class="dropdown-item" href="javascript:;">Separated link</a>
              </div>
            </div>
          </div>
        </div>
        <!--end breadcrumb-->


        <div class="row row-cols-1 row-cols-lg-2 row-cols-xxl-4">
          <div class="col">
            <div class="card radius-10">
              <div class="card-body">
                <div class="d-flex align-items-start gap-2">
                  <div>
                    <p class="mb-0 fs-6">Total Revenue</p>
                  </div>
                  <div class="ms-auto widget-icon-small text-white bg-gradient-purple">
                    <ion-icon name="wallet-outline"></ion-icon>
                  </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                  <div>
                    <h4 class="mb-0">$92,854</h4>
                  </div>
                  <div class="ms-auto">+6.32%</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card radius-10">
              <div class="card-body">
                <div class="d-flex align-items-start gap-2">
                  <div>
                    <p class="mb-0 fs-6">Total Customer</p>
                  </div>
                  <div class="ms-auto widget-icon-small text-white bg-gradient-info">
                    <ion-icon name="people-outline"></ion-icon>
                  </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                  <div>
                    <h4 class="mb-0">48,789</h4>
                  </div>
                  <div class="ms-auto">+12.45%</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card radius-10">
              <div class="card-body">
                <div class="d-flex align-items-start gap-2">
                  <div>
                    <p class="mb-0 fs-6">Total Orders</p>
                  </div>
                  <div class="ms-auto widget-icon-small text-white bg-gradient-danger">
                    <ion-icon name="bag-handle-outline"></ion-icon>
                  </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                  <div>
                    <h4 class="mb-0">88,234</h4>
                  </div>
                  <div class="ms-auto">+3.12%</div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card radius-10">
              <div class="card-body">
                <div class="d-flex align-items-start gap-2">
                  <div>
                    <p class="mb-0 fs-6">Conversion Rate</p>
                  </div>
                  <div class="ms-auto widget-icon-small text-white bg-gradient-success">
                    <ion-icon name="bar-chart-outline"></ion-icon>
                  </div>
                </div>
                <div class="d-flex align-items-center mt-3">
                  <div>
                    <h4 class="mb-0">48.76%</h4>
                  </div>
                  <div class="ms-auto">+8.52%</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--end row-->


        <div class="row row-cols-1 row-cols-lg-3">
          <div class="col">
            <div class="card radius-10 w-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <h6 class="mb-0">Sales by Countries</h6>
                  <div class="dropdown options ms-auto">
                    <div class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                      <ion-icon name="ellipsis-horizontal-outline" class="md hydrated"></ion-icon>
                    </div>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Action</a></li>
                      <li><a class="dropdown-item" href="#">Another action</a></li>
                      <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                  </div>
                </div>
                <div class="countries-list">
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="country-icon">
                      <img src="assets/images/icons/usa.png" alt="" width="35">
                    </div>
                    <div class="country-name flex-grow-1">
                      <h5 class="mb-0">$84.5K</h5>
                      <p class="mb-0 text-secondary">United states</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-success d-flex gap-1 align-items-center fw-500"><i class='bx bx-up-arrow-alt'></i><span>25%</span></p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="country-icon">
                      <img src="assets/images/icons/india.png" alt="" width="35">
                    </div>
                    <div class="country-name flex-grow-1">
                      <h5 class="mb-0">$750</h5>
                      <p class="mb-0 text-secondary">India</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-success d-flex gap-1 align-items-center fw-500"><i class='bx bx-up-arrow-alt'></i><span>18%</span></p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="country-icon">
                      <img src="assets/images/icons/china.png" alt="" width="35">
                    </div>
                    <div class="country-name flex-grow-1">
                      <h5 class="mb-0">$38.5</h5>
                      <p class="mb-0 text-secondary">China</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-danger d-flex gap-1 align-items-center fw-500"><i class='bx bx-down-arrow-alt'></i><span>14%</span></p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="country-icon">
                      <img src="assets/images/icons/russia.png" alt="" width="35">
                    </div>
                    <div class="country-name flex-grow-1">
                      <h5 class="mb-0">$88.0K</h5>
                      <p class="mb-0 text-secondary">France</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-success d-flex gap-1 align-items-center fw-500"><i class='bx bx-up-arrow-alt'></i><span>28%</span></p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="country-icon">
                      <img src="assets/images/icons/australia.png" alt="" width="35">
                    </div>
                    <div class="country-name flex-grow-1">
                      <h5 class="mb-0">$78.3K</h5>
                      <p class="mb-0 text-secondary">Australia</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-danger d-flex gap-1 align-items-center fw-500"><i class='bx bx-down-arrow-alt'></i><span>16%</span></p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="country-icon">
                      <img src="assets/images/icons/brazil.png" alt="" width="35">
                    </div>
                    <div class="country-name flex-grow-1">
                      <h5 class="mb-0">$10.5K</h5>
                      <p class="mb-0 text-secondary">Brazil</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-success d-flex gap-1 align-items-center fw-500"><i class='bx bx-up-arrow-alt'></i><span>25%</span></p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3 mb-0">
                    <div class="country-icon">
                      <img src="assets/images/icons/UAE.png" alt="" width="35">
                    </div>
                    <div class="country-name flex-grow-1">
                      <h5 class="mb-0">$30.5K</h5>
                      <p class="mb-0 text-secondary">UAE</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-success d-flex gap-1 align-items-center fw-500"><i class='bx bx-up-arrow-alt'></i><span>25%</span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card radius-10">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <h6 class="mb-0">Total Earning</h6>
                  <div class="dropdown options ms-auto">
                    <div class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                      <ion-icon name="ellipsis-horizontal-outline" class="md hydrated"></ion-icon>
                    </div>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Action</a></li>
                      <li><a class="dropdown-item" href="#">Another action</a></li>
                      <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                  </div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                  <h2 class="mb-0">68%</h2>
                  <div class="">
                    <p class="mb-0 text-success d-flex gap-1 align-items-center fw-500 fs-6"><i class='bx bx-up-arrow-alt'></i><span>25%</span></p>
                  </div>
                </div>
                <div id="chart1"></div>
                <div class="mt-4">
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="widget-icon-small rounded bg-light-success text-success">
                      <ion-icon name="wallet-outline"></ion-icon>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0">$545.69</h6>
                      <p class="mb-0 text-secondary">Last Month Sales</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-success d-flex gap-1 align-items-center fw-500 fs-6"><i class='bx bx-up-arrow-alt'></i><span>35%</span></p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="widget-icon-small rounded bg-light-tiffany text-tiffany">
                      <ion-icon name="flag-outline"></ion-icon>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0">$956.34</h6>
                      <p class="mb-0 text-secondary">Last Month Sales</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-danger d-flex gap-1 align-items-center fw-500 fs-6"><i class='bx bx-up-arrow-alt'></i><span>45%</span></p>
                    </div>
                  </div>
                  <div class="d-flex align-items-center gap-3">
                    <div class="widget-icon-small rounded bg-light-danger text-danger">
                      <ion-icon name="school-outline"></ion-icon>
                    </div>
                    <div class="flex-grow-1">
                      <h6 class="mb-0">$6956.48</h6>
                      <p class="mb-0 text-secondary">Last Year Sales</p>
                    </div>
                    <div class="">
                      <p class="mb-0 text-success d-flex gap-1 align-items-center fw-500 fs-6"><i class='bx bx-up-arrow-alt'></i><span>66%</span></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="card radius-10 overflow-hidden w-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <h6 class="mb-0">Total Traffic</h6>
                  <div class="dropdown options ms-auto">
                    <div class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                      <ion-icon name="ellipsis-horizontal-outline"></ion-icon>
                    </div>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                </div>
                <div class="d-flex align-items-center font-13 gap-2">
                  <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1 text-tiffany"></i>Cliks</span>
                  <span class="border px-1 rounded cursor-pointer"><i class="bx bxs-circle me-1 text-success"></i>Views</span>
                </div>
                <div id="chart2"></div>
              </div>
            </div>
          </div>
        </div><!--end row-->


        <div class="row">
          <div class="col-12 col-xl-8 d-flex">
            <div class="card radius-10 w-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <h6 class="mb-0">Earning Reports</h6>
                  <div class="dropdown options ms-auto">
                    <div class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                      <ion-icon name="ellipsis-horizontal-outline"></ion-icon>
                    </div>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                </div>
               <div class="row g-4 align-items-center mb-4">
                  <div class="col-12 col-xl-4">
                     <div class="d-flex align-items-center gap-2 mb-3">
                         <h1 class="mb-0">$856</h1>
                         <p class="mb-0 text-success bg-light-success px-2 rounded py-1">+10.6%</p>
                     </div>
                     <p class="mb-0">In publishing and graphic design, Lorem ipsum is a placeholder text commonly used to demonstrate.</p>
                  </div>
                  <div class="col-12 col-xl-8">
                    <div id="chart3"></div>
                  </div>
               </div><!--end row-->
               <div class="d-flex flex-column flex-lg-row align-items-lg-center align-self-end justify-content-lg-between border p-3 gap-3 mb-0 rounded-3">
                <div class="d-flex align-items-center gap-3">
                  <div class="widget-icon rounded-circle bg-light-success text-success">
                    <ion-icon name="card-outline"></ion-icon>
                  </div>
                  <div class="">
                    <h4 class="mb-0">$95,286.50</h4>
                    <p class="mb-0 text-secondary">Total Revenue</p>
                  </div>
                </div>
                <div class="vr d-none d-lg-block"></div>
                <div class="d-flex align-items-center gap-3">
                  <div class="widget-icon rounded-circle bg-light-info text-info">
                    <ion-icon name="diamond-outline"></ion-icon>
                  </div>
                  <div class="">
                    <h4 class="mb-0">$58,820</h4>
                    <p class="mb-0 text-secondary">Total Profit</p>
                  </div>
                </div>
                <div class="vr d-none d-lg-block"></div>
                <div class="d-flex align-items-center gap-3">
                  <div class="widget-icon rounded-circle bg-light-purple text-purple">
                    <ion-icon name="people-circle-outline"></ion-icon>
                  </div>
                  <div class="">
                    <h4 class="mb-0">$26,498</h4>
                    <p class="mb-0 text-secondary">Total Customer</p>
                  </div>
                </div>
              </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-xl-4 d-flex">
            <div class="card radius-10 overflow-hidden w-100">
              <div class="card-body">
                <div class="d-flex flex-column gap-3">
                  <div class="card border shadow-none radius-10 flex-grow-1 mb-0">
                    <div class="card-body">
                      <div class="d-flex align-items-start gap-2">
                        <div>
                          <h5 class="mb-0 ">Total Accounts</h5>
                        </div>
                        <div class="ms-auto widget-icon-2 text-white bg-info rounded-circle">
                          <ion-icon name="people-outline"></ion-icon>
                        </div>
                      </div>
                      <div class="">
                        <h3 class="mb-2">68,542</h3>
                        <div class="d-flex align-items-center gap-2">
                           <div class="widget-icon-small bg-light-danger text-danger">
                            <ion-icon name="arrow-down-outline"></ion-icon>
                           </div>
                           <p class="mb-0">+9% last year</p>
                        </div>
                      </div>
                      <div id="chart4"></div>
                    </div>
                  </div>
                  <div class="card border shadow-none radius-10 mb-0">
                    <div class="card-body">
                      <div class="d-flex align-items-start gap-2">
                        <div><p class="mb-0">Disk Space</p></div>
                        <div class="dropdown options ms-auto">
                          <div class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                            <ion-icon name="ellipsis-horizontal-outline"></ion-icon>
                          </div>
                          <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                            <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                          </ul>
                        </div>
                      </div>
                      <div class="d-flex align-items-center mt-3">
                       <div>
                         <h4 class="mb-3">48GB</h4>
                         <div class="d-flex align-items-center gap-2">
                          <div class="widget-icon-small bg-light-danger text-danger">
                           <ion-icon name="arrow-down-outline"></ion-icon>
                          </div>
                          <p class="mb-0">+7% last month</p>
                       </div>
                       </div>
                       <div class="ms-auto">
                         <div class="w_chart" id="chart5" data-percent="60">
                           <span class="w_percent"></span>
                         </div>
                       </div>
                      </div>
                    </div>
                   </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--end row-->

        <div class="row">
          <div class="col-12 col-lg-12 col-xl-6 d-flex">
            <div class="card radius-10 w-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <h6 class="mb-0">Customers</h6>
                  <div class="dropdown options ms-auto">
                    <div class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                      <ion-icon name="ellipsis-horizontal-outline"></ion-icon>
                    </div>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                      <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                  </div>
                </div>
                <div class="row row-cols-1 row-cols-md-2 g-3 align-items-center">
                  <div class="col-lg-7 col-xl-7 col-xxl-7 order-2">
                    <div id="chart6"></div>
                  </div>
                  <div class="col-lg-5 col-xl-5 col-xxl-5">
                    <div class="">
                       <div class="mb-4">
                         <h2 class="mb-0">846</h2>
                         <p class="mb-0">Total Customers</p>
                       </div>
                      <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="widget-icon-small rounded bg-light-purple text-purple">
                          <ion-icon name="gift-outline"></ion-icon>
                        </div>
                        <div>
                          <p class="mb-1">Current Customers</p>
                          <p class="mb-0 h5">124</p>
                        </div>
                      </div>
                      <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="widget-icon-small rounded bg-light-info text-info">
                          <ion-icon name="briefcase-outline"></ion-icon>
                        </div>
                        <div>
                          <p class="mb-1">New Customers</p>
                          <p class="mb-0 h5">386</p>
                        </div>
                      </div>
                      <div class="d-flex align-items-start gap-3">
                        <div class="widget-icon-small rounded bg-light-orange text-orange">
                          <ion-icon name="book-outline"></ion-icon>
                        </div>
                        <div>
                          <p class="mb-1">Retargeted Customers</p>
                          <p class="mb-0 h5">425</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-12 col-xl-6 d-flex">
            <div class="card radius-10 w-100">
              <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                  <h6 class="mb-0">Sales By Country</h6>
                  <div class="dropdown options ms-auto">
                    <div class="dropdown-toggle dropdown-toggle-nocaret" data-bs-toggle="dropdown">
                      <ion-icon name="ellipsis-horizontal-outline" role="img" class="md hydrated"
                        aria-label="ellipsis horizontal outline"></ion-icon>
                    </div>
                    <ul class="dropdown-menu">
                      <li><a class="dropdown-item" href="#">Action</a></li>
                      <li><a class="dropdown-item" href="#">Another action</a></li>
                      <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-borderless align-middle mb-0">
                    <tbody>
                      <tr>
                        <td>
                          <div class="country-icon">
                            <img src="assets/images/icons/india.png" alt="" width="32">
                          </div>
                        </td>
                        <td>
                          <div class="country-name h6 mb-0">India</div>
                        </td>
                        <td class="w-100">
                          <div class="progress flex-grow-1" style="height: 5px;">
                            <div class="progress-bar bg-gradient-info" role="progressbar" style="width: 82%;"></div>
                          </div>
                        </td>
                        <td>
                          <div class="percent-data">82%</div>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <div class="country-icon">
                            <img src="assets/images/icons/usa.png" alt="" width="32">
                          </div>
                        </td>
                        <td>
                          <div class="country-name h6 mb-0">USA</div>
                        </td>
                        <td class="w-100">
                          <div class="progress flex-grow-1" style="height: 5px;">
                            <div class="progress-bar bg-gradient-purple" role="progressbar" style="width: 70%;"></div>
                          </div>
                        </td>
                        <td>
                          <div class="percent-data">70%</div>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <div class="country-icon">
                            <img src="assets/images/icons/china.png" alt="" width="32">
                          </div>
                        </td>
                        <td>
                          <div class="country-name h6 mb-0">China</div>
                        </td>
                        <td class="w-100">
                          <div class="progress flex-grow-1" style="height: 5px;">
                            <div class="progress-bar bg-gradient-success" role="progressbar" style="width: 60%;"></div>
                          </div>
                        </td>
                        <td>
                          <div class="percent-data">60%</div>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <div class="country-icon">
                            <img src="assets/images/icons/russia.png" alt="" width="32">
                          </div>
                        </td>
                        <td>
                          <div class="country-name h6 mb-0">Russia</div>
                        </td>
                        <td class="w-100">
                          <div class="progress flex-grow-1" style="height: 5px;">
                            <div class="progress-bar bg-gradient-warning" role="progressbar" style="width: 45%;"></div>
                          </div>
                        </td>
                        <td>
                          <div class="percent-data">45%</div>
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <div class="country-icon">
                            <img src="assets/images/icons/russia.png" alt="" width="32">
                          </div>
                        </td>
                        <td>
                          <div class="country-name h6 mb-0">Russia</div>
                        </td>
                        <td class="w-100">
                          <div class="progress flex-grow-1" style="height: 5px;">
                            <div class="progress-bar bg-gradient-danger" role="progressbar" style="width: 30%;"></div>
                          </div>
                        </td>
                        <td>
                          <div class="percent-data">30%</div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!--end row-->




      </div>
      <!-- end page content-->
    </div>
    <!--end page content wrapper-->


    <!--start footer-->
    <footer class="footer">
      <div class="footer-text">
        Copyright © 2023. All right reserved.
      </div>
    </footer>
    <!--end footer-->


    <!--Start Back To Top Button-->
    <a href="javaScript:;" class="back-to-top">
      <ion-icon name="arrow-up-outline"></ion-icon>
    </a>
    <!--End Back To Top Button-->

    <!--start switcher-->
    <div class="switcher-body">
      <button class="btn btn-primary btn-switcher shadow-sm" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#offcanvasScrolling" aria-controls="offcanvasScrolling">
        <ion-icon name="color-palette-outline" class="me-0"></ion-icon>
      </button>
      <div class="offcanvas offcanvas-end shadow border-start-0 p-2" data-bs-scroll="true" data-bs-backdrop="false"
        tabindex="-1" id="offcanvasScrolling">
        <div class="offcanvas-header border-bottom">
          <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Theme Customizer</h5>
          <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
          <h6 class="mb-0">Theme Variation</h6>
          <hr>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="LightTheme" value="option1" checked>
            <label class="form-check-label" for="LightTheme">Light</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="DarkTheme" value="option2">
            <label class="form-check-label" for="DarkTheme">Dark</label>
          </div>
          <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="inlineRadioOptions" id="SemiDark" value="option3">
            <label class="form-check-label" for="SemiDark">Semi Dark</label>
          </div>
          <hr />
          <h6 class="mb-0">Header Colors</h6>
          <hr />
          <div class="header-colors-indigators">
            <div class="row row-cols-auto g-3">
              <div class="col">
                <div class="indigator headercolor1" id="headercolor1"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor2" id="headercolor2"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor3" id="headercolor3"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor4" id="headercolor4"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor5" id="headercolor5"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor6" id="headercolor6"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor7" id="headercolor7"></div>
              </div>
              <div class="col">
                <div class="indigator headercolor8" id="headercolor8"></div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
    <!--end switcher-->


    <!--start overlay-->
    <div class="overlay nav-toggle-icon"></div>
    <!--end overlay-->

  </div>
  <!--end wrapper-->


  <!-- JS Files-->
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
  <script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
  <script src="assets/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <!--plugins-->
  <script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
  <script src="assets/plugins/apexcharts-bundle/js/apexcharts.min.js"></script>
  <script src="assets/plugins/easyPieChart/jquery.easypiechart.js"></script>
  <script src="assets/plugins/chartjs/chart.min.js"></script>
  <script src="assets/js/index.js"></script>
  <!-- Main JS-->
  <script src="assets/js/main.js"></script>


</body>

</html>