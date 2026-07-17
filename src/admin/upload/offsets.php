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

$allowedFiles = [
    'buttons.json',
    'client_dll.json',
    'offsets.json'
];
$downloadDir = SITE_ROOT . '/downloads/';
$maxFileSize = 2 * 1024 * 1024;
$statusMessage = '';
$statusType = 'danger';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['offset_file']) || $_FILES['offset_file']['error'] !== UPLOAD_ERR_OK) {
        $statusMessage = 'No file uploaded or an upload error occurred.';
    } elseif ($_FILES['offset_file']['size'] < 1) {
        $statusMessage = 'The selected file is empty.';
    } elseif ($_FILES['offset_file']['size'] > $maxFileSize) {
        $statusMessage = 'The selected file exceeds the 2 MB limit.';
    } elseif (!is_uploaded_file($_FILES['offset_file']['tmp_name'])) {
        $statusMessage = 'The uploaded file could not be verified.';
    } else {
        $filename = basename($_FILES['offset_file']['name']);
        $contents = file_get_contents($_FILES['offset_file']['tmp_name']);

        if (!in_array($filename, $allowedFiles, true)) {
            $statusMessage = 'Only buttons.json, client_dll.json and offsets.json are allowed.';
        } elseif ($contents === false || json_decode($contents, true) === null && json_last_error() !== JSON_ERROR_NONE) {
            $statusMessage = 'The selected file does not contain valid JSON.';
        } elseif (!is_dir($downloadDir) && !mkdir($downloadDir, 0755, true)) {
            $statusMessage = 'The downloads directory could not be created.';
        } elseif (!is_writable($downloadDir)) {
            $statusMessage = 'The downloads directory is not writable by PHP.';
        } elseif (move_uploaded_file($_FILES['offset_file']['tmp_name'], $downloadDir . $filename)) {
            @chmod($downloadDir . $filename, 0644);
            clearstatcache(true, $downloadDir . $filename);
            $statusMessage = "$filename uploaded successfully.";
            $statusType = 'success';
            $user->log(Session::get('username'), "Uploaded $filename", admin_logs);
        } else {
            $statusMessage = 'The file could not be saved.';
        }
    }
}

Util::head("Offsets Upload");
?>

<!DOCTYPE html>
<html lang="en">

<head><?php Util::navbar(); ?></head>
<?php display_top_nav("Offsets Upload"); ?>

<body class="pace-done no-loader page-sidebar-collapsed">
   <div class="page-container">
      <div class="page-content">
         <div class="main-wrapper">
            <div class="row justify-content-center">
               <div class="col-xl-7 col-lg-8 col-md-10">
                  <?php if (!empty($statusMessage)) : ?>
                     <div class="alert alert-<?php Util::display($statusType); ?> text-center">
                        <?php Util::display($statusMessage); ?>
                     </div>
                  <?php endif; ?>

                  <div class="card card-bg">
                     <div class="card-body">
                        <h5 class="card-title">Upload Offsets</h5>
                        <p class="card-description">Select buttons.json, client_dll.json or offsets.json with a maximum size of 2 MB.</p>

                        <form method="POST" enctype="multipart/form-data">
                           <div class="form-group">
                              <input type="file" name="offset_file" class="form-control" accept=".json,application/json" required>
                           </div>
                           <br>
                           <button type="submit" class="btn btn-outline-primary" onclick="return confirm('Replace the selected offsets file?');">
                              <i class="fas fa-upload"></i> Upload
                           </button>
                        </form>
                     </div>
                  </div>

                  <br>
                  <div class="card card-bg">
                     <div class="card-body">
                        <h5 class="card-title">Current Files</h5>
                        <table class="table">
                           <thead>
                              <tr>
                                 <th>File</th>
                                 <th>Status</th>
                                 <th>Last updated</th>
                                 <th></th>
                              </tr>
                           </thead>
                           <tbody>
                              <?php foreach ($allowedFiles as $filename) : ?>
                                 <?php $filePath = $downloadDir . $filename; ?>
                                 <tr>
                                    <td><?php Util::display($filename); ?></td>
                                    <?php if (file_exists($filePath)) : ?>
                                       <td><span class="badge badge-success">Uploaded</span></td>
                                       <td><?php Util::display(date('F j, Y, H:i', filemtime($filePath))); ?></td>
                                       <td>
                                          <a href="<?php Util::display(SUB_DIR . '/downloads/' . $filename); ?>" class="btn btn-outline-primary btn-sm" download>
                                             <i class="fas fa-download"></i>
                                          </a>
                                       </td>
                                    <?php else : ?>
                                       <td><span class="badge badge-secondary">Missing</span></td>
                                       <td>-</td>
                                       <td></td>
                                    <?php endif; ?>
                                 </tr>
                              <?php endforeach; ?>
                           </tbody>
                        </table>
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
