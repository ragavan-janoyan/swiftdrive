<?php require_once __DIR__.'/config.php'; $u=user(); $cur=basename($_SERVER['PHP_SELF']); ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($title??'Home') ?> | SwiftDrive Rentals</title>
<link rel="stylesheet" href="<?= BASE ?>/assets/css/style.css"></head><body>
<header class="nav"><div class="container nav-in">
<a class="logo" href="<?= BASE ?>/index.php">Swift<span>Drive</span></a>
<button id="menuBtn" aria-label="Menu">☰</button>
<nav id="menu">
<a class="<?= $cur=='index.php'?'active':'' ?>" href="<?= BASE ?>/index.php">Home</a>
<a class="<?= in_array($cur,['vehicles.php','vehicle.php'])?'active':'' ?>" href="<?= BASE ?>/vehicles.php">Vehicles</a>
<a class="<?= $cur=='about.php'?'active':'' ?>" href="<?= BASE ?>/about.php">About &amp; Terms</a>
<a class="<?= $cur=='blog.php'?'active':'' ?>" href="<?= BASE ?>/blog.php">Blog</a>
<a class="<?= $cur=='contact.php'?'active':'' ?>" href="<?= BASE ?>/contact.php">Contact</a>
<?php if($u): ?>
  <?php if($u['role']!=='customer'): ?><a href="<?= BASE ?>/admin/index.php">Dashboard</a><?php endif; ?>
  <a class="<?= $cur=='my_bookings.php'?'active':'' ?>" href="<?= BASE ?>/my_bookings.php">My Bookings</a>
  <a class="<?= $cur=='profile.php'?'active':'' ?>" href="<?= BASE ?>/profile.php">Hi, <?= e(explode(' ',$u['name'])[0]) ?></a>
  <a href="<?= BASE ?>/logout.php">Logout</a>
<?php else: ?>
  <a class="<?= $cur=='login.php'?'active':'' ?>" href="<?= BASE ?>/login.php">Login</a>
  <a class="btn sm" href="<?= BASE ?>/register.php">Sign up</a>
<?php endif; ?>
</nav></div></header>
<main class="container">
<?php if(!empty($_SESSION['flash'])): [$m,$t]=$_SESSION['flash']; unset($_SESSION['flash']); ?>
<div class="flash <?= $t ?>"><?= e($m) ?></div><?php endif; ?>