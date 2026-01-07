<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tour View</title>
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
function safePost($conn, $name){
    // if its set in the post array it strips any tags from it and real_escapes it for going into the database, otherwise it returns empty string
    if(!isset($_POST[$name])) return "";
    $value = strip_tags($_POST[$name]);
    $value = str_replace(["\r\n", "\r"], " ", $value);
    return $conn->real_escape_string($value);
}

require '../dbConnection.php';


$id = safePost($conn, "id");
$stage = safePost($conn, "stage");

if($stage === "2"){  // stage 2 show details of the tour
    $sql = "SELECT * FROM `Tours` WHERE `tourID` = $id;";
    $result = $conn->query($sql);  // issue the query
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
    }
    ?>
    <div class="viewTours">
    <table>
        <tr>
            <td>Tour ID: </td>
            <td><?php echo $row['tourID'] ?></td></tr>
        <tr>
            <td>Title: </td>
            <td><?php echo $row['title'] ?></td></tr>
        <tr>
            <td>Short Summary: </td>
            <td><?php echo $row['shortSummary'] ?></td></tr>
        <tr>
            <td>Description: </td>
            <td><?php echo $row['description'] ?></td></tr>
        <tr>
            <td>Image: </td>
            <td><img src='data:image/jpeg;base64, <?php echo base64_encode($row["image"])?>' alt='<?php echo $row["title"]?> image'></td></tr>
        <tr>
            <td>Image 2: </td>
            <td><img src='data:image/jpeg;base64, <?php echo base64_encode($row["image2"])?>' alt='<?php echo $row["title"]?> image 2'></td></tr>
        <tr>
            <td>Image 3: </td>
            <td><img src='data:image/jpeg;base64, <?php echo base64_encode($row["image3"])?>' alt='<?php echo $row["title"]?> image 3'></td></tr>
        <tr>
            <td>Price: </td>
            <td><?php echo $row['price'] ?></td></tr>
        <tr>
            <td>Duration: </td>
            <td><?php echo $row['duration'] ?></td></tr>
        <tr>
            <td>Location: </td>
            <td><?php echo $row['location'] ?></td></tr>
        <tr>
            <td>Background Image: </td>
            <td><img src='data:image/jpeg;base64, <?php echo base64_encode($row["backgroundImage"])?>' alt='<?php echo $row["title"]?> background image'></td></tr>
        <tr>
            <td>Itinerary: </td>
            <td><?php echo $row['itinerary'] ?></td></tr>
    </table>
    </div>
    <?php
}
else{  // stage not set to assume stage 1
    ?>
    <!-- Stage 1 - show the select menu to pick a tour to view -->
    <div class="idSelector">
    <form method="post" action="viewTours.php">
        <input type="hidden" name="stage" value="2"> <!-- when this is submitted it will be stage 2 -->
        <p><label for="id">Choose a tour to view:</label>

            <select name="id" id="id">
                <?php
                $sql = "SELECT * FROM `Tours`;";
                $result = $conn->query($sql);  // issue the query

                if(!$result){
                    die("Query failed ".$conn->error);
                }

                // first output as paragraphs
                if($result->num_rows>0){
                    while($row = $result->fetch_assoc()){  // loops round and give next row of table
                        // keys in the associative array match the columns in the database
                        echo "<option value='".$row["tourID"]."'>".$row["tourID"]." - ".$row["title"]."</option>";
                    }
                }
                ?>
            </select></p>
        <p><input type="submit"></p>
    </form>
    </div>
    <?php
}?>
</body>
</html>
