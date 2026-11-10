<?php $title='My bookings'; require_once 'includes/config.php'; require_login();
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
  $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND user_id=? AND status IN('pending','confirmed') AND pickup_date>CURDATE()")->execute([(int)$_POST['cancel'],user()['id']]);
  flash('Booking cancelled (if eligible). Only future bookings can be cancelled.');redirect('my_bookings.php');}
require 'includes/header.php';
$s=$pdo->prepare("SELECT b.*,v.name,v.brand,(SELECT COUNT(*) FROM payments p WHERE p.booking_id=b.id AND p.type='deposit' AND p.status='success') paid FROM bookings b JOIN vehicles v ON v.id=b.vehicle_id WHERE b.user_id=? ORDER BY b.created_at DESC");
$s->execute([user()['id']]);$rows=$s->fetchAll(); ?>
<h1>My bookings</h1><div class="tablewrap"><table><tr><th>#</th><th>Vehicle</th><th>Dates</th><th>Total</th><th>Deposit</th><th>Status</th><th></th></tr>
<?php foreach($rows as $b): ?><tr><td><?= $b['id'] ?></td><td><?= e($b['brand'].' '.$b['name']) ?></td><td><?= e($b['pickup_date']) ?> → <?= e($b['return_date']) ?><br><small><?= e($b['pickup_location']) ?></small></td>
<td><?= money($b['total']) ?><?= $b['late_fee']>0?'<br><small>+ late fee '.money($b['late_fee']).'</small>':'' ?></td>
<td><?= $b['paid']?'<span class="badge confirmed">Paid</span>':'<a class="btn sm" href="payment.php?id='.$b['id'].'">Pay now</a>' ?></td>
<td><span class="badge <?= $b['status'] ?>"><?= e(str_replace('_',' ',$b['status'])) ?></span></td>
<td><?php if(in_array($b['status'],['pending','confirmed'])&&$b['pickup_date']>date('Y-m-d')): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="cancel" value="<?= $b['id'] ?>"><button class="btn sm danger" data-confirm="Cancel this booking?">Cancel</button></form><?php endif; ?></td></tr>
<?php endforeach; ?></table></div><?php if(!$rows) echo '<p class="box">No bookings yet. <a href="vehicles.php">Browse vehicles</a></p>';
require 'includes/footer.php';
