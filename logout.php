<?php require_once 'includes/config.php';$_SESSION=[];session_destroy();session_start();flash('You have been logged out.');redirect('index.php');
