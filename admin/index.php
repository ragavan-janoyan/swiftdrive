<?php $title='Dashboard'; require '../includes/header.php'; require '_nav.php';
$q=fn($sql)=>$pdo->query($sql)->fetchColumn();
$total=$q("SELECT COUNT(*) FROM vehicles");$out=$q("SELECT COUNT(DISTINCT vehicle_id) FROM bookings WHERE status='picked_up'");
$rev=$q("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='success'");
$byStatus=$pdo->query("SELECT status,COUNT(*) c FROM bookings GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR); ?>
<div class="stats">
<div class="stat"><b><?= $total ?></b>Vehicles in fleet</div>
<div class="stat"><b><?= $total?round($out/$total*100):0 ?>%</b>Fleet utilisation (<?= $out ?> on rent)</div>
<div class="stat"><b><?= $byStatus['pending']??0 ?></b>Pending bookings</div>
<div class="stat"><b><?= $q("SELECT COUNT(*) FROM inquiries WHERE status='new'") ?></b>New inquiries</div>
<div class="stat"><b><?= money($rev) ?></b>Payments received</div>
<div class="stat"><b><?= $q("SELECT COUNT(*) FROM users WHERE role='customer'") ?></b>Customers</div></div>
<h2>Today's pick-ups &amp; returns</h2><div class="tablewrap"><table><tr><th>Booking</th><th>Customer</th><th>Vehicle</th><th>Type</th></tr>
<?php $t=$pdo->query("SELECT b.id,u.name,v.brand,v.name vn,'Pick-up' k FROM bookings b JOIN users u ON u.id=b.user_id JOIN vehicles v ON v.id=b.vehicle_id WHERE b.pickup_date=CURDATE() AND b.status IN('pending','confirmed')
UNION SELECT b.id,u.name,v.brand,v.name,'Return' FROM bookings b JOIN users u ON u.id=b.user_id JOIN vehicles v ON v.id=b.vehicle_id WHERE b.return_date=CURDATE() AND b.status='picked_up'")->fetchAll();
foreach($t as $r) echo '<tr><td>#'.$r['id'].'</td><td>'.e($r['name']).'</td><td>'.e($r['brand'].' '.$r['vn']).'</td><td>'.$r['k'].'</td></tr>'; ?></table></div>
<?php require '../includes/footer.php';
