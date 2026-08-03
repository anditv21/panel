<?php
require_once "../../app/require.php";
require_once "../../includes/head.nav.inc.php";

$user = new UserController();
Session::init();

if (!Session::isLogged()) {
    Util::redirect('/auth/login.php');
}

Util::banCheck();
Util::checktoken();
Util::adminCheck();

$clientFile = CLIENT_URL;
$maxFileSize = 20 * 1024 * 1024;
$statusMessage = '';
$statusType = 'danger';

if (isset($_GET['download']) && file_exists($clientFile)) {
    header('Content-Type: application/x-dosexec');
    header('Content-Disposition: attachment; filename="' . CLIENT_FILE . '"');
    header('Content-Length: ' . filesize($clientFile));
    $user->log(Session::get('username'), 'Downloaded the client from admin panel', admin_logs);
    readfile($clientFile);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['client']) || $_FILES['client']['error'] !== UPLOAD_ERR_OK) {
        $statusMessage = 'No file uploaded or an upload error occurred.';
    } elseif ($_FILES['client']['size'] < 1) {
        $statusMessage = 'The selected file is empty.';
    } elseif ($_FILES['client']['size'] > $maxFileSize) {
        $statusMessage = 'The selected file exceeds the 20 MB limit.';
    } elseif (strtolower(pathinfo($_FILES['client']['name'], PATHINFO_EXTENSION)) !== 'exe') {
        $statusMessage = 'Only .exe files are allowed.';
    } elseif (!is_uploaded_file($_FILES['client']['tmp_name'])) {
        $statusMessage = 'The uploaded file could not be verified.';
    } else {
        $fileHandle = fopen($_FILES['client']['tmp_name'], 'rb');
        $signature = $fileHandle ? fread($fileHandle, 2) : false;

        if ($fileHandle) {
            fclose($fileHandle);
        }

        if ($signature !== 'MZ') {
            $statusMessage = 'The selected file is not a valid Windows executable.';
        } elseif (!is_writable(dirname($clientFile))) {
            $statusMessage = 'The client directory is not writable by PHP.';
        } elseif (move_uploaded_file($_FILES['client']['tmp_name'], $clientFile)) {
            @chmod($clientFile, 0644);
            clearstatcache(true, $clientFile);
            $statusMessage = 'Client uploaded successfully.';
            $statusType = 'success';
            $user->log(Session::get('username'), 'Uploaded a new client', admin_logs);
        } else {
            $statusMessage = 'The client could not be saved.';
        }
    }
}

$clientExists = file_exists($clientFile);
$clientSize = $clientExists ? filesize($clientFile) : 0;
$clientUpdated = $clientExists ? filemtime($clientFile) : 0;

Util::head("Client Upload");
?>

<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Client Upload"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row justify-content-center">
               <div class="col-xl-6 col-lg-7 col-md-9">
                  <?php if (!empty($statusMessage)) : ?>
                     <div class="alert alert-<?php Util::display($statusType); ?> text-center">
                        <?php Util::display($statusMessage); ?>
                     </div>
                  <?php endif; ?>

                  <div class="card card-bg">
                     <div class="card-body">
                        <h5 class="card-title">Upload Client</h5>
                        <p class="card-description">Select a Windows executable with a maximum size of 20 MB.</p>

                        <form method="POST" enctype="multipart/form-data">
                           <?php Util::csrfField(); ?>
                           <div class="form-group">
                              <input type="file" name="client" class="form-control" accept=".exe,application/x-msdownload" required>
                           </div>
                           <br>
                           <button type="submit" class="btn btn-outline-primary" onclick="return confirm('Replace the current client?');">
                              <i class="fas fa-upload"></i> Upload
                           </button>
                        </form>
                     </div>
                  </div>

                  <br>
                  <div class="card card-bg">
                     <div class="card-body">
                        <h5 class="card-title">Current Client</h5>
                        <?php if ($clientExists) : ?>
                           <table class="table">
                              <tbody>
                                 <tr>
                                    <th>File</th>
                                    <td><?php Util::display(CLIENT_FILE); ?></td>
                                 </tr>
                                 <tr>
                                    <th>Size</th>
                                    <td><?php Util::display(number_format($clientSize / 1048576, 2)); ?> MB</td>
                                 </tr>
                                 <tr>
                                    <th>Last updated</th>
                                    <td><?php Util::display(date('F j, Y, H:i', $clientUpdated)); ?></td>
                                 </tr>
                              </tbody>
                           </table>
                           <a href="?download=1" class="btn btn-outline-primary">
                              <i class="fas fa-download"></i> Download
                           </a>
                        <?php else : ?>
                           <div class="alert alert-info mb-0">No client has been uploaded yet.</div>
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
