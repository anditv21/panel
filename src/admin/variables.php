<?php

require_once '../app/require.php';
require_once '../includes/head.nav.inc.php';
require_once '../app/controllers/AdminController.php';

$admin = new AdminController();
Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}

Util::banCheck();
Util::checktoken();
Util::adminCheck();
Util::head('Variables');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $error = false;

    if (isset($_POST['createVariable'])) {
        $name = Util::securevar($_POST['name'] ?? '');
        $content = Util::securevar($_POST['content'] ?? '');
        $error = $admin->createVariable($name, $content);
    } elseif (isset($_POST['editVariable'])) {
        $id = (int) ($_POST['id'] ?? 0);
        $name = Util::securevar($_POST['name'] ?? '');
        $content = Util::securevar($_POST['content'] ?? '');
        $error = $admin->editVariable($id, $name, $content);
    } elseif (isset($_POST['deleteVariable'])) {
        $id = (int) $_POST['deleteVariable'];
        $error = $admin->deleteVariable($id);
    }

    if ($error) {
        header('location: variables.php?' . http_build_query(['alert' => $error, 'type' => 'danger']));
    } else {
        header('location: variables.php?' . http_build_query(['alert' => 'Variable saved.', 'type' => 'success']));
    }
    exit;
}

$variables = $admin->getVariables();
?>

<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav('Variables'); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
    <div class="page-container">
        <div class="page-content">
            <div class="main-wrapper">
                <div class="row">
                    <div class="col-lg-12">
                        <?php if (isset($_GET['alert'], $_GET['type'])) : ?>
                            <div class="alert alert-<?php Util::display(Util::securevar($_GET['type'])); ?>">
                                <?php Util::display(Util::securevar($_GET['alert'])); ?>
                            </div>
                        <?php endif; ?>

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Create variable</h5>
                                <p class="card-description">Variables are added to successful API responses.</p>
                                <form method="POST" action="<?php Util::display(Util::securevar($_SERVER['PHP_SELF'])); ?>">
                                    <?php Util::csrfField(); ?>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="name" maxlength="64" placeholder="Name" required>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="content" maxlength="255" placeholder="Value" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="submit" name="createVariable" class="btn btn-success w-100">Create</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Variables</h5>
                                <?php if (empty($variables)) : ?>
                                    <div class="alert alert-info">No variables found.</div>
                                <?php else : ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Value</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($variables as $variable) : ?>
                                                    <tr>
                                                        <td>
                                                            <input form="variable-<?php Util::display((int) $variable->id); ?>" type="text" class="form-control" name="name" maxlength="64" value="<?php Util::display($variable->name); ?>" required>
                                                        </td>
                                                        <td>
                                                            <input form="variable-<?php Util::display((int) $variable->id); ?>" type="text" class="form-control" name="content" maxlength="255" value="<?php Util::display($variable->content); ?>" required>
                                                        </td>
                                                        <td class="text-center">
                                                            <form id="variable-<?php Util::display((int) $variable->id); ?>" method="POST" action="<?php Util::display(Util::securevar($_SERVER['PHP_SELF'])); ?>">
                                                                <?php Util::csrfField(); ?>
                                                                <input type="hidden" name="id" value="<?php Util::display((int) $variable->id); ?>">
                                                                <button type="submit" name="editVariable" class="btn btn-outline-primary btn-sm">Save</button>
                                                                <button type="submit" name="deleteVariable" value="<?php Util::display((int) $variable->id); ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this variable?')">Delete</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
