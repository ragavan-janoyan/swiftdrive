<?php $title='Pay deposit'; require_once 'includes/config.php'; require_login();
$s=$pdo->prepare("SELECT b.*,v.name,v.brand FROM bookings b JOIN vehicles v ON v.id=b.vehicle_id WHERE b.id=? AND b.user_id=?");
$s->execute([(int)($_GET['id']??0),user()['id']]);$b=$s->fetch();
if(!$b||$b['status']==='cancelled'){flash('Booking not found.','err');redirect('my_bookings.php');}
$paid=$pdo->prepare("SELECT COUNT(*) FROM payments WHERE booking_id=? AND type='deposit' AND status='success'");$paid->execute([$b['id']]);
if($paid->fetchColumn()){flash('Deposit already paid.');redirect('my_bookings.php');}
function luhn($n){$s=0;$alt=false;for($i=strlen($n)-1;$i>=0;$i--){$d=(int)$n[$i];if($alt){$d*=2;if($d>9)$d-=9;}$s+=$d;$alt=!$alt;}return $s%10===0;}
$err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  csrf_check();
  $num=preg_replace('/\D/','',$_POST['card']??'');$exp=$_POST['expiry']??'';$cvv=$_POST['cvv']??'';
  if(!preg_match('/^\d{13,16}$/',$num)||!luhn($num))$err='Invalid card number.';
  elseif(!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/',$exp,$m)||mktime(0,0,0,$m[1]+1,1,2000+$m[2])<time())$err='Card has expired or expiry is invalid (MM/YY).';
  elseif(!preg_match('/^\d{3,4}$/',$cvv))$err='Invalid CVV.';
  else{
    // SIMULATED gateway: card 4000 0000 0000 0002 always declines (to demonstrate failed payment handling)
    $ok=($num!=='4000000000000002');$ref='SD'.strtoupper(bin2hex(random_bytes(4)));
    $pdo->prepare("INSERT INTO payments(booking_id,amount,type,status,reference) VALUES(?,?,'deposit',?,?)")->execute([$b['id'],$b['deposit'],$ok?'success':'failed',$ref]);
    if($ok){flash("Deposit paid (ref $ref). Staff will confirm your booking shortly.");redirect('my_bookings.php');}
    $err='Payment declined by the bank. No money was taken. Please try another card.';
  }
}
require 'includes/header.php'; ?>
<div class="narrow"><h1>Pay deposit</h1>
<div class="box"><b><?= e($b['brand'].' '.$b['name']) ?></b><br><?= e($b['pickup_date']) ?> → <?= e($b['return_date']) ?><br>
Total: <?= money($b['total']) ?><br><b>Deposit due now: <?= money($b['deposit']) ?></b></div>
<?php if($err) echo '<div class="flash err">'.e($err).'</div>'; ?>
<form class="box stack" method="post" autocomplete="off"><?= csrf_field() ?>
<div><label>Card number</label><input id="card" name="card" placeholder="4242 4242 4242 4242" required></div>
<div><label>Expiry (MM/YY)</label><input name="expiry" placeholder="12/28" required maxlength="5"></div>
<div><label>CVV</label><input name="cvv" type="password" maxlength="4" required></div>
<button class="btn">Pay <?= money($b['deposit']) ?></button>
<small>Demo gateway: use 4242 4242 4242 4242 to succeed, 4000 0000 0000 0002 to simulate a declined payment. Card details are never stored.</small></form></div>
<?php require 'includes/footer.php';
