<?php
require_once "../app/require.php";
require_once("../includes/head.nav.inc.php");


$user = new UserController();


Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$uid = Session::get("uid");
$username = Session::get("username");

$itemsPerPage = 15;
$currentPage = isset($_GET["page"]) ? (int) Util::securevar($_GET["page"]) : 1;
$search = isset($_GET["search"]) ? Util::securevar($_GET["search"]) : '';

if ($currentPage < 1) {
    $currentPage = 1;
}

$totalLogs = $user->getLogsCount($username, $search);
$totalPages = max(1, ceil($totalLogs / $itemsPerPage));

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset = ($currentPage - 1) * $itemsPerPage;
$logarray = $user->getLogsPaginated($username, $offset, $itemsPerPage, $search);
$searchQuery = !empty($search) ? '&search=' . urlencode($search) : '';

Util::banCheck();
Util::checktoken();
Util::head("Logs");


if (Util::securevar($_SERVER["REQUEST_METHOD"]) === "POST") {
    if (isset($_POST["password"])) {
        $submittedPassword = Util::securevar($_POST["password"]);
        $error = $user->flush($submittedPassword);
    }
    header('Location: log.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Account logs"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row">
               <div class="col-xl-12">
                  <div class="card">
                     <div class="card-body">
                        <form method="POST">
                           <button type="button" class="btn btn-outline-primary btn-block" onclick="openPasswordModal();" name="flush">Flush all logs</button>
                        </form>
                     </div>
                  </div>
                  <br>

                  <form method="GET" action="log.php" class="d-flex mb-3">
                     <input type="text" name="search" class="form-control form-control-sm me-2" value="<?php Util::display($search); ?>" placeholder="Action, browser, OS or IP">
                     <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
                     <?php if (!empty($search)) : ?>
                        <a href="log.php" class="btn btn-outline-secondary btn-sm ms-2">Reset</a>
                     <?php endif; ?>
                  </form>

                  <p class="card-description"><code><?php Util::display($totalLogs); ?></code> log/s found.</p>

                  <table class="table table-bordered">
                     <thead>
                        <tr>
                           <th scope="col" class="text-center">Time</th>
                           <th scope="col" class="text-center">Action</th>
                           <th scope="col" class="text-center">Browser</th>
                           <th scope="col" class="text-center">OS</th>
                           <th scope="col" class="text-center">IP</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php if (empty($logarray)) : ?>
                           <tr>
                              <td colspan="5" class="text-center">No logs found.</td>
                           </tr>
                        <?php endif; ?>
                        <?php foreach ($logarray as $row) : ?>
                           <tr style="text-align: center;">
                              <td><?php Util::display($row->time); ?></td>
                              <td><?php Util::display($row->action); ?></td>
                              <td><?php Util::display($row->browser); ?></td>
                              <td><?php Util::display($row->os); ?></td>
                              <td>
                              <?php Util::display("<em onclick=\"lookup('" . $row->ip . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->ip . "</em>"); ?>
                              </td>
                           </tr>
                        <?php endforeach; ?>
                     </tbody>
                  </table>

                  <?php if ($totalPages > 1) : ?>
                     <nav aria-label="Log pagination">
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

                  <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel" aria-hidden="true">
                     <div class="modal-dialog">
                        <div class="modal-content">
                           <div class="modal-header">
                              <h5 class="modal-title" id="passwordModalLabel">Enter Password to Flush Logs</h5>
                           </div>
                           <div class="modal-body">
                              <form method="POST" id="flushForm">
                                 <div class="form-group">
                                    <label style="color: #86c1ed;" for="password">Password:</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                 </div>
                              </form>
                           </div>
                           <div class="modal-footer">
                              <button type="submit" form="flushForm" class="btn btn-outline-primary btn-block">Submit</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <script>
                     // Function to open the Bootstrap modal dialog
                     function openPasswordModal() {
                        $('#passwordModal').modal('show');
                     }
                  </script>
                  <script>
                     function lookup(ip) {
                        window.location.replace("<?php echo Util::display(SITE_URL . SUB_DIR . '/user/lookup.php?ip='); ?>" + ip);
                     }
                  </script>
                  <br>
               </div>
            </div>
         </div>
      </div>
</body>

</html>
