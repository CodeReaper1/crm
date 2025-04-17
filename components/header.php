<?php
// Make sure we have access to functions
if (!function_exists('enqueue_crm_assets')) {
    require_once __DIR__ . '/../functions.php';
}

// Enqueue all CRM assets

function getHeader($title = 'APEX CRM - CRM built by legends for legends', $active_menu){
    enqueue_crm_assets();

    $title = $title ?? 'APEX CRM - CRM built by legends for legends';
    ob_start();
?>
<!DOCTYPE HTML>
<html lang="en" class="dark-theme">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS Files -->
    <?php print_styles(); ?>

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>

    <?php print_head_scripts(); ?>

  <title><?php echo $title; ?></title>
</head>

<body>
  <!--start wrapper-->
  <div class="wrapper">

    <!--start sidebar -->
    <aside class="sidebar-wrapper" data-simplebar="true">
      <div class="sidebar-header">
        <div>
          <img src="<?php echo ASSET_URL; ?>images/logo-icon-2.png" class="logo-icon" alt="logo icon" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjMDA3YmZmIiAvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjI0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBhbGlnbm1lbnQtYmFzZWxpbmU9Im1pZGRsZSIgZmlsbD0id2hpdGUiPkE8L3RleHQ+PC9zdmc+'">
        </div>
        <div>
          <h4 class="logo-text">Apex CRM</h4>
        </div>
      </div>
      <!--navigation-->
      <ul class="metismenu" id="menu">
        <li>
          <a href="index.php" class="<?php echo $active_menu == 'dashboard' ? 'active' : ''; ?>">
            <div class="parent-icon">
              <ion-icon name="home-outline"></ion-icon>
            </div>
            <div class="menu-title">Dashboard</div>
          </a>
        </li>
        <li>
          <a href="profile.php" class="<?php echo $active_menu == 'profile' ? 'active' : ''; ?>">
            <div class="parent-icon">
              <ion-icon name="person-circle-outline"></ion-icon>
            </div>
            <div class="menu-title">User Profile</div>
          </a>
        </li>
        <li class="menu-label">Leads</li>
        <li>
          <a href="page-call-stack.php" class="<?php echo $active_menu == 'stack' ? 'active' : ''; ?>">
            <div class="parent-icon">
              <ion-icon name="server-outline"></ion-icon>
            </div>
            <div class="menu-title">Call Stack</div>
          </a>
        </li>
        <li>
          <a href="page-cold-leads.php" class="<?php echo $active_menu == 'cold' ? 'active' : ''; ?>">
            <div class="parent-icon">
              <ion-icon name="snow-outline"></ion-icon>
            </div>
            <div class="menu-title">Cold Leads</div>
          </a>
        </li>
        <li>
          <a href="page-warm-leads.php" class="<?php echo $active_menu == 'warm' ? 'active' : ''; ?>">
            <div class="parent-icon">
              <ion-icon name="sunny-outline"></ion-icon>
            </div>
            <div class="menu-title">Warm Leads</div>
          </a>
        </li>
        <li>
          <a href="currently-working-with.php" class="<?php echo $active_menu == 'customer' ? 'active' : ''; ?>">
            <div class="parent-icon">
              <ion-icon name="briefcase-outline"></ion-icon>
            </div>
            <div class="menu-title">Currently Working With</div>
          </a>
        </li>
        <li>
          <a href="bank.php" class="<?php echo $active_menu == 'bank' ? 'active' : ''; ?>">
            <div class="parent-icon">
              <ion-icon name="cash-outline"></ion-icon>
            </div>
            <div class="menu-title">Bank</div>
          </a>
        </li>
        <li class="menu-label">Developer</li>
        <li>
          <a href="dev-tools.php" class="<?php echo $active_menu == 'dev-tools' ? 'active' : ''; ?>">
            <div class="parent-icon">
              <ion-icon name="code-slash-outline"></ion-icon>
            </div>
            <div class="menu-title">Developer Tools</div>
          </a>
        </li>
      </ul>
      <!--end navigation-->
    </aside>
    <!--end sidebar -->

    <!--start top header-->
    <header class="top-header">
      <nav class="navbar navbar-expand gap-3">
        <div class="toggle-icon">
          <ion-icon name="menu-outline"></ion-icon>
        </div>

        <form class="searchbar">
          <div class="position-absolute top-50 translate-middle-y search-icon ms-3">
            <ion-icon name="search-outline"></ion-icon>
          </div>
          <input class="form-control" type="text" placeholder="Search for anything">
          <div class="position-absolute top-50 translate-middle-y search-close-icon">
            <ion-icon name="close-outline"></ion-icon>
          </div>
        </form>
        <div class="top-navbar-right ms-auto">
          <ul class="navbar-nav align-items-center">
            <li class="nav-item dropdown dropdown-user-setting">
              <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
                <div class="user-setting">
                  <img src="<?php echo ASSET_URL; ?>images/logo-tryout1.png" class="user-img" alt="" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjUiIGN5PSIyNSIgcj0iMjUiIGZpbGw9IiMwMDdiZmYiIC8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMjQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGFsaWdubWVudC1iYXNlbGluZT0ibWlkZGxlIiBmaWxsPSJ3aGl0ZSI+QTwvdGV4dD48L3N2Zz4='">
                </div>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="javascript:;">
                    <div class="d-flex flex-row align-items-center gap-2">
                      <img src="<?php echo ASSET_URL; ?>images/logo-tryout1.png" alt="" class="rounded-circle" width="54" height="54" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTQiIGhlaWdodD0iNTQiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMjciIGN5PSIyNyIgcj0iMjciIGZpbGw9IiMwMDdiZmYiIC8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMjQiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGFsaWdubWVudC1iYXNlbGluZT0ibWlkZGxlIiBmaWxsPSJ3aGl0ZSI+QTwvdGV4dD48L3N2Zz4='">
                      <div class="">
                        <h6 class="mb-0 dropdown-user-name">Jhon Deo</h6>
                        <small class="mb-0 dropdown-user-designation text-secondary">UI Developer</small>
                      </div>
                    </div>
                  </a>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li>
                  <a class="dropdown-item" href="profile.php">
                    <div class="d-flex align-items-center">
                      <div class="">
                        <ion-icon name="person-outline"></ion-icon>
                      </div>
                      <div class="ms-3"><span>Profile</span></div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="javascript:;">
                    <div class="d-flex align-items-center">
                      <div class="">
                        <ion-icon name="settings-outline"></ion-icon>
                      </div>
                      <div class="ms-3"><span>Setting</span></div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="index.php">
                    <div class="d-flex align-items-center">
                      <div class="">
                        <ion-icon name="speedometer-outline"></ion-icon>
                      </div>
                      <div class="ms-3"><span>Dashboard</span></div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="javascript:;">
                    <div class="d-flex align-items-center">
                      <div class="">
                        <ion-icon name="wallet-outline"></ion-icon>
                      </div>
                      <div class="ms-3"><span>Earnings</span></div>
                    </div>
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="javascript:;">
                    <div class="d-flex align-items-center">
                      <div class="">
                        <ion-icon name="cloud-download-outline"></ion-icon>
                      </div>
                      <div class="ms-3"><span>Downloads</span></div>
                    </div>
                  </a>
                </li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li>
                  <a class="dropdown-item" href="logout.php">
                    <div class="d-flex align-items-center">
                      <div class="">
                        <ion-icon name="log-out-outline"></ion-icon>
                      </div>
                      <div class="ms-3"><span>Logout</span></div>
                    </div>
                  </a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <!--end top header-->
    <?php
    return ob_get_clean();
  }
?>
