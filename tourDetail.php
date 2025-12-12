<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nàdair Tours</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<?php
require 'dbConnection.php';  // connect to the database using external file
if (isset($_GET["page"])){  // get the number of the page else it is 1
    $pageNum = $_GET["page"];
}
else{
    $pageNum=1;
}
$sql = "SELECT * FROM `Tours` WHERE tourID = $pageNum;";
$result = $conn->query($sql);  // issue the query
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
}
?>
<ul class="navbar">
    <li><a href="home.php">Nàdair Tours</a></li>
    <li><a href="adminLogin.php" class="right">Admin Login</a></li>
    <li><a href="cart.php" class="right">Cart</a></li>
    <li><a href="tours.php" class="right">Tours</a></li>
    <li><a href="home.php" class="right">Home</a></li>
</ul>
<!-- https://stackoverflow.com/questions/20714163/proper-header-php-mysql-blob-display-image -->
<div class="top" style="background-image:url('data:image/jpeg;base64,<?php echo base64_encode( $row['backgroundImage'] );?>');"> <!-- THIS IS VERY MESSY, SORT -->
    <div class="intro">
        <h1><?php echo $row["title"] ?></h1>
        <p><?php
            $length = $row["duration"];
            if($length == 1){  // if it's 1 day
                echo $length." day |&nbsp;";
            } else {  // if it's multiple days
                echo " ".$length." days |&nbsp;";
            }
            echo $row["location"]; ?></p>
    </div>
</div>
<div class="detailsBody">
    <div class="mainDescription">
        <h1>Overview: </h1>
        <p><?php echo nl2br($row["description"]); ?></p>  <!-- nl2br changes new lines on database to a br html -->
    </div>
    <div class="tourDetails">
        <h1>Tour Details:</h1>
        <br>
        <h3>Location</h3>
        <p><?php echo $row["location"]; ?></p>
        <br>
        <h3>Duration</h3>
        <p><?php
            $length = $row["duration"];
            if($length == 1){  // if it's 1 day
                echo $length." day";
            } else {  // if it's multiple days
                echo " ".$length." days";
            } ?>
        </p>
        <br>
        <h3>Price</h3>
        <p>£<?php echo $row["price"]."pp"; ?></p>
        <br>
        <div class="detailsButtonContainer">
            <form method="post">
                <input type="hidden" name="tourID" value = "<?php echo $row['tourID'] ?>">
                <button type="submit" id="detailsCartButton" name="addCart">Add to Cart</button>
                <br>
                <a href='bookingForm.php?page=<?php echo $row['tourID'] ?>' id='detailsBookButton'>Book Now</a>
            </form>
        </div>
    </div>

    <div class="itinerary">
        <h1>Itinerary: </h1>
        <p><?php echo nl2br($row["itinerary"]); ?></p>
    </div>

    <div class="gallery"> <!-- https://www.w3schools.com/howto/howto_js_slideshow_gallery.asp -->
        <h1>Gallery: </h1>
        <div class="slides">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image'])?>" alt="<?php echo $row['title']?> image 1">
        </div>
        <div class="slides">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image2'])?>" alt="<?php echo $row['title']?> image 2">
        </div>
        <div class="slides">
            <img src="data:image/jpeg;base64,<?php echo base64_encode($row['image3'])?>" alt="<?php echo $row['title']?> image 3">
        </div>

        <a class="prev" onclick="nextSlide(-1)">❮</a>
        <a class="next" onclick="nextSlide(1)">❯</a>
    </div>

</div>

<script>
    let slideNo = 1;
    showSlide(slideNo);

    function nextSlide(n) {
        showSlide(slideNo += n)
    }
    function currentSlide(n) {
        showSlide(slideNo = n)
    }

    function showSlide(n) {
        let slides = document.getElementsByClassName("slides");
        // if reached the end loop round
        if(n > slides.length){
            slideNo = 1;
        }
        if(n < 1) {
            slideNo = slides.length;
        }
        for(i=0; i < slides.length; i++){
            slides[i].style.display = "none";
        }
        slides[slideNo-1].style.display = "block";
    }
</script>

<?php
// for cart
if(empty($_SESSION["cart"])) {
    $_SESSION["cart"] = [];  // creates an array
}

if(isset($_POST["addCart"])){
    $id = $_POST["tourID"];

    if(!isset($_SESSION['cart'][$id])) {  // adds tourid to assoc array details added in cart page
        $_SESSION["cart"][$id] = ['participants'=>1, 'date'=>"", 'cost'=>0.00, 'completed'=>0];
        echo "<script> window.alert('Tour successfully added to cart');</script>";
    }
    else{
        echo "<script> window.alert('Tour already in cart');</script>";
    }
}
?>
<div>
<footer>
    <a href="#">Back to Top</a>
    <p>nadairtours@mail.com | 01415678910</p>
    <p>© 2025 Nàdair Tours, All Rights Reserved</p>
</footer>
</div>
</body>
</html>