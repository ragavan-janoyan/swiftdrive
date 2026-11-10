<?php
session_start();
define('BASE','/swiftdrive');
define('DB_HOST','localhost'); define('DB_NAME','swiftdrive'); define('DB_USER','root'); define('DB_PASS','');
try {
  $pdo=new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',DB_USER,DB_PASS,[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
} catch(PDOException $ex){ die('Database connection failed. Check WAMP is running and database.sql was imported.'); }

function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function user(){return $_SESSION['user']??null;}
function redirect($p){header('Location: '.BASE.'/'.$p);exit;}
function flash($m,$t='ok'){$_SESSION['flash']=[$m,$t];}
function money($n){return 'Rs. '.number_format((float)$n,2);}
function csrf_field(){if(empty($_SESSION['csrf']))$_SESSION['csrf']=bin2hex(random_bytes(16));return '<input type="hidden" name="csrf" value="'.$_SESSION['csrf'].'">';}
function csrf_check(){if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){http_response_code(400);die('Invalid request token.');}}
function require_login(){if(!user()){flash('Please log in first.','err');redirect('login.php');}}
function require_role($roles){require_login();if(!in_array(user()['role'],$roles,true)){http_response_code(403);die('Access denied.');}}
function icon($t){return ['economy'=>'ð','suv'=>'ð','van'=>'ð','luxury'=>'ðï¸'][$t]??'ð';}
// Weekly rate applies per full 7 days, daily rate for the remainder
function calc_price($v,$days){return floor($days/7)*$v['weekly_rate']+($days%7)*$v['daily_rate'];}
function rental_days($a,$b){return max(1,(int)(new DateTime($a))->diff(new DateTime($b))->days);}
// A vehicle is unavailable if an active booking overlaps the requested dates
function is_booked($pdo,$vid,$from,$to){
  $s=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE vehicle_id=? AND status IN('pending','confirmed','picked_up') AND ?<return_date AND ?>pickup_date");
  $s->execute([$vid,$from,$to]);return $s->fetchColumn()>0;
}
// Photo for a vehicle: uses the Image URL field if set, otherwise looks for a file in
// assets/img/vehicles/ named after the vehicle (e.g. "Alto 800" -> alto-800.jpg)
function vehicle_img($v){
  if(!empty($v['image_url'])) return $v['image_url'];
  $slug=trim(preg_replace('/[^a-z0-9]+/','-',strtolower($v['name'])),'-');
  foreach(['jpg','jpeg','png','webp'] as $x){
    if(is_file(__DIR__.'/../assets/img/vehicles/'.$slug.'.'.$x)) return BASE.'/assets/img/vehicles/'.$slug.'.'.$x;
  }
  return '';
}
function vehicle_card($v){
  $src=vehicle_img($v);
  $img=$src?'<img src="'.e($src).'" alt="'.e($v['name']).'" loading="lazy">':'<div class="ph">'.icon($v['type']).'</div>';
  $q=isset($_GET['pickup'])?'&pickup='.urlencode($_GET['pickup']).'&return='.urlencode($_GET['return']??''):'';
  return '<article class="card">'.$img.'<div class="card-b"><span class="tag">'.e(ucfirst($v['type'])).'</span><h3>'.e($v['brand'].' '.$v['name']).'</h3>
  <p class="muted">'.e($v['transmission']).' Â· '.e($v['fuel']).' Â· '.(int)$v['seats'].' seats</p>
  <p class="price">'.money($v['daily_rate']).' <small>/day</small> Â· '.money($v['weekly_rate']).' <small>/week</small></p>
  <a class="btn" href="'.BASE.'/vehicle.php?id='.(int)$v['id'].$q.'">View &amp; Book</a></div></article>';
}