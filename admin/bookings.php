<?php require_once '../includes/config.php'; require_role(['staff','admin']);
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();$id=(int)$_POST['id'];$act=$_POST['action'];
  $s=$pdo->prepare('SELECT b.*,v.daily_rate FROM bookings b JOIN vehicles v ON v.id=b.vehicle_id WHERE b.id=?');$s->execute([$id]);$b=$s->fetch();
  if($b){
    if($act==='confirm'&&$b['status']==='pending'){
      $p=$pdo->prepare("SELECT COUNT(*) FROM payments WHERE booking_id=? AND type='deposit' AND status='success'");$p->execute([$id]);
      if(!$p->fetchColumn())flash('Cannot confirm: deposit not paid.','err');
      else{$pdo->prepare("UPDATE bookings SET status='confirmed' WHERE id=?")->execute([$id]);flash('Booking confirmed.');}
    }elseif($act==='pickup'&&$b['status']==='confirmed'){$pdo->prepare("UPDATE bookings SET status='picked_up' WHERE id=?")->execute([$id]);flash('Vehicle handed over.');}
    elseif($act==='return'&&$b['status']==='picked_up'){
      // Late-return penalty: 150% of daily rate for each day after the agreed return date
      $late=0;if(date('Y-m-d')>$b['return_date'])$late=(int)(new DateTime($b['return_date']))->diff(new DateTime())->days*$b['daily_rate']*1.5;
      $pdo->prepare("UPDATE bookings SET status='returned',late_fee=? WHERE id=?")->execute([$late,$id]);
      if($late>0)$pdo->prepare("INSERT INTO payments(booking_id,amount,type,status,reference) VALUES(?,?,'penalty','success',?)")->execute([$id,$late,'PEN'.$id]);
      flash($late>0?'Returned late. Penalty '.money($late).' recorded.':'Vehicle returned on time.');
    }elseif($act==='cancel'&&in_array($b['status'],['pending','confirmed'])){$pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?")->execute([$id]);flash('Booking cancelled.');}
    else flash('Action not allowed for this booking status.','err');
  }redirect('admin/bookings.php');}
$title='Bookings'; require '../includes/header.php'; require '_nav.php';
$rows=$pdo->query("SELECT b.*,u.name un,u.phone,v.brand,v.name vn FROM bookings b JOIN users u ON u.id=b.user_id JOIN vehicles v ON v.id=b.vehicle_id ORDER BY b.id DESC")->fetchAll(); ?>
<div class="tablewrap"><table><tr><th>#</th><th>Customer</th><th>Vehicle</th><th>Dates</th><th>Total</th><th>Status</th><th>Actions</th></tr>
<?php foreach($rows as $b): ?><tr><td><?= $b['id'] ?></td><td><?= e($b['un']) ?><br><small><?= e($b['phone']) ?></small></td><td><?= e($b['brand'].' '.$b['vn']) ?></td>
<td><?= e($b['pickup_date']) ?> → <?= e($b['return_date']) ?></td><td><?= money($b['total']) ?></td><td><span class="badge <?= $b['status'] ?>"><?= e(str_replace('_',' ',$b['status'])) ?></span></td>
<td><form method="post" style="display:flex;gap:4px;flex-wrap:wrap"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $b['id'] ?>">
<?php $acts=['pending'=>['confirm'=>'ok','cancel'=>'danger'],'confirmed'=>['pickup'=>'alt','cancel'=>'danger'],'picked_up'=>['return'=>'ok']][$b['status']]??[];
foreach($acts as $a=>$c) echo '<button class="btn sm '.$c.'" name="action" value="'.$a.'">'.ucfirst($a).'</button>'; ?></form></td></tr><?php endforeach; ?></table></div>
<?php require '../includes/footer.php';
