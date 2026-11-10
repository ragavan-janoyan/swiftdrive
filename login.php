<?php $title='Login'; require_once 'includes/config.php';
if(user())redirect('index.php');$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
  $s=$pdo->prepare('SELECT * FROM users WHERE email=?');$s->execute([trim($_POST['email']??'')]);$u=$s->fetch();
  if($u&&password_verify($_POST['password']??'',$u['password'])){
    session_regenerate_id(true);$_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
    redirect($u['role']==='customer'?'index.php':'admin/index.php');
  }$err='Incorrect email or password.';
}
require 'includes/header.php'; ?>
<div class="narrow"><h1>Login</h1><?php if($err) echo '<div class="flash err">'.e($err).'</div>'; ?>
<form class="box stack" method="post"><?= csrf_field() ?>
<div><label>Email</label><input type="email" name="email" required></div>
<div><label>Password</label><input type="password" name="password" required></div>
<button class="btn">Login</button><small>New here? <a href="register.php">Create an account</a></small></form></div>
<?php require 'includes/footer.php';
