<?php $title='Blog'; require 'includes/header.php';
$posts=$pdo->query('SELECT * FROM posts ORDER BY created_at DESC')->fetchAll(); ?>
<h1>Travel blog</h1><?php foreach($posts as $p): ?><article class="box"><h3><?= e($p['title']) ?></h3><small><?= e(date('d M Y',strtotime($p['created_at']))) ?></small><p><?= e($p['body']) ?></p></article><?php endforeach;
require 'includes/footer.php';
