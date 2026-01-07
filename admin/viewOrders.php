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
$sql = "SELECT * FROM `Orders`;";
$result = $conn->query($sql);  // issue the query
if($result->num_rows>0) { ?>
    <div class='viewTable'>
        <h1>All Order Information</h1>
    <table>
    <tr><th>OrderID</th>
        <th>Customer Name</th>
        <th>Customer Email</th>
        <th>Customer Phone Number</th>
        <th>Order Cost</th>
    </tr>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row['orderID']."</td>";
        echo "<td>".$row['customerName']."</td>";
        echo "<td>".$row['customerEmail']."</td>";
        echo "<td>".$row['customerPhone']."</td>";
        echo "<td>".$row['totalCost']."</td>";
        echo "</tr>";
    }
}
?>
    </table>
    </div>
</body>
</html>