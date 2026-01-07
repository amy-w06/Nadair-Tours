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
    $value = str_replace(["\r\n", "\r"], " ", $value);
    return $conn->real_escape_string($value);
}

require '../dbConnection.php';
$sql = "SELECT * FROM `Bookings`;";
$result = $conn->query($sql);  // issue the query
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
}

if(!$result){
    die("Query failed ".$conn->error);
}

$id = safePost($conn, "id");
$stage = safePost($conn, "stage");

if($stage === "3"){  // stage 3 update table for specified booking
    $cost = safePost($conn, "cost");
    $participants = safePost($conn, "participants");
    $date = safePost($conn, "date");
    $tourID = safePost($conn, "tour");

    $stmt = $conn->prepare("UPDATE `Bookings` SET 
            `totalCost`     = ?,
            `participants`  = ?,
            `dateBeginning` = ?,
            `tourID`        = ?
            WHERE `bookingID` = ?");
    $stmt->bind_param("disii", $cost, $participants, $date, $tourID, $id);
    $stmt->execute();

    echo "<div class='idSelector'>";
    echo "<h1>Update for Booking $id successful</h1>";
    echo "<br>";
    echo "<a href='../admin.php' id='cartButton'>Back to Home</a>";
    echo "</div>";
}
else if($stage === "2"){  // stage 2 show the form for user $id


    $sql = "SELECT * FROM `Bookings` WHERE `bookingID` = $id";
    $result = $conn->query($sql);  // issue the query
    if(!$result){
        die("Query failed ".$conn->error);
    }

    // first output as paragraphs
    if($result->num_rows===1){
        while($row = $result->fetch_assoc()){
            ?>
        <div class="inputContainer">
            <p>Updating booking id: <?php echo $id?></p>
            <form action="bookingUpdate.php" method="post" id="updateForm">
                <input type="hidden" name="stage" value="3"> <!-- when this is submitted it will be stage 3 -->
                <input type="hidden" name="id" value="<?php echo $row["bookingID"]?>">
                <table>
                    <tr>
                        <td><label for="cost">Total Cost: </label></td>
                        <td><input name="cost" id="cost" type="number" value="<?php echo $row["totalCost"]?>"><span id="costError"></span></td></tr>
                    <tr>
                        <td><label for="participants">Participants: </label></td>
                        <td><input name="participants" id="participants" type="number" value="<?php echo $row["participants"]?>"><span id="participantsError"></span></td></tr>
                    <tr>
                        <td><label for="date">Date: </label></td>
                        <td><input name="date" id="date" type="date" value="<?php echo $row["dateBeginning"]?>"><span id="dateError"></span></td></tr>
                    <tr>
                        <td><label for="tour">Tour ID: </label></td>
                        <td><input name="tour" id="tour" type="number" min="1" step="1" value="<?php echo $row["tourID"]?>"><span id="tourError"></span></td></tr>
                    <tr>
                        <td></td>
                        <td><input type="submit"></td>
                    </tr>
                </table>
            </form>
        </div>
            <?php
        }
    }?>

    <script>
        function check(event){
            let errs = "";
            const bookCost = document.getElementById("cost");
            const bookPartitipcants = document.getElementById("participants");
            const bookDate = document.getElementById("date");
            const bookTour = document.getElementById("tour");

            const costError = document.getElementById("costError");
            const participantsError = document.getElementById("participantError");
            const dateError = document.getElementById("dateError");
            const tourError = document.getElementById("tourError");

            // reset error displays
            bookCost.style.backgroundColor = "white";  // background colour reset
            bookPartitipcants.style.backgroundColor = "white";
            bookDate.style.backgroundColor = "white";
            bookTour.style.backgroundColor = "white";

            costError.innerHTML = "";  // warning reset
            participantsError.innerHTML = "";
            dateError.innerHTML = "";
            tourError.innerHTML = "";


            if (bookCost.value === "") {
                errs += "• Please ensure 'Total Cost' field has a value.\n";
                bookCost.style.backgroundColor = "pink";
                costError.innerHTML = " ⚠";
            }
            if (bookPartitipcants.value === "") {
                errs += "• Please ensure 'Participants' field has a value.\n";
                bookPartitipcants.style.backgroundColor = "pink";
                participantsError.innerHTML = " ⚠";
            }
            if (bookDate.value === "") {
                errs += "• Please ensure 'Date' field has a value.\n";
                bookDate.style.backgroundColor = "pink";
                dateError.innerHTML = " ⚠";
            }
            if (bookTour.value === "") {
                errs += "• Please ensure 'Tour ID' field has a value and that it is a valid tour ID.\n";
                bookTour.style.backgroundColor = "pink";
                tourError.innerHTML = " ⚠";
            }

            if (errs !== "") {
                window.alert("Please complete the following errors before submission:\n" + errs);
                event.preventDefault();
            }
            return (errs === "");
        }
        document.getElementById("updateForm").addEventListener("submit", check);
    </script>

    <?php
}
else{  // stage not set to assume stage 1
    ?>
    <!-- Stage 1 - show the select menu to pick a tour to edit -->
<div class="idSelector">
    <form method="post" action="bookingUpdate.php">
        <input type="hidden" name="stage" value="2"> <!-- when this is submitted it will be stage 2 -->
        <p><label for="id">Choose an booking to edit:</label>

            <select name="id" id="id">
                <?php
                $sql = "SELECT * FROM `Bookings`;";
                $result = $conn->query($sql);  // issue the query

                if(!$result){
                    die("Query failed ".$conn->error);
                }

                // first output as paragraphs
                if($result->num_rows>0){
                    while($row = $result->fetch_assoc()){  // loops round and give next row of table
                        // keys in the associative array match the columns in the database
                        echo "<option value='".$row["bookingID"]."'>".$row["bookingID"]." - Tour ID: ".$row["tourID"].", Order ID: ".$row["orderID"]. "</option>";
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
