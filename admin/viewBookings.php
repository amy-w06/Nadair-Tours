<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nàdair Tours Admin</title>
    <link rel="stylesheet" type="text/css" href="../style.css"/>
</head>
<body>
<ul class="navbar">
    <li><a href="../home.php">Nàdair Tours</a></li>
    <li><a href="../adminLogin.php" class="right">Admin Login</a></li>
    <li><a href="../home.php" class="right">User Home</a></li>
    <li><a href="../admin.php" class="right">Admin Home</a></li>
</ul>

<?php
require '../dbConnection.php';
$sql = "SELECT * FROM `Bookings`;";
$result = $conn->query($sql);  // issue the query
if($result->num_rows>0) { ?>
<div class='viewTable'>
    <h1>All Booking Information</h1>
    <table>
        <tr><th>Booking ID</th>
            <th>Total Cost</th>
            <th>Number of Participants</th>
            <th>Date</th>
            <th>Tour ID</th>
            <th>Order ID</th>
        </tr>
        <?php
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>".$row['bookingID']."</td>";
            echo "<td>".$row['totalCost']."</td>";
            echo "<td>".$row['participants']."</td>";
            echo "<td>".$row['dateBeginning']."</td>";
            echo "<td>".$row['tourID']."</td>";
            echo "<td>".$row['orderID']."</td>";
            echo "</tr>";
        }
}
?>
    </table>
</div>
</body>
</html>