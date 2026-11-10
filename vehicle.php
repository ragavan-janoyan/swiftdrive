<?php require_once 'includes/config.php';
$s=$pdo->prepare('SELECT * FROM vehicles WHERE id=?');$s->execute([(int)($_GET['id']??0)]);$v=$s->fetch();
if(!$v){http_response_code(404);die('Vehicle not found.');}
$title=$v['brand'].' '.$v['name'];
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['review'])){
  csrf_check();require_login();
  $ok=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id=? AND vehicle_id=? AND status='returned'");$ok->execute([user()['id'],$v['id']]);
  $rt=(int)$_POST['rating'];
  if(!$ok->fetchColumn())flash('Only customers who completed a rental can review.','err');
  elseif($rt<1||$rt>5)flash('Rating must be 1-5.','err');
  else{$pdo->prepare('INSERT INTO reviews(user_id,vehicle_id,rating,comment) VALUES(?,?,?,?)')->execute([user()['id'],$v['id'],$rt,trim($_POST['comment'])]);flash('Thanks for your review!');}
  redirect('vehicle.php?id='.$v['id']);
}
require 'includes/header.php';
$rv=$pdo->prepare('SELECT r.*,u.name FROM reviews r JOIN users u ON u.id=r.user_id WHERE vehicle_id=? ORDER BY r.created_at DESC');$rv->execute([$v['id']]);$reviews=$rv->fetchAll(); ?>
<div class="split"><div>
<div class="box"><div class="ph" style="border-radius:12px;height:220px"><?= ($src=vehicle_img($v))?'<img src="'.e($src).'" style="height:220px;border-radius:12px" alt="">':icon($v['type']) ?></div>
<h1 style="margin-top:16px"><?= e($v['brand'].' '.$v['name']) ?></h1><span class="tag"><?= e(ucfirst($v['type'])) ?></span>
<p style="margin:12px 0"><?= e($v['description']) ?></p>
<table><tr><th>Transmission</th><td><?= e($v['transmission']) ?></td><th>Fuel</th><td><?= e($v['fuel']) ?></td></tr>
<tr><th>Seats</th><td><?= (int)$v['seats'] ?></td><th>Location</th><td><?= e($v['location']) ?></td></tr>
<tr><th>Daily rate</th><td><?= money($v['daily_rate']) ?></td><th>Weekly rate</th><td><?= money($v['weekly_rate']) ?></td></tr></table></div>
<h2>Reviews (<?= count($reviews) ?>)</h2>
<?php foreach($reviews as $r): ?><div class="box"><span class="stars"><?= str_repeat('â',$r['rating']).str_repeat('â',5-$r['rating']) ?></span> <b><?= e($r['name']) ?></b><p><?= e($r['comment']) ?></p></div><?php endforeach;
if(!$reviews)echo '<p class="muted">No reviews yet.</p>';
if(user()): ?><form class="box stack" method="post"><h3>Leave a review</h3><?= csrf_field() ?><input type="hidden" name="review" value="1">
<select name="rating"><option value="5">5 - Excellent</option><option value="4">4</option><option value="3">3</option><option value="2">2</option><option value="1">1 - Poor</option></select>
<textarea name="comment" rows="3" required placeholder="Share your experience"></textarea><button class="btn">Submit review</button></form><?php endif; ?>
</div>
<aside><form id="bookForm" class="box stack" method="post" action="book.php" data-daily="<?= $v['daily_rate'] ?>" data-weekly="<?= $v['weekly_rate'] ?>">
<h3>Book this vehicle</h3><?= csrf_field() ?><input type="hidden" name="vehicle_id" value="<?= $v['id'] ?>">
<div><label>Pick-up date</label><input type="date" name="pickup_date" required value="<?= e($_GET['pickup']??'') ?>"></div>
<div><label>Return date</label><input type="date" name="return_date" required value="<?= e($_GET['return']??'') ?>"></div>
<div><label>Pick-up location</label><select name="pickup_location"><option>Negombo</option><option>Colombo</option></select></div>
<div id="estimate" class="live"></div>
<?php if($v['status']!=='available'): ?><p class="flash err">Currently unavailable (maintenance).</p><?php else: ?>
<button class="btn"><?= user()?'Reserve &amp; pay deposit':'Login to book' ?></button><?php endif; ?></form></aside></div>
<?php require 'includes/footer.php';