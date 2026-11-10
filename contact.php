<?php $title='Contact'; require_once 'includes/config.php';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
  $n=trim($_POST['name']);$m=trim($_POST['email']);$t=trim($_POST['message']);
  if(strlen($n)<2||!filter_var($m,FILTER_VALIDATE_EMAIL)||strlen($t)<10)flash('Please fill all fields correctly (message at least 10 characters).','err');
  else{$pdo->prepare('INSERT INTO inquiries(name,email,message) VALUES(?,?,?)')->execute([$n,$m,$t]);flash('Thanks! We will reply soon.');}
  redirect('contact.php');}
require 'includes/header.php'; ?>
<div class="narrow"><h1>Contact us</h1><form class="box stack" method="post"><?= csrf_field() ?>
<input name="name" placeholder="Your name" value="<?= e(user()['name']??'') ?>" required><input type="email" name="email" placeholder="Email" value="<?= e(user()['email']??'') ?>" required>
<textarea name="message" rows="5" placeholder="How can we help?" required></textarea><button class="btn">Send inquiry</button></form></div>
<?php require 'includes/footer.php';
