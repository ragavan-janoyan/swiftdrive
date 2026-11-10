<?php $title='Vehicles'; require 'includes/header.php';
$w=["v.status='available'"];$p=[];
foreach(['type','transmission','location'] as $f) if(!empty($_GET[$f])){$w[]="v.$f=?";$p[]=$_GET[$f];}
if(is_numeric($_GET['min']??'')){$w[]='v.daily_rate>=?';$p[]=$_GET['min'];}
if(is_numeric($_GET['max']??'')){$w[]='v.daily_rate<=?';$p[]=$_GET['max'];}
if(is_numeric($_GET['seats']??'')){$w[]='v.seats>=?';$p[]=$_GET['seats'];}
$pick=$_GET['pickup']??'';$ret=$_GET['return']??'';$dateMsg='';
if($pick&&$ret){
  if($ret<=$pick||$pick<date('Y-m-d')) $dateMsg='Invalid dates: return must be after pick-up and pick-up cannot be in the past.';
  else{$w[]="NOT EXISTS(SELECT 1 FROM bookings b WHERE b.vehicle_id=v.id AND b.status IN('pending','confirmed','picked_up') AND ?<b.return_date AND ?>b.pickup_date)";$p[]=$pick;$p[]=$ret;}
}
$sort=['price_asc'=>'v.daily_rate ASC','price_desc'=>'v.daily_rate DESC'][$_GET['sort']??'']??'v.id ASC';
$s=$pdo->prepare('SELECT v.* FROM vehicles v WHERE '.implode(' AND ',$w)." ORDER BY $sort");
if(!$dateMsg){$s->execute($p);$rows=$s->fetchAll();}else $rows=[]; ?>
<h1>Our Fleet</h1>
<form class="box filters" method="get">
<div><label>Pick-up</label><input type="date" name="pickup" value="<?= e($pick) ?>"></div>
<div><label>Return</label><input type="date" name="return" value="<?= e($ret) ?>"></div>
<div><label>Type</label><select name="type"><option value="">All</option><?php foreach(['economy','suv','van','luxury'] as $t) echo '<option value="'.$t.'"'.(($_GET['type']??'')==$t?' selected':'').'>'.ucfirst($t).'</option>'; ?></select></div>
<div><label>Transmission</label><select name="transmission"><option value="">Any</option><?php foreach(['Automatic','Manual'] as $t) echo '<option'.(($_GET['transmission']??'')==$t?' selected':'').'>'.$t.'</option>'; ?></select></div>
<div><label>Location</label><select name="location"><option value="">Any</option><?php foreach(['Negombo','Colombo'] as $t) echo '<option'.(($_GET['location']??'')==$t?' selected':'').'>'.$t.'</option>'; ?></select></div>
<div><label>Min Rs/day</label><input type="number" name="min" min="0" value="<?= e($_GET['min']??'') ?>"></div>
<div><label>Max Rs/day</label><input type="number" name="max" min="0" value="<?= e($_GET['max']??'') ?>"></div>
<div><label>Min seats</label><input type="number" name="seats" min="1" value="<?= e($_GET['seats']??'') ?>"></div>
<div><label>Sort</label><select name="sort"><option value="">Default</option><option value="price_asc"<?= ($_GET['sort']??'')=='price_asc'?' selected':'' ?>>Price low-high</option><option value="price_desc"<?= ($_GET['sort']??'')=='price_desc'?' selected':'' ?>>Price high-low</option></select></div>
<button class="btn">Filter</button></form>
<?php if($dateMsg) echo '<div class="flash err">'.e($dateMsg).'</div>'; ?>
<p class="muted" style="margin-bottom:16px"><?= count($rows) ?> vehicle(s) found</p>
<div class="grid"><?php foreach($rows as $v) echo vehicle_card($v); ?></div>
<?php if(!$rows&&!$dateMsg) echo '<div class="box">No vehicles match your filters or dates. Try different options.</div>';
require 'includes/footer.php';