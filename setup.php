<?php
// Run ONCE in browser: http://localhost/swiftdrive/setup.php  (then delete this file)
require_once __DIR__.'/includes/config.php';
$users=[['Admin','admin@swiftdrive.lk','Admin@123','admin'],['Staff Member','staff@swiftdrive.lk','Staff@123','staff'],['Demo Customer','demo@swiftdrive.lk','Demo@123','customer']];
$st=$pdo->prepare("INSERT IGNORE INTO users(name,email,password,role,phone) VALUES(?,?,?,?, '0771234567')");
foreach($users as $u){$st->execute([$u[0],$u[1],password_hash($u[2],PASSWORD_DEFAULT),$u[3]]);}
echo "<h3>Setup complete</h3><p>admin@swiftdrive.lk / Admin@123<br>staff@swiftdrive.lk / Staff@123<br>demo@swiftdrive.lk / Demo@123</p><p><b>Delete setup.php now.</b></p>";
