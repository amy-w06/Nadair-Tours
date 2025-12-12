<?php session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nàdair Tours</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<?php require 'dbConnection.php';  // connect to the database using external file
$sql = "SELECT * FROM `Tours` LIMIT 4;";
$result = $conn->query($sql);  // issue the query

if(!$result){
    die("Query failed ".$conn->error);
}?>
<ul class="navbar">
    <li><a href="home.php">Nàdair Tours</a></li>
    <li><a href="adminLogin.php" class="right">Admin Login</a></li>
    <li><a href="cart.php" class="right">Cart</a></li>
    <li><a href="tours.php" class="right">Tours</a></li>
    <li><a href="home.php" class="right">Home</a></li>
</ul>

<div class="top" style="background-image: url('images/homeBackground.jpg');">
    <div class="intro">
        <h1>Nàdair Tours</h1>
        <p>Committed to providing environmentally friendly tours all across Scotland.</p>
        <br>
        <a href="tours.php" class="tourButton">See all our Tours</a>
    </div>
</div>

<div class="toursIntro">
    <h1>Our Recommended Tours:</h1>
    <a href="tours.php" class="tourButton">See all our Tours</a>
</div>
<!-- CHANGE THIS TO OTHER WAY WITH LIMITER DONE -->
<div class="tourList">
    <?php  // creates a tour overview for first 4 tours
    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){  // loops round and give next row of table
            // displays the details
            echo "<div class='tourItem'>";
            echo "<img src='data:image/jpeg;base64,".base64_encode($row['image'])."' alt='".$row["title"]." image'>";  // credit in tourDetail page
            echo "<h3>".$row["title"]."</h3>";
            echo "<p>".$row["shortSummary"]."</p>";
            echo "<br>";
            echo "<p>".$row["duration"]." days | £".$row["price"]."pp"."</p>";
            echo "<div class='buttonContainer'>";
                echo "<a href='tourDetail.php?page=".$row['tourID']."' id='detailsButton'>View Details</a>";
                echo "<form method='post'>";
                    echo "<input type='hidden' name='tourID' value = '".$row['tourID']."'>";
                    echo "<button type='submit' id='cartButton' name='addCart'>Add to Cart</button>";
                echo "</form>";
                echo "<a href='bookingForm.php?page=".$row['tourID']."' id='bookButton'>Book Now</a>";
            echo "</div>";
            echo "</div>";
        }
    }

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
</div>

<div id="aboutUs">
    <h1 style="text-align: center"><b>About Us</b></h1>
    <p>Here at Nàdair Tours we offer a wide range of tours all across Scotland, from the excitement of the bustling cities to the tranquility of the rural highlands. We have a tour that will match everyone's desired experience.</p>
    <br>
    <p>Nàdair means 'nature' in Gaelic and we believe that the best way to tour Scotland is to fully embrace the culture, including respecting the land. That's why we strongly promote responsible travel practices that support the UN Sustainable Development Goals. In particular goals 11, 12, and 15. You can learn more about the goals and how we follow best practices to align with these below.</p>
</div>
<div class="goalsList">
    <div class="unGoal">
        <h2>Sustainable Development Goal: 11</h2>
        <h3>"Make cities and human settlements inclusive, safe, resilient and sustainable"</h3>
        <br>
        <p>A city that is not good to its residents it not good to tourists. By carefully planning out tour destinations we avoid overtourism while helping protect local spaces.
            We prioritize working with local guides and businesses ensuring that income created by tourism remains in the community. </p>
        <br>
        <a href="https://sdgs.un.org/goals/goal11" target="_blank">Learn More Here</a>
    </div>

    <div class="unGoal">
        <h2>Sustainable Development Goal: 12</h2>
        <h3>"Ensure sustainable consumption and production patterns"</h3>
        <br>
        <p>To promote sustainable consumption we provide information on how to experience the wonders of the sights while also remaining environmentally conscious.
            We encourage everyone who takes part in out tours to buy local and to minimise any waste they may produce.</p>
        <br>
        <a href="https://sdgs.un.org/goals/goal12" target="_blank">Learn More Here</a>
    </div>

    <div class="unGoal">
        <h2>Sustainable Development Goal: 15</h2>
        <h3>"Protect, restore and promote sustainable use of terrestrial ecosystems, sustainably manage forests, combat desertification, and halt and reverse land degradation and halt biodiversity loss"</h3>
        <br>
        <p>Our tours highlight the natural beauty of Scotland and we are firmly against the destruction or modification of any natural landscape.
            We are very mindful of the complex ecosystems and promote the preservation of all flora, fauna, and species that can be found.</p>
        <br>
        <a href="https://sdgs.un.org/goals/goal15" target="_blank">Learn More Here</a>
    </div>
</div>

<footer>
    <a href="#">Back to Top</a>
    <p>nadairtours@mail.com | 01415678910</p>
    <p>© 2025 Nàdair Tours, All Rights Reserved</p>
</footer>
</body>
</html>
