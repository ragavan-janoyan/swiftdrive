<?php $title='Sign up'; require_once 'includes/config.php';
$err=[];$f=['name'=>'','email'=>'','phone'=>''];
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
  foreach($f as $k=>$_)$f[$k]=trim($_POST[$k]??'');$pw=$_POST['password']??'';
  if(strlen($f['name'])<2)$err[]='Name is required.';
  if(!filter_var($f['email'],FILTER_VALIDATE_EMAIL))$err[]='Enter a valid email.';
  if(!preg_match('/^[0-9+\- ]{9,15}$/',$f['phone']))$err[]='Enter a valid phone number.';
  if(strlen($pw)<8||!preg_match('/\d/',$pw)||!preg_match('/[A-Za-z]/',$pw))$err[]='Password needs 8+ characters with letters and numbers.';
  if($pw!==($_POST['confirm']??''))$err[]='Passwords do not match.';
  if(!$err){$c=$pdo->prepare('SELECT id FROM users WHERE email=?');$c->execute([$f['email']]);if($c->fetch())$err[]='That email is already registered.';}
  if(!$err){$pdo->prepare("INSERT INTO users(name,email,phone,password) VALUES(?,?,?,?)")->execute([$f['name'],$f['email'],$f['phone'],password_hash($pw,PASSWORD_DEFAULT)]);
    flash('Account created. Please log in.');redirect('login.php');}
}
require 'includes/header.php'; ?>
<div class="narrow"><h1>Create account</h1>
<?php foreach($err as $x) echo '<div class="flash err">'.e($x).'</div>'; ?>
<form class="box stack" method="post"><?= csrf_field() ?>
<div><label>Full name</label><input name="name" value="<?= e($f['name']) ?>" required></div>
<div><label>Email</label><input type="email" name="email" value="<?= e($f['email']) ?>" required></div>
<div><label>Phone</label><input name="phone" value="<?= e($f['phone']) ?>" required></div>
<div><label>Password</label><input type="password" name="password" required></div>
<div><label>Confirm password</label><input type="password" name="confirm" required></div>
<button class="btn">Sign up</button><small>Already registered? <a href="login.php">Log in</a></small></form></div>
<?php require 'includes/footer.php';
