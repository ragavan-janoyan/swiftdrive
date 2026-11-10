<?php require_once '../includes/config.php'; require_role(['admin']);
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
  if(isset($_POST['create'])){
    $e=trim($_POST['email']);$pw=$_POST['password'];
    $c=$pdo->prepare('SELECT id FROM users WHERE email=?');$c->execute([$e]);
    if(!filter_var($e,FILTER_VALIDATE_EMAIL)||strlen($pw)<8||$c->fetch()||!in_array($_POST['role'],['staff','admin']))flash('Invalid data or email already used (password min 8 chars).','err');
    else{$pdo->prepare('INSERT INTO users(name,email,phone,password,role) VALUES(?,?,?,?,?)')->execute([trim($_POST['name']),$e,trim($_POST['phone']),password_hash($pw,PASSWORD_DEFAULT),$_POST['role']]);flash('Account created.');}
  }elseif((int)$_POST['delete']===user()['id'])flash('You cannot delete your own account.','err');
  else{$pdo->prepare('DELETE FROM users WHERE id=?')->execute([(int)$_POST['delete']]);flash('User deleted.');}
  redirect('admin/users.php');}
$title='Users'; require '../includes/header.php'; require '_nav.php'; ?>
<form class="box filters" method="post"><?= csrf_field() ?><h3 style="grid-column:1/-1">Create staff / admin account</h3><input type="hidden" name="create" value="1">
<input name="name" placeholder="Name" required><input type="email" name="email" placeholder="Email" required><input name="phone" placeholder="Phone">
<input type="password" name="password" placeholder="Password" required><select name="role"><option>staff</option><option>admin</option></select><button class="btn">Create</button></form>
<div class="tablewrap"><table><tr><th>Name</th><th>Email</th><th>Role</th><th></th></tr>
<?php foreach($pdo->query('SELECT * FROM users ORDER BY role,id') as $u): ?><tr><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td>
<td><form method="post"><?= csrf_field() ?><button class="btn sm danger" name="delete" value="<?= $u['id'] ?>" data-confirm="Delete this user and their bookings?">Delete</button></form></td></tr><?php endforeach; ?></table></div>
<?php require '../includes/footer.php';
