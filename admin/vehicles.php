<?php require_once '../includes/config.php'; require_role(['staff','admin']);
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check();
  if(isset($_POST['delete'])){
    $c=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE vehicle_id=? AND status IN('pending','confirmed','picked_up')");$c->execute([(int)$_POST['delete']]);
    if($c->fetchColumn())flash('Cannot delete: vehicle has active bookings.','err');
    else{$pdo->prepare('DELETE FROM vehicles WHERE id=?')->execute([(int)$_POST['delete']]);flash('Vehicle deleted.');}
  }else{
    $d=[trim($_POST['name']),trim($_POST['brand']),$_POST['type'],$_POST['transmission'],trim($_POST['fuel']),(int)$_POST['seats'],(float)$_POST['daily_rate'],(float)$_POST['weekly_rate'],$_POST['location'],trim($_POST['image_url']),trim($_POST['description']),$_POST['status']];
    if(!$d[0]||!$d[1]||$d[5]<1||$d[6]<=0||$d[7]<=0)flash('Fill in name, brand, seats and positive rates.','err');
    elseif(!empty($_POST['id'])){$pdo->prepare('UPDATE vehicles SET name=?,brand=?,type=?,transmission=?,fuel=?,seats=?,daily_rate=?,weekly_rate=?,location=?,image_url=?,description=?,status=? WHERE id=?')->execute([...$d,(int)$_POST['id']]);flash('Vehicle updated.');}
    else{$pdo->prepare('INSERT INTO vehicles(name,brand,type,transmission,fuel,seats,daily_rate,weekly_rate,location,image_url,description,status) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)')->execute($d);flash('Vehicle added.');}
  }redirect('admin/vehicles.php');}
$title='Manage vehicles'; require '../includes/header.php'; require '_nav.php';
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM vehicles WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$x=fn($k,$d='')=>e($edit[$k]??$d);
$rows=$pdo->query('SELECT * FROM vehicles ORDER BY id DESC')->fetchAll(); ?>
<form class="box filters" method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $x('id') ?>">
<h3 style="grid-column:1/-1"><?= $edit?'Edit vehicle':'Add vehicle' ?></h3>
<div><label>Name</label><input name="name" value="<?= $x('name') ?>" required></div><div><label>Brand</label><input name="brand" value="<?= $x('brand') ?>" required></div>
<div><label>Type</label><select name="type"><?php foreach(['economy','suv','van','luxury'] as $t) echo '<option value="'.$t.'"'.(($edit['type']??'')==$t?' selected':'').'>'.ucfirst($t).'</option>'; ?></select></div>
<div><label>Transmission</label><select name="transmission"><?php foreach(['Automatic','Manual'] as $t) echo '<option'.(($edit['transmission']??'')==$t?' selected':'').'>'.$t.'</option>'; ?></select></div>
<div><label>Fuel</label><input name="fuel" value="<?= $x('fuel','Petrol') ?>"></div><div><label>Seats</label><input type="number" name="seats" min="1" value="<?= $x('seats') ?>" required></div>
<div><label>Daily rate</label><input type="number" step="0.01" name="daily_rate" value="<?= $x('daily_rate') ?>" required></div><div><label>Weekly rate</label><input type="number" step="0.01" name="weekly_rate" value="<?= $x('weekly_rate') ?>" required></div>
<div><label>Location</label><select name="location"><?php foreach(['Negombo','Colombo'] as $t) echo '<option'.(($edit['location']??'')==$t?' selected':'').'>'.$t.'</option>'; ?></select></div>
<div><label>Status</label><select name="status"><?php foreach(['available','maintenance'] as $t) echo '<option'.(($edit['status']??'')==$t?' selected':'').'>'.$t.'</option>'; ?></select></div>
<div><label>Image URL (optional)</label><input name="image_url" value="<?= $x('image_url') ?>"></div>
<div style="grid-column:1/-1"><label>Description</label><textarea name="description" rows="2"><?= $x('description') ?></textarea></div>
<button class="btn"><?= $edit?'Update':'Add' ?></button></form>
<div class="tablewrap"><table><tr><th>Vehicle</th><th>Type</th><th>Rates</th><th>Status</th><th></th></tr>
<?php foreach($rows as $v): ?><tr><td><?= e($v['brand'].' '.$v['name']) ?></td><td><?= e($v['type']) ?></td><td><?= money($v['daily_rate']) ?> / <?= money($v['weekly_rate']) ?></td><td><?= e($v['status']) ?></td>
<td style="display:flex;gap:4px"><a class="btn sm alt" href="?edit=<?= $v['id'] ?>">Edit</a><form method="post"><?= csrf_field() ?><button class="btn sm danger" name="delete" value="<?= $v['id'] ?>" data-confirm="Delete this vehicle?">Delete</button></form></td></tr><?php endforeach; ?></table></div>
<?php require '../includes/footer.php';
