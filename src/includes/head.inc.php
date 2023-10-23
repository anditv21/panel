<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= (SITE_DESC); ?>" />
    <meta name="theme-color" content="#e14eca">

    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="<?= (SITE_NAME); ?>" />
    <meta property="og:title" content="<?= htmlspecialchars(stripslashes(trim($title))); ?>" />
    <meta property="og:url" content="<?= (SITE_URL); ?>" />
    <meta property="og:description" content="<?= (SITE_DESC); ?>" />
    <meta property="og:image" content="" />

    <meta property="twitter:description" content="<?= (SITE_DESC); ?>" />
    <meta property="twitter:title" content="<?= htmlspecialchars(stripslashes(trim($title))); ?>" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= SUB_DIR ?>/bootstrap/css/bootstrap.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= SUB_DIR ?>/assets/css/custom.css" />
</head>

</html>