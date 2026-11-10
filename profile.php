<?php $title='Profile'; require_once 'includes/config.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$id=user()['id'];
  if(isset($_POST['info'])){
    $n=trim($_POST['name']);$p=trim($_POST['phone']);
    if(strlen($n)<2||!preg_match('/^[0-9+\- ]{9,15}$/',$p))flash('Enter a valid name and phone.','err');
    else{$pdo->prepare('UPDATE users SET name=?,phone=? WHERE id=?')->execute([$n,$p,$id]);$_SESSION['user']['name']=$n;flash('Profile updated.');}
  }else{
    $s=$pdo->prepare('SELECT password FROM users WHERE id=?');$s->execute([$id]);$h=$s->fetchColumn();$new=$_POST['new']??'';
    if(!password_verify($_POST['old']??'',$h))flash('Current password is wrong.','err');
    elseif(strlen($new)<8||!preg_match('/\d/',$new)||!preg_match('/[A-Za-z]/',$new))flash('New password needs 8+ characters with letters and numbers.','err');
    else{$pdo->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($new,PASSWORD_DEFAULT),$id]);flash('Password changed.');}
  }redirect('profile.php');}
$s=$pdo->prepare('SELECT * FROM users WHERE id=?');$s->execute([user()['id']]);$me=$s->fetch();
require 'includes/header.php'; ?>
<div class="narrow"><h1>My profile</h1>
<form class="box stack" method="post"><?= csrf_field() ?><input type="hidden" name="info" value="1">
<div><label>Email (cannot be changed)</label><input value="<?= e($me['email']) ?>" disabled></div>
<div><label>Name</label><input name="name" value="<?= e($me['name']) ?>" required></div>
<div><label>Phone</label><input name="phone" value="<?= e($me['phone']) ?>" required></div><button class="btn">Save</button></form>
<form class="box stack" method="post"><?= csrf_field() ?><h3>Change password</h3>
<input type="password" name="old" placeholder="Current password" required><input type="password" name="new" placeholder="New password" required><button class="btn alt">Change password</button></form></div>
<?php require 'includes/footer.php';
