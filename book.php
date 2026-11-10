<?php require_once 'includes/config.php';
if($_SERVER['REQUEST_METHOD']!=='POST')redirect('vehicles.php');
csrf_check();require_login();
$vid=(int)$_POST['vehicle_id'];$a=$_POST['pickup_date']??'';$b=$_POST['return_date']??'';$loc=$_POST['pickup_location']??'Negombo';
$back='vehicle.php?id='.$vid.'&pickup='.urlencode($a).'&return='.urlencode($b);
$d1=DateTime::createFromFormat('Y-m-d',$a);$d2=DateTime::createFromFormat('Y-m-d',$b);
if(!$d1||!$d2||$d1->format('Y-m-d')!==$a||$d2->format('Y-m-d')!==$b){flash('Please enter valid dates.','err');redirect($back);}
if($a<date('Y-m-d')){flash('Pick-up date cannot be in the past.','err');redirect($back);}
if($b<=$a){flash('Return date must be after the pick-up date.','err');redirect($back);}
if(rental_days($a,$b)>90){flash('Maximum rental period is 90 days.','err');redirect($back);}
try{
  $pdo->beginTransaction();
  // Lock the vehicle row so two customers cannot book the same dates at once
  $s=$pdo->prepare("SELECT * FROM vehicles WHERE id=? AND status='available' FOR UPDATE");$s->execute([$vid]);$v=$s->fetch();
  if(!$v){$pdo->rollBack();flash('Vehicle is not available.','err');redirect('vehicles.php');}
  if(is_booked($pdo,$vid,$a,$b)){$pdo->rollBack();flash('Sorry, this vehicle was just booked for those dates. Please choose other dates.','err');redirect($back);}
  $total=calc_price($v,rental_days($a,$b));$dep=round($total*0.2,2);
  $pdo->prepare('INSERT INTO bookings(user_id,vehicle_id,pickup_date,return_date,pickup_location,total,deposit) VALUES(?,?,?,?,?,?,?)')
      ->execute([user()['id'],$vid,$a,$b,$loc,$total,$dep]);
  $id=$pdo->lastInsertId();$pdo->commit();
  redirect('payment.php?id='.$id);
}catch(PDOException $ex){if($pdo->inTransaction())$pdo->rollBack();flash('Booking failed. Please try again.','err');redirect($back);}
