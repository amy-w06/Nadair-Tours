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


function safePost($conn, $name){
    // if its set in the post array it strips any tags from it and real_escapes it for going into the database, otherwise it returns empty string
    if(!isset($_POST[$name])) return "";
    $value = strip_tags($_POST[$name]);
    $value = str_replace(["\r\n", "\r", "\n"], " ", $value);
    return $conn->real_escape_string($value);
}

$id = safePost($conn, "id");
$stage = safePost($conn, "stage");

if($stage == 2) {
    $title = safePost($conn, "title");
    $shortSummary = safePost($conn, "shortSummary");
    $description = safePost($conn, "description");
    $price = safePost($conn, "price");
    $duration = safePost($conn, "duration");
    $location = safePost($conn, "location");
    $itinerary = safePost($conn, "itinerary");

    $image = file_get_contents($_FILES["image"]["tmp_name"]);
    $image2 = file_get_contents($_FILES["image2"]["tmp_name"]);
    $image3 = file_get_contents($_FILES["image3"]["tmp_name"]);
    $bgImage = file_get_contents($_FILES["backgroundImage"]["tmp_name"]);

    $null = null;
    $stmt = $conn->prepare("INSERT INTO `Tours` (`tourID`, `title`, `shortSummary`, `description`, `image`, `image2`, `image3`, `price`, `duration`, `location`, `backgroundImage`, `itinerary`)
                                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $stmt->bind_param("isssbbbdisbs", $null, $title, $shortSummary, $description, $image, $image2, $image3, $price, $duration, $location, $bgImage, $itinerary);
    $stmt->send_long_data(4, $image);  // https://planet.mysql.com/entry/?id=22296
    $stmt->send_long_data(5, $image2);
    $stmt->send_long_data(6, $image3);
    $stmt->send_long_data(10, $bgImage);
    $stmt->execute();

    echo "<script>console.log('inserted Successfully')</script>";
    echo "<h1>Tour Inserted Successfully</h1>";
}
?>

<div class="inputContainer">
    <h1>Create New Tour</h1>
    <form id="insertForm" action="tourInsert.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="stage" value="2">
        <table>
            <tr>
                <td><label for="title">Tour Title: </label></td>
                <td><input name="title" id="title" type="text"><span id="titleError"></span></td></tr>
            <tr>
                <td><label for="shortSummary">Short Summary: </label></td>
                <td><textarea name="shortSummary" id="shortSummary" cols="50" rows="5"></textarea><span id="summaryError"></span></td></tr>
            <tr>
                <td><label for="description">Description: </label></td>
                <td><textarea name="description" id="description" cols="100" rows="10"></textarea><span id="descriptionError"></span></td></tr>
            <tr>
                <td><label for="image">Image: </label></td>
                <td><input name="image" id="image" type="file" accept="image/*"><span id="imageError"></span></td></tr>
            <tr>
                <td><label for="image2">Image 2: </label></td>
                <td><input name="image2" id="image2" type="file" accept="image/*"><span id="image2Error"></span></td></tr>
            <tr>
                <td><label for="image3">Image 3: </label></td>
                <td><input name="image3" id="image3" type="file" accept="image/*"><span id="image3Error"></span></td></tr>
            <tr>
                <td><label for="price">Price: </label></td>
                <td><input name="price" id="price" type="number" min="0.01" step="0.01"><span id="priceError"></span></td></tr>
            <tr>
                <td><label for="duration">Duration: </label></td>
                <td><input name="duration" id="duration" type="number" min="1"><span id="durationError"></span></td></tr>
            <tr>
                <td><label for="location">Location: </label></td>
                <td><input name="location" id="location" type="text"><span id="locationError"></span></td></tr>
            <tr>
                <td><label for="backgroundImage">Background Image: </label></td>
                <td><input name="backgroundImage" id="backgroundImage" type="file" accept="image/*"><span id="bgImageError"></span></td></tr>
            <tr>
                <td><label for="itinerary">Itinerary: </label></td>
                <td><textarea name="itinerary" id="itinerary" cols="100" rows="10"></textarea><span id="itineraryError"></span></td>
            <tr>
                <td></td>
                <td><input type="submit"></td>
            </tr>
        </table>
    </form>
</div>

<script>
    function check(event){
        let errs = "";
        const tourTitle = document.getElementById("title");
        const tourSummary = document.getElementById("shortSummary");
        const tourDesc = document.getElementById("description");
        const tourImage = document.getElementById("image");
        const tourImage2 = document.getElementById("image2");
        const tourImage3 = document.getElementById("image3");
        const tourPrice = document.getElementById("price");
        const tourDuration = document.getElementById("duration");
        const tourLocation = document.getElementById("location");
        const tourBgImage = document.getElementById("backgroundImage");
        const tourItinerary = document.getElementById("itinerary");

        const titleError = document.getElementById("titleError");
        const summaryError = document.getElementById("summaryError");
        const descError = document.getElementById("descriptionError");
        const imageError = document.getElementById("imageError");
        const imageError2 = document.getElementById("image2Error");
        const imageError3 = document.getElementById("image3Error");
        const priceError = document.getElementById("priceError");
        const durationError = document.getElementById("durationError");
        const locationError = document.getElementById("locationError");
        const bgError = document.getElementById("bgImageError");
        const itineraryError = document.getElementById("itineraryError");

        // reset error displays
        tourTitle.style.backgroundColor = "white";  // background colour reset
        tourSummary.style.backgroundColor = "white";
        tourDesc.style.backgroundColor = "white";
        tourImage.style.backgroundColor = "white";
        tourImage2.style.backgroundColor = "white";
        tourImage3.style.backgroundColor = "white";
        tourPrice.style.backgroundColor = "white";
        tourDuration.style.backgroundColor = "white";
        tourLocation.style.backgroundColor = "white";
        tourBgImage.style.backgroundColor = "white";
        tourItinerary.style.backgroundColor = "white";

        titleError.innerHTML = "";  // warning reset
        summaryError.innerHTML = "";
        descError.innerHTML = "";
        imageError.innerHTML = "";
        imageError2.innerHTML = "";
        imageError3.innerHTML = "";
        priceError.innerHTML = "";
        durationError.innerHTML = "";
        locationError.innerHTML = "";
        bgError.innerHTML = "";
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
        if (tourImage.value === "") {
            errs += "• Please ensure 'Image' field has a value.\n";
            tourImage.style.backgroundColor = "pink";
            imageError.innerHTML = " ⚠";
        }
        if (tourImage2.value === "") {
            errs += "• Please ensure 'Image 2' field has a value.\n";
            tourImage2.style.backgroundColor = "pink";
            imageError2.innerHTML = " ⚠";
        }
        if (tourImage3.value === "") {
            errs += "• Please ensure 'Image 3' field has a value.\n";
            tourImage3.style.backgroundColor = "pink";
            imageError3.innerHTML = " ⚠";
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
        if (tourBgImage.value === "") {
            errs += "• Please ensure 'Background Image' field has a value.\n";
            tourBgImage.style.backgroundColor = "pink";
            bgError.innerHTML = " ⚠";
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
    document.getElementById("insertForm").addEventListener("submit", check);
</script>
</body>
</html>