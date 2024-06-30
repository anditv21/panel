<?php

function display_top_nav($title)
{
    Util::display('<div class="page-header">
    <nav class="navbar navbar-expand-lg d-flex justify-content-between">
       <div class="header-title flex-fill">
          <a href="#" id="sidebar-toggle"><i data-feather="arrow-left"></i></a>
          <h5>' . $title . '</h5>
       </div>
       <div class="flex-fill" id="headerNav">
          <ul class="navbar-nav">
             <li class="nav-item d-md-block d-lg-none">
             </li>
             <li class="nav-item dropdown">
                <a class="nav-link profile-dropdown" href="#" id="profileDropDown" role="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="' . SUB_DIR . '/assets/images/guy.webp" alt=""></a>
                <div class="dropdown-menu dropdown-menu-end profile-drop-menu" aria-labelledby="profileDropDown">
                   <a class="dropdown-item" href="' . SUB_DIR . '/user/profile.php"><i data-feather="user"></i>Profile</a>
                   <div class="dropdown-divider"></div>
                   <a class="dropdown-item" href="' . SUB_DIR . '/auth/logout.php"><i data-feather="log-out"></i>Logout</a>
                </div>
             </li>
          </ul>
       </div>
    </nav>
 </div>');
}

?>
