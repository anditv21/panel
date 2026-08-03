<?php
require_once "../app/require.php";
require_once "../includes/head.nav.inc.php";
require_once "../app/controllers/AdminController.php";


$user = new UserController();
$admin = new AdminController();

Session::init();

if (!Session::isLogged()) {
    Util::redirect("/auth/login.php");
}

$uid = Session::get("uid");
$username = Session::get("username");

Util::banCheck();
Util::checktoken();
Util::adminCheck();

$itemsPerPage = 20;
$currentPage = isset($_GET["page"]) && is_scalar($_GET["page"]) ? (int) $_GET["page"] : 1;
$search = isset($_GET["search"]) && is_string($_GET["search"]) ? Util::securevar($_GET["search"]) : '';

if ($currentPage < 1) {
    $currentPage = 1;
}

$totalLogs = $admin->getAdminLogsCount($search);
$totalPages = max(1, (int) ceil($totalLogs / $itemsPerPage));

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset = ($currentPage - 1) * $itemsPerPage;
$logarray = $admin->getAdminLogsPaginated($offset, $itemsPerPage, $search);
$searchQuery = !empty($search) ? '&search=' . urlencode($search) : '';

Util::head("Admin Logs");

?>

<!DOCTYPE html>
<html lang="en">
<?php display_top_nav("Logs"); ?>

<head>
    <?php Util::navbar(); ?>
</head>

<body class="pace-done no-loader page-sidebar-collapsed">
    <div class="page-container">
        <div class="page-content">
            <div class="main-wrapper">
                <div class="row">
                    <div class="col-xl-12">
                        <br>
                        <form method="GET" action="logs.php" class="d-flex mb-3">
                            <input type="text" name="search" class="form-control form-control-sm me-2" value="<?php Util::display($search); ?>" placeholder="Username, action, IP or time">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
                            <?php if (!empty($search)) : ?>
                                <a href="logs.php" class="btn btn-outline-secondary btn-sm ms-2">Reset</a>
                            <?php endif; ?>
                        </form>

                        <p class="card-description"><code><?php Util::display($totalLogs); ?></code> log/s found.</p>

                        <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">Time</th>
                                    <th scope="col" class="text-center">Username</th>
                                    <th scope="col" class="text-center">Action</th>
                                    <th scope="col" class="text-center">IP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($logarray)) : ?>
                                    <tr>
                                        <td colspan="4" class="text-center">No logs found.</td>
                                    </tr>
                                <?php endif; ?>
                                <?php foreach ($logarray as $row) : ?>
                                    <tr style="text-align: center;">
                                        <td><?php Util::display($row->time); ?></td>
                                        <td><?php Util::display($row->username); ?></td>
                                        <td>
                                            <?php
                                            $action = $row->action;
                                    if (strpos($action, 'Generated an inv:') === 0) {
                                        Util::display('Generated an inv: <span class="spoiler">' . substr($action, strlen('Generated an inv:')) . '</span>');
                                    } else {
                                        Util::display($action);
                                    }
                                    ?>
                                        </td>
                                        <td style='text-align: center;'>
                                            <center>
                                                <?php Util::display("<p onclick=\"lookup('" . $row->ip . "')\" title='Click to lookup' data-toggle='tooltip' data-placement='top' class='spoiler'>" . $row->ip . "</p>"); ?>
                                            </center>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>

                        <?php if ($totalPages > 1) : ?>
                            <nav aria-label="Admin log pagination">
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
