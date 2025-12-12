<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nàdair Tours Admin</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<ul class="navbar">
    <li><a href="home.php">Nàdair Tours</a></li>
    <li><a href="adminLogin.php" class="right">Admin Login</a></li>
    <li><a href="home.php" class="right">User Home</a></li>
    <li><a href="admin.php" class="right">Admin Home</a></li>
</ul>

<div class="top" style="background-image: url('images/homeBackground.jpg');">
    <div class="intro">
        <h1>Nàdair Tours Admin Section</h1>
        <p>In this section you can manage Tours, Orders, and Bookings.</p>
        <p>This allows you to view all records as well as edit, delete, and create certain items.</p>
    </div>
</div>

<div class="adminBody">
    <div class="toursOptions">
        <h2>Tours Options</h2>
        <br>
        <a href="admin/tourDelete.php">tour delete</a>
        <a href="admin/tourInsert.php">tour insert</a>
        <a href="admin/tourUpdate.php">tour update</a>
        <a href="admin/viewTours.php">view tours</a>
    </div>
    <div class="bookingOptions">
        <h2>Booking Options</h2>
        <br>
        <a href="admin/bookingDelete.php">booking delete</a>
        <a href="admin/bookingUpdate.php">booking update</a>
        <a href="admin/viewBookings.php">view bookings</a>
    </div>
    <div class="ordersOptions">
        <h2>Orders Options</h2>
        <br>
        <a href="admin/orderDelete.php">order delete</a>
        <a href="admin/orderUpdate.php">order update</a>
        <a href ="admin/viewOrders.php">view orders</a>
    </div>
</div>

<footer>
    <a href="#">Back to Top</a>
    <p>nadairtours@mail.com | 01415678910</p>
    <p>© 2025 Nàdair Tours, All Rights Reserved</p>
</footer>
</body>
</html>
