<?php $title='Home'; require 'includes/header.php';
$featured=$pdo->query("SELECT * FROM vehicles WHERE status='available' ORDER BY RAND() LIMIT 6")->fetchAll();
$reviews=$pdo->query("SELECT r.*,u.name,v.brand,v.name vname FROM reviews r JOIN users u ON u.id=r.user_id JOIN vehicles v ON v.id=r.vehicle_id ORDER BY r.created_at DESC LIMIT 3")->fetchAll(); ?>
<section class="hero"><h1>Drive Sri Lanka Your Way</h1>
<p>Economy cars, SUVs, vans and luxury rides from Negombo. Short or long-term rental, booked in minutes.</p>
<form class="search" action="vehicles.php" method="get">
<div><label>Pick-up date</label><input type="date" name="pickup" required min="<?= date('Y-m-d') ?>"></div>
<div><label>Return date</label><input type="date" name="return" required min="<?= date('Y-m-d') ?>"></div>
<div><label>Location</label><select name="location"><option value="">Any</option><option>Negombo</option><option>Colombo</option></select></div>
<div><label>Type</label><select name="type"><option value="">All</option><option value="economy">Economy</option><option value="suv">SUV</option><option value="van">Van</option><option value="luxury">Luxury</option></select></div>
<button class="btn">Search cars</button></form></section>
<h2>Featured vehicles</h2><div class="grid"><?php foreach($featured as $v) echo vehicle_card($v); ?></div>
<h2>Why SwiftDrive?</h2>
<div class="grid"><div class="box"><h3>✅ No booking conflicts</h3><p class="muted">Live availability prevents double bookings.</p></div>
<div class="box"><h3>💳 Secure deposit</h3><p class="muted">Pay only 20% online to reserve your vehicle.</p></div>
<div class="box"><h3>📍 Easy pick-up</h3><p class="muted">Collect in Negombo or Colombo.</p></div></div>
<?php if($reviews): ?><h2>Customer reviews</h2><div class="grid"><?php foreach($reviews as $r): ?>
<div class="box"><div class="stars"><?= str_repeat('★',$r['rating']).str_repeat('☆',5-$r['rating']) ?></div>
<p>“<?= e($r['comment']) ?>”</p><small>— <?= e($r['name']) ?>, <?= e($r['brand'].' '.$r['vname']) ?></small></div><?php endforeach; ?></div><?php endif;
require 'includes/footer.php';
