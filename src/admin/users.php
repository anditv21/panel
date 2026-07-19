<?php
require_once "../app/require.php";
require_once "../app/controllers/AdminController.php";
require_once "../includes/head.nav.inc.php";

// Initialize controllers and session
$user = new UserController();
$admin = new AdminController();
Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}
// Get session username and user list
$username = Session::get("username");

$itemsPerPage = 15;
$currentPage = isset($_GET["page"]) ? (int) Util::securevar($_GET["page"]) : 1;
$search = isset($_GET["search"]) ? Util::securevar($_GET["search"]) : '';

if ($currentPage < 1) {
    $currentPage = 1;
}

$totalUsers = $admin->getUserCount($search);
$totalPages = max(1, ceil($totalUsers / $itemsPerPage));

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset = ($currentPage - 1) * $itemsPerPage;
$userList = $admin->getPaginatedUsers($offset, $itemsPerPage, $search);
$searchQuery = !empty($search) ? '&search=' . urlencode($search) : '';

// Security checks and page setup
Util::banCheck();
Util::checktoken();
Util::suppCheck();
Util::head("Admin Panel");

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $hwid = isset($_POST["resetHWID"]) ? Util::securevar($_POST["resetHWID"]) : null;
    $supp = isset($_POST["setsupp"]) ? Util::securevar($_POST["setsupp"]) : null;
    $ban = isset($_POST["setBanned"]) ? Util::securevar($_POST["setBanned"]) : null;
    $adminuser = isset($_POST["setAdmin"]) ? Util::securevar($_POST["setAdmin"]) : null;
    $mute = isset($_POST["setMute"]) ? Util::securevar($_POST["setMute"]) : null;

    if ($hwid) {
        Util::suppCheck();
        $admin->resetHWID($hwid);
    }

    if ($ban) {
        Util::adminCheck();
        $admin->setBanned($ban);
    }

    if ($supp) {
        Util::adminCheck();
        $admin->setsupp($supp);
    }

    if ($adminuser) {
        Util::adminCheck();
        $admin->setAdmin($adminuser);
    }

    if ($mute) {
        Util::suppCheck();
        $admin->setMute($mute);
    }

    header("location: users.php?page=$currentPage$searchQuery");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Users"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row">
               <div class="col">
                  <div class="card">
                     <div class="card-body">
                        <h5 class="card-title">User management</h5>
                        <p class="card-description"><code><?php Util::display($totalUsers); ?></code> user/s found.</p>
                        <form method="GET" action="users.php" class="d-flex mb-3">
                           <input type="text" name="search" class="form-control form-control-sm me-2" value="<?php Util::display($search); ?>" placeholder="UID, username, IP or HWID">
                           <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
                           <?php if (!empty($search)) : ?>
                              <a href="users.php" class="btn btn-outline-secondary btn-sm ms-2">Reset</a>
                           <?php endif; ?>
                        </form>
                        <table class="table table-hover">
                           <thead>
                              <tr>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1000">Picture</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1000">UID</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1200">Username</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1200">IP (Web)</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="1200">HWID</th>
                                 <th scope="col" data-aos="fade-left" data-aos-duration="2000">Actions</th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php if (empty($userList)) : ?>
                                 <tr>
                                    <td colspan="6" style="text-align: center;">No users found.</td>
                                 </tr>
                              <?php endif; ?>
                              <?php foreach ($userList as $row) : ?>
                                    <tr style="text-align: center;">
                                       <td scope="row" data-aos="fade-right" data-aos-duration="1000">
                                          <?php if (Util::getavatar($row->uid) == false) : ?>
                                             <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/images/avatars/Portrait_Placeholder.png" width="45" height="45">
                                          <?php else : ?>
                                             <?php
                                             $ext = pathinfo(Util::getavatardl($row->uid), PATHINFO_EXTENSION);
                                              $name = $row->uid . "." . $ext;
                                              ?>
                                             <a href="<?php Util::display(Util::getavatar($row->uid)); ?>" download="<?php Util::display($name); ?>">
                                                <img title="Click to download" data-toggle="tooltip" data-placement="top" class="rounded-circle img-profile" width="45" height="45" src="<?php Util::display(Util::getavatar($row->uid)); ?>">
                                             </a>
                                          <?php endif; ?>
                                       </td>
                                       <th scope="row" data-aos="fade" data-aos-duration="2000">
                                          <?php Util::display($row->uid); ?>
                                       </th>
                                       <td data-aos="fade" data-aos-duration="2000">
                                          <?php Util::display($row->username . " "); ?>
                                          <?php if ($row->admin == 1) : ?>
                                             <img title="Admin" data-toggle="tooltip" data-placement="top" src="../assets/images/admin.webp" width="15" height="15">
                                             <img title="Support" data-toggle="tooltip" data-placement="top" src="../assets/images/supp.webp" width="18" height="18">
                                          <?php elseif ($row->admin == 0 && $row->supp == 1) : ?>
                                             <img title="Support" data-toggle="tooltip" data-placement="top" src="../assets/images/supp.webp" width="18" height="18">
                                          <?php endif; ?>
                                          <?php if ($row->banned == 1) : ?>
                                             <img title="Banned" data-toggle="tooltip" data-placement="top" src="../assets/images/banned.webp" width="15" height="15">
                                          <?php endif; ?>
                                          <?php $days = $user->getSubStatus($row->username); ?>
                                          <?php if ($days > 0) : ?>
                                             <?php $days = Util::formatSubscriptionLabel($days, 'LT'); ?>
                                             <img title="Has <?php Util::display($days); ?> day/s sub left" data-toggle="tooltip" data-placement="top" src="../assets/images/sub.webp" width="15" height="15">
                                          <?php endif; ?>
                                       </td>
                                       <td style="text-align: center;">

                                          <?php Util::display("<p onclick=\"lookup('" . $row->lastIP . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->lastIP . "</p>"); ?>

                                       </td>
                                       <td style="text-align: center;">
                                          <p onclick="copyToClipboard('<?php Util::display($row->hwid); ?>')" title='Click to copy' data-toggle='tooltip' data-placement='top' class='spoiler'>
                                             <?php if ($row->hwid !== null) {
                                                 Util::display($row->hwid);
                                             } else {
                                                 Util::Display('No HWID found.');
                                             } ?>
                                          </p>
                                       </td>

                                       <td style="text-align: center;">
                                          <form method="POST" action="users.php?page=<?php echo $currentPage; ?><?php Util::display($searchQuery); ?>">
                                             <?php Util::csrfField(); ?>
                                             <button class="btn btn-warning" data-aos="fade-right" data-aos-duration="1000" value="<?php Util::display($row->uid); ?>" name="resetHWID" type="submit" id="reset-hwid">
                                                <i class="fas fa-microchip"></i> Reset HWID
                                             </button>
                                             <button class="btn btn-info" data-aos="fade-left" data-aos-duration="1000" value="<?php Util::display($row->uid); ?>" name="setAdmin" type="submit" id="admin">
                                                <i class="fas fa-user-shield"></i> set Admin
                                             </button>
                                             <button class="btn btn-info" data-aos="fade-left" data-aos-duration="1000" value="<?php Util::display($row->uid); ?>" name="setsupp" type="submit" id="admin">
                                                <i class="fas fa-info-circle"></i> set Support
                                             </button>
                                             <button class="btn btn-info" data-aos="fade-left" data-aos-duration="1000" value="<?php Util::display($row->uid); ?>" name="setMute" type="submit">
                                                <i class="fas fa-volume-mute"></i> <?php echo $row->muted ? 'Unmute' : 'Mute'; ?>
                                             </button>
                                             <a class="btn btn-info" data-aos="fade-left" data-aos-duration="1000" href='<?php Util::display(SITE_URL . SUB_DIR . '/user/viewprofile.php?uid=' .$row->uid); ?>' name="viewprofile">
                                                <i class="fas fa-info-circle"></i> View Profile
                                             </a>
                                          </form>
                                       </td>
                                    </tr>
                              <?php endforeach; ?>
                           </tbody>
                        </table>
                        <?php if ($totalPages > 1) : ?>
                           <nav aria-label="User pagination">
                              <ul class="pagination justify-content-center">
                                 <li class="page-item <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $currentPage - 1; ?><?php Util::display($searchQuery); ?>">&laquo;</a>
                                 </li>
                                 <?php
                                 $startPage = max(1, $currentPage - 2);
                                 $endPage = min($totalPages, $currentPage + 2);
                                 for ($i = $startPage; $i <= $endPage; $i++) :
                                     ?>
                                    <li class="page-item <?php echo $i == $currentPage ? 'active' : ''; ?>">
                                       <a class="page-link" href="?page=<?php echo $i; ?><?php Util::display($searchQuery); ?>"><?php echo $i; ?></a>
                                    </li>
                                 <?php endfor; ?>
                                 <li class="page-item <?php echo $currentPage >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $currentPage + 1; ?><?php Util::display($searchQuery); ?>">&raquo;</a>
                                 </li>
                              </ul>
                           </nav>
                        <?php endif; ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <script>
            function lookup(ip) {
               window.location.replace("<?php Util::display(SITE_URL . SUB_DIR . '/user/lookup.php?ip='); ?>" + ip);
            }
         </script>
</body>

</html>
