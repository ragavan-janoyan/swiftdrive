<?php require_role(['staff','admin']); ?>
<h1>Staff panel</h1><div class="subnav">
<a class="btn sm alt" href="index.php">Dashboard</a><a class="btn sm alt" href="bookings.php">Bookings</a><a class="btn sm alt" href="vehicles.php">Vehicles</a><a class="btn sm alt" href="inquiries.php">Inquiries</a>
<?php if(user()['role']==='admin'): ?><a class="btn sm alt" href="users.php">Users</a><?php endif; ?></div>
