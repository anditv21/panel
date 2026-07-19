<?php
require_once '../app/require.php';
require_once("../includes/head.nav.inc.php");

$user = new UserController();

Session::init();
if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}

Util::banCheck();
Util::checktoken();

$itemsPerPage = 15;
$currentPage = isset($_GET['page']) ? (int) Util::securevar($_GET['page']) : 1;
$search = isset($_GET['search']) ? Util::securevar($_GET['search']) : '';

if ($currentPage < 1) {
    $currentPage = 1;
}

$totalUsers = $user->getUserCount($search);
$totalPages = max(1, ceil($totalUsers / $itemsPerPage));

if ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

$offset = ($currentPage - 1) * $itemsPerPage;
$userList = $user->getPaginatedUsers($offset, $itemsPerPage, $search);
$searchQuery = !empty($search) ? '&search=' . urlencode($search) : '';

Util::head("User list");
?>
<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar();?></head>
<?php display_top_nav("User list"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
    <div class="page-container">
        <div class="page-content">
            <div class="main-wrapper">
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">User list</h5>
                                <p class="card-description"><code><?php Util::display($totalUsers); ?></code> user/s found.</p>
                                <form method="GET" action="userlist.php" class="d-flex mb-3">
                                    <input type="text" name="search" class="form-control form-control-sm me-2" value="<?php Util::display($search); ?>" placeholder="UID, username, display name or inviter">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Search</button>
                                    <?php if (!empty($search)) : ?>
                                        <a href="userlist.php" class="btn btn-outline-secondary btn-sm ms-2">Reset</a>
                                    <?php endif; ?>
                                </form>
                                <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="text-align: center;">
                                            <th scope="col" data-aos="fade-left" data-aos-duration="1000">Picture</th>
                                            <th scope="col" data-aos="fade-left" data-aos-duration="1000">UID</th>
                                            <th scope="col" data-aos="fade-left" data-aos-duration="1200">Username</th>
                                            <th scope="col" data-aos="fade-left" data-aos-duration="1800">Inviter</th>
                                            <th scope="col" data-aos="fade-left" data-aos-duration="2000">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($userList)) : ?>
                                            <tr>
                                                <td colspan="5" class="text-center">No users found.</td>
                                            </tr>
                                        <?php endif; ?>
                                        <?php foreach ($userList as $row) : ?>
                                                <tr style="text-align: center;">
                                                    <td scope="row" data-aos="fade-right" data-aos-duration="1000">
                                                        <?php if (Util::getavatar($row->uid) == false) : ?>
                                                            <img title="Click to download" data-toggle="tooltip" data-placement="top" class="border rounded-circle img-profile" src="../assets/images/avatars/Portrait_Placeholder.png" width="45" height="45">
                                                        <?php else : ?>
                                                            <?php $ext = pathinfo(Util::getavatardl($row->uid), PATHINFO_EXTENSION);
                                                            $name = $row->uid . "." . $ext;
                                                            ?>
                                                            <a href="<?php Util::display(
                                                                Util::getavatar($row->uid)
                                                            ); ?>" download="<?php Util::display(
                                                                $name
                                                            ); ?>">
                                                                <img title="Click to download" data-toggle="tooltip" data-placement="top" class="rounded-circle img-profile" width="45" height="45" src="<?php Util::display(
                                                                    Util::getavatar($row->uid)
                                                                ); ?>"></a>
                                                        <?php endif; ?>
                                                    </td>
                                                    <th scope="row" data-aos="fade" data-aos-duration="2000"><?php Util::display($row->uid); ?></th>
                                                    <td data-aos="fade" data-aos-duration="2000"><?php Util::display($row->username . " "); ?>
                                                        <?php if ($row->admin == 1) : ?>
                                                            <img title="Admin" data-toggle="tooltip" data-placement="top" src="../assets/images/admin.webp" width="15" height="15">
                                                            <img title="Supporter" data-toggle="tooltip" data-placement="top" src="../assets/images/supp.webp" width="18" height="18">
                                                        <?php elseif ($row->admin == 0 && $row->supp == 1) : ?>
                                                            <img title="Supporter" data-toggle="tooltip" data-placement="top" src="../assets/images/supp.webp" width="18" height="18">
                                                        <?php endif; ?>


                                                        <?php if ($row->banned == 1) : ?>
                                                            <img title="Banned" data-toggle="tooltip" data-placement="top" src="../assets/images/banned.webp" width="15" height="15">
                                                        <?php endif; ?>

                                                        <?php if ($row->subscription_days > 0) : ?>
                                                            <img title="Has sub" data-toggle="tooltip" data-placement="top" src="../assets/images/sub.webp" width="15" height="15">
                                                        <?php endif; ?>
                                                    </td>
                                                    <td data-aos="fade" data-aos-duration="2000"><?php Util::display($row->invitedBy); ?></td>
                                                    <td>
                                                        <?php if (!empty($row->dcid)) : ?>
                                                            <a class="btn btn-primary" data-aos="fade-right" data-aos-duration="1000" href="<?php Util::display("https://discord.com/users/" . $row->dcid); ?>"><i class="fab fa-discord"></i> View Discord</a>
                                                        <?php endif; ?>
                                                        <a class="btn btn-danger" data-aos="fade-right" data-aos-duration="1000" href="<?php Util::display(SUB_DIR . "/user/viewprofile.php?uid=" . $row->uid); ?>"><i class="fas fa-user-circle"></i> View Profile</a>
                                                    </td>
                                                </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                </div>
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
        </div>
    </div>
</body>
<?php Util::footer(); ?>

</html>
