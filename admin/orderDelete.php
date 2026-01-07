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
function safePost($conn, $name){
    // if its set in the post array it strips any tags from it and real_escapes it for going into the database, otherwise it returns empty string
    if(!isset($_POST[$name])) return "";
    $value = strip_tags($_POST[$name]);
    $value = str_replace(["\r\n", "\r", "\n"], " ", $value);
    return $conn->real_escape_string($value);
}

require '../dbConnection.php';
$sql = "SELECT * FROM `Tours`;";
$result = $conn->query($sql);  // issue the query
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
}

if(!$result){
    die("Query failed ".$conn->error);
}

$id = safePost($conn, "id");
$stage = safePost($conn, "stage");

if($stage === "3"){  // stage 2 update table for specified tour
    $sql = "DELETE FROM `Orders` WHERE `orderID` = $id;";

    if ($conn->query($sql)) {
        echo "<script>console.log('Record deleted successfully');</script>";
    } else {
        echo "<script>console.log('error deleting record');</script>";
    }

    echo "<div class='idSelector'>";
    echo "<h1>Deletion of Order $id successful</h1>";
    echo "<br>";
    echo "<a href='../admin.php' id='cartButton'>Back to Home</a>";
    echo "</div>";
}
else if($stage === "2"){  // stage 2 confirm the deletion of the tour

    $sql = "SELECT * FROM `Orders` WHERE `orderID` = $id;";
    $result = $conn->query($sql);  // issue the query
    if(!$result){
        die("Query failed ".$conn->error);
    }
    $row = $result->fetch_assoc()
    ?>

    <div class="inputContainer">
    <h1>Confirm deletion of order with id: <?php echo $id?></h1>
    <form action="orderDelete.php" method="post" id="deleteForm">
        <input type="hidden" name="stage" value="3">  <!-- will be stage 3 when submitted -->
        <input type="hidden" name="id" value="<?php echo $row["orderID"]?>">
        <p>NOTE: By deleting this order you are affecting the booking(s) that are a part of this. All bookings under this id will be deleted.</p>
        <p>By confirming deletion of the order you acknowledge this is an irreversible action.</p>
        <table>
            <tr>
                <td><label for="confirm">Confirm deletion by typing "confirm": </label></td>
                <td><input name="confirm" id="confirm" type="text"><span id="confirmError"></span>
                <td><input type="submit"></td></tr>
        </table>
    </form>
    </div>

    <script>
        function check(event){
            let errs = "";
            const confirm = document.getElementById("confirm");
            const confirmError = document.getElementById("confirmError");

            confirm.style.backgroundColor = "white";
            confirmError.innerHTML = "";

            if (confirm.value != "confirm") {
                errs += "• To confirm deletion type 'confirm' into the text field.\n";
                confirm.style.backgroundColor = "pink";
                confirmError.innerHTML = " ⚠";
            }
            if (errs !== "") {
                window.alert("Please complete the following errors before submission:\n" + errs);
                event.preventDefault();
            }
            return (errs === "");
        }
        document.getElementById("deleteForm").addEventListener("submit", check);
    </script>
    <?php
}
else{  // stage not set to assume stage 1
    ?>
    <!-- Stage 1 - show the select menu to pick a tour to delete -->
<div class="idSelector">
    <form method="post" action="orderDelete.php">
        <input type="hidden" name="stage" value="2"> <!-- when this is submitted it will be stage 2 -->
        <p><label for="id">Choose a order to delete:</label>

            <select name="id" id="id">
                <?php
                $sql = "SELECT * FROM `Orders`;";
                $result = $conn->query($sql);  // issue the query

                if(!$result){
                    die("Query failed ".$conn->error);
                }

                // first output as paragraphs
                if($result->num_rows>0){
                    while($row = $result->fetch_assoc()){  // loops round and give next row of table
                        // keys in the associative array match the columns in the database
                        echo "<option value='".$row["orderID"]."'>".$row["orderID"]." - ".$row["customerName"]."</option>";
                    }
                }
                ?>
            </select></p>
        <br>
        <p><input type="submit" id="cartButton"></p>
    </form>
</div>
    <?php
}?>
</body>
</html>