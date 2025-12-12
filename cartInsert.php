<?php
session_start();

function safePost($conn, $name){
    // if its set in the post array it strips any tags from it and real_escapes it for going into the database, otherwise it returns empty string
    return isset($_POST[$name])?$conn->real_escape_string(strip_tags($_POST[$name])):"";
}

require 'dbConnection.php';  // connect to the database using external file
$sql = "SELECT * FROM `Bookings`;";
$result = $conn->query($sql);  // issue the query

if (!$result) {
    die("Query failed " . $conn->error);
}

$name = safePost($conn, "customerName");
$email = safePost($conn, "customerEmail");
$phone = safePost($conn, "customerPhone");
$orderCost = safePost($conn, "totalCartCost");

// insert into orders table
$sql = "INSERT INTO `Orders` (`orderID`, `customerName`, `customerEmail`, `customerPhone`, `totalCost`)".
    "VALUES (NULL, '$name', '$email', '$phone', '$orderCost');";

// for orderid in bookings table
if($conn->query($sql)){
    $lastID = $conn->insert_id;  // from w3schools
}

// goes through every item in the session array and inserts into booking table
foreach ($_SESSION["cart"] as $id=>$tour){
    $bookingCost = $_SESSION["cart"][$id]["cost"];
    $participants = $_SESSION["cart"][$id]["participants"];
    $date = $_SESSION["cart"][$id]["date"];

    $sql = "INSERT INTO `Bookings` (`bookingID`, `totalCost`, `participants`, `dateBeginning`, `tourID`, `orderID`)".
        "VALUES (NULL, '$bookingCost', '$participants', '$date', '$id', '$lastID');";

    if($conn->query($sql)) {
        echo "<script>console.log('Insert successful') </script>";
    } else {
        echo "<script>console.log('Insert Unsuccessful')</script>";
    }

    unset($_SESSION["cart"][$id]);  // remove it from session array (and cart)
}
// for html
$sql = "SELECT * FROM `Tours` LIMIT 4;";
$result = $conn->query($sql);  // issue the query
if(!$result){
    die("Query failed ".$conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nàdair Tours</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<ul class="navbar">
    <li><a href="home.php">Nàdair Tours</a></li>
    <li><a href="adminLogin.php" class="right">Admin Login</a></li>
    <li><a href="cart.php" class="right">Cart</a></li>
    <li><a href="tours.php" class="right">Tours</a></li>
    <li><a href="home.php" class="right">Home</a></li>
</ul>

<div class="top" style="background-image:url('images/cartBackground.jpg');">
    <div class="intro">
        <h1>Tour Booked Successfully!</h1>
        <?php
        echo "<p>Thank you for your order $name!</p>";
        echo "<p style='padding-bottom: 2vh'>Your order number is $lastID.</p>";
        ?>
        <a href="home.php" class="tourButton">Go to Home</a>
        <a href="tours.php" class="tourButton">View More Tours</a>
    </div>
</div>

<div class="information">
    <p>By purchasing your tours with Nàdair Tours you are joining the movement to make tourism in Scotland more eco-friendly and ethical.</p>
    <br>
    <p>We are committed to proving that experiences can be both fun and sustainable and we hope you enjoy what you have booked with us!</p>
    <br>
    <p>If you wish to read more about how we align ourselves with the UN Sustainable Development Goals you can click the button below to find out.</p>
    <br>
    <a href="home.php#aboutUs" class="tourButton">Learn More</a>
</div>

<footer>
    <a href="#">Back to Top</a>
    <p>nadairtours@mail.com | 01415678910</p>
    <p>© 2025 Nàdair Tours, All Rights Reserved</p>
</footer>
</body>
</html>


