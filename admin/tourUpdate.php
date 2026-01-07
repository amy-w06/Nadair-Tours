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
    $value = str_replace(["\r\n", "\r", "\n"], " ", $value);  // new lines weren't working so replace them with spaces
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

if($stage === "3") {  // stage 3 update table for specified tour
    $title = safePost($conn, "title");
    $shortSummary = safePost($conn, "shortSummary");
    $description = safePost($conn, "description");
    $price = safePost($conn, "price");
    $duration = safePost($conn, "duration");
    $location = safePost($conn, "location");
    $itinerary = safePost($conn, "itinerary");

    // if there's a new image check it's okay size and update
    // https://www.geeksforgeeks.org/php/php-_files-array-http-file-upload-variables/
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === 0) {  // IMAGE 1
        if ($_FILES["image"]["size"] > 4294967295) {  // this is the max size of large blob (the way it's stored on the database)
            die('Exceeded filesize limit.');
        } else {
            $image = file_get_contents($_FILES["image"]["tmp_name"]);
        }
    } else {  // no new image uploaded, take the image to be one in the database
        $sql = "SELECT * FROM `Tours` WHERE `tourID` = $id;";
        $result = $conn->query($sql);  // issue the query
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }
        $image = $row["image"];
    }

    if (isset($_FILES["image2"]) && $_FILES["image2"]["error"] === 0) {  // IMAGE 2
        if ($_FILES["image2"]["size"] > 4294967295) {  // this is the max size of large blob (the way it's stored on the database)
            die('Exceeded filesize limit.');
        } else {
            $image2 = file_get_contents($_FILES["image2"]["tmp_name"]);
        }
    } else {  // no new image uploaded, take the image to be one in the database
        $sql = "SELECT * FROM `Tours` WHERE `tourID` = $id;";  // this is repeated incase previous images were updated so query was never run
        $result = $conn->query($sql);  // issue the query
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }
        $image2 = $row["image2"];
    }

    if (isset($_FILES["image3"]) && $_FILES["image3"]["error"] === 0) {  // IMAGE 3
        if ($_FILES["image3"]["size"] > 4294967295) {  // this is the max size of large blob (the way it's stored on the database)
            die('Exceeded filesize limit.');
        } else {
            $image3 = file_get_contents($_FILES["image3"]["tmp_name"]);
        }
    } else {  // no new image uploaded, take the image to be one in the database
        $sql = "SELECT * FROM `Tours` WHERE `tourID` = $id;";  // this is repeated incase previous images were updated so query was never run
        $result = $conn->query($sql);  // issue the query
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }
        $image3 = $row["image3"];
    }

    if (isset($_FILES["backgroundImage"]) && $_FILES["backgroundImage"]["error"] === 0) {  // BACKGROUND IMAGE
        if ($_FILES["backgroundImage"]["size"] > 4294967295) {  // this is the max size of large blob (the way it's stored on the database)
            die('Exceeded filesize limit.');
        } else {
            $bgImage = file_get_contents($_FILES["backgroundImage"]["tmp_name"]);
        }
    } else {  // no new image uploaded, take the image to be one in the database
        $sql = "SELECT * FROM `Tours` WHERE `tourID` = $id;";
        $result = $conn->query($sql);  // issue the query
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
        }
        $bgImage = $row["backgroundImage"];
    }

    echo "<p>Update for tour $id</p>";

    // https://www.w3schools.com/php/php_mysql_prepared_statements.asp, allows for images
    $stmt = $conn->prepare("UPDATE `Tours` SET 
            `title`        = ?,
            `shortSummary` = ?,
            `description`  = ?,
            `image`        = ?,
            `image2`        = ?,
            `image3`        = ?,
            `price`        = ?,
            `duration`     = ?,
            `location`     = ?,
            `backgroundImage` = ?,
            `itinerary`    = ?
            WHERE `tourID` = ?");
    $stmt->bind_param("sssbbbdisbsi", $title, $shortSummary, $description, $image, $image2, $image3, $price, $duration, $location, $bgImage, $itinerary, $id);
    $stmt->send_long_data(3, $image);  // https://planet.mysql.com/entry/?id=22296
    $stmt->send_long_data(4, $image2);
    $stmt->send_long_data(5, $image3);
    $stmt->send_long_data(9, $bgImage);
    $stmt->execute();

    echo "<div class='idSelector'>";
    echo "<h1>Update for Tour $id successful</h1>";
    echo "<br>";
    echo "<a href='../admin.php' id='cartButton'>Back to Home</a>";
    echo "</div>";
}
else if($stage === "2"){  // stage 2 show the form for user $id

    $sql = "SELECT * FROM `Tours` WHERE `tourID` = $id";
    $result = $conn->query($sql);  // issue the query
    if(!$result){
        die("Query failed ".$conn->error);
    }

    // first output as paragraphs
    if($result->num_rows===1){
        while($row = $result->fetch_assoc()){
            ?>
        <div class="inputContainer">
            <h1>Updating tour id: <?php echo $id?></h1>
            <form action="tourUpdate.php" method="post" id="updateForm" enctype="multipart/form-data"> <!-- enctype from https://www.geeksforgeeks.org/html/what-does-enctypemultipart-form-data-mean-in-an-html-form/ -->
                <input type="hidden" name="stage" value="3"> <!-- when this is submitted it will be stage 3 -->
                <input type="hidden" name="id" value="<?php echo $row["tourID"]?>">
                <table>
                <tr>
                    <td><label for="title">Tour Title: </label></td>
                    <td><input name="title" id="title" type="text" value="<?php echo $row["title"]?>"><span id="titleError"></span></td></tr>
                <tr>
                    <td><label for="shortSummary">Short Summary: </label></td>
                    <td><textarea name="shortSummary" id="shortSummary" cols="50" rows="5"><?php echo $row["shortSummary"]?></textarea><span id="summaryError"></span></td></tr>
                <tr>
                    <td><label for="description">Description: </label></td>
                    <td><textarea name="description" id="description" cols="100" rows="10"><?php echo $row["description"]?></textarea><span id="descriptionError"></span></td></tr>
                <tr>
                    <td><label for="image">Image: </label></td>
                    <td><input name="image" id="image" type="file" accept="image/*"></td></tr>
                <tr>
                    <td><label for="image2">Image 2: </label></td>
                    <td><input name="image2" id="image2" type="file" accept="image/*"></td></tr>
                <tr>
                    <td><label for="image3">Image 3: </label></td>
                    <td><input name="image3" id="image3" type="file" accept="image/*"></td></tr>
                <tr>
                    <td><label for="price">Price: </label></td>
                    <td><input name="price" id="price" type="number" min="0.01" step="0.01" value="<?php echo $row["price"]?>"><span id="priceError"></span></td></tr>
                <tr>
                    <td><label for="duration">Duration: </label></td>
                    <td><input name="duration" id="duration" type="number" min="1" value="<?php echo $row["duration"]?>"><span id="durationError"></span></td></tr>
                <tr>
                    <td><label for="location">Location: </label></td>
                    <td><input name="location" id="location" type="text" value="<?php echo $row["location"]?>"><span id="locationError"></span></td></tr>
                <tr>
                    <td><label for="backgroundImage">Background Image: </label></td>
                    <td><input name="backgroundImage" id="backgroundImage" type="file" accept="image/*"></td></tr>
                <tr>
                    <td><label for="itinerary">Itinerary: </label></td>
                    <td><textarea name="itinerary" id="itinerary" cols="100" rows="10"><?php echo $row["itinerary"]?></textarea><span id="itineraryError"></span></td>
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
            const tourTitle = document.getElementById("title");
            const tourSummary = document.getElementById("shortSummary");
            const tourDesc = document.getElementById("description");
            const tourPrice = document.getElementById("price");
            const tourDuration = document.getElementById("duration");
            const tourLocation = document.getElementById("location");
            const tourItinerary = document.getElementById("itinerary");

            const titleError = document.getElementById("titleError");
            const summaryError = document.getElementById("summaryError");
            const descError = document.getElementById("descriptionError");
            const priceError = document.getElementById("priceError");
            const durationError = document.getElementById("durationError");
            const locationError = document.getElementById("locationError");
            const itineraryError = document.getElementById("itineraryError");

            // reset error displays
            tourTitle.style.backgroundColor = "white";  // background colour reset
            tourSummary.style.backgroundColor = "white";
            tourDesc.style.backgroundColor = "white";
            tourPrice.style.backgroundColor = "white";
            tourDuration.style.backgroundColor = "white";
            tourLocation.style.backgroundColor = "white";
            tourItinerary.style.backgroundColor = "white";

            titleError.innerHTML = "";  // warning reset
            summaryError.innerHTML = "";
            descError.innerHTML = "";
            priceError.innerHTML = "";
            durationError.innerHTML = "";
            locationError.innerHTML = "";
            itineraryError.innerHTML = "";

            if (tourTitle.value === "") {
                errs += "• Please ensure 'Title' field has a value.\n";
                tourTitle.style.backgroundColor = "pink";
                titleError.innerHTML = " ⚠";
            }
            if (tourSummary.value === "") {
                errs += "• Please ensure 'Short Summary' field has a value.\n";
                tourSummary.style.backgroundColor = "pink";
                summaryError.innerHTML = " ⚠";
            }
            if (tourDesc.value === "") {
                errs += "• Please ensure 'Description' field has a value.\n";
                tourDesc.style.backgroundColor = "pink";
                descError.innerHTML = " ⚠";
            }
            if (tourPrice.value === "") {
                errs += "• Please ensure 'Price' field has a value.\n";
                tourPrice.style.backgroundColor = "pink";
                priceError.innerHTML = " ⚠";
            }
            if (tourDuration.value === "") {
                errs += "• Please ensure 'Duration' field has a value.\n";
                tourDuration.style.backgroundColor = "pink";
                durationError.innerHTML = " ⚠";
            }
            if (tourLocation.value === "") {
                errs += "• Please ensure 'Location' field has a value.\n";
                tourLocation.style.backgroundColor = "pink";
                locationError.innerHTML = " ⚠";
            }
            if (tourItinerary.value === "") {
                errs += "• Please ensure 'Itinerary' field has a value.\n";
                tourItinerary.style.backgroundColor = "pink";
                itineraryError.innerHTML = " ⚠";
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
    <form method="post" action="tourUpdate.php">
        <input type="hidden" name="stage" value="2"> <!-- when this is submitted it will be stage 2 -->
        <p><label for="id">Choose a tour to edit:</label>

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
        <br>
        <p><input type="submit" id="cartButton"></p>
    </form>
</div>
    <?php
}?>
</body>
</html>