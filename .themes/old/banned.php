<?php
   require_once 'app/require.php';

   $user = new UserController();

   Session::init();

   $username = Session::get("username");

   if (!Session::isLogged()) {
       Util::redirect('/auth/login.php');
   }
   ?>
<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
   <title>Banned - Brand</title>
   <link rel="icon" type="image/png" href="favicon.png">
   <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
   <link rel="stylesheet"href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
   <link rel="stylesheet" href="assets/css/untitled.css">
</head>
<html>
   <div class="container-fluid">
   <br>
   <center>
      <div  style='max-width: 500px;    margin-bottom: -7px;' class='alert alert-primary' role='alert'>
         You have been permanently banned. 
         <br>
         Reason: <?php Util::Display($user->banreason($username)); ?>
      </div>
      <br>
   </center>
   <center>
         <br>
         <a
      class="dropdown-item" id="logout"
      href="/auth/logout.php"
      style="color: rgb(255,255,255);"><i
      class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"
      style="color: rgb(255,255,255)!important;"></i>&nbsp;Logout</a>
   </center>


   <style>
      body {
      background: #121421;
      }
   </style>
   <html>

   