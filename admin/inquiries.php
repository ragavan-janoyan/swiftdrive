<?php require_once '../includes/config.php'; require_role(['staff','admin']);
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$pdo->prepare("UPDATE inquiries SET status='replied' WHERE id=?")->execute([(int)$_POST['id']]);flash('Marked as replied.');redirect('admin/inquiries.php');}
$title='Inquiries'; require '../includes/header.php'; require '_nav.php';
foreach($pdo->query('SELECT * FROM inquiries ORDER BY id DESC') as $i): ?>
<div class="box"><b><?= e($i['name']) ?></b> &lt;<a href="mailto:<?= e($i['email']) ?>"><?= e($i['email']) ?></a>&gt; <span class="badge <?= $i['status']=='new'?'pending':'returned' ?>"><?= e($i['status']) ?></span>
<small> · <?= e($i['created_at']) ?></small><p><?= nl2br(e($i['message'])) ?></p>
<?php if($i['status']=='new'): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $i['id'] ?>"><button class="btn sm ok">Mark replied</button></form><?php endif; ?></div>
<?php endforeach; require '../includes/footer.php';
