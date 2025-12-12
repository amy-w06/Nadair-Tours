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

// make sure to start taking records from however many 8 records after
$start = ($pageNum-1) * 8;
$sql = "SELECT * FROM `Tours` LIMIT 8 OFFSET $start;";
$result = $conn->query($sql);  // issue the query

if(!$result){
    die("Query failed ".$conn->error);
}
?>
<ul class="navbar">
    <li><a href="home.php">Nàdair Tours</a></li>
    <li><a href="adminLogin.php" class="right">Admin Login</a></li>
    <li><a href="cart.php" class="right">Cart</a></li>
    <li><a href="tours.php" class="right">Tours</a></li>
    <li><a href="home.php" class="right">Home</a></li>
</ul>

<div class="top" style="background-image: url('images/toursBackground.jpg');">
    <div class="intro">
        <h1>Find your Perfect Tour of Scotland</h1>
        <p>From rolling hills and ancient castles to vibrant cities, explore the rich history and culture of this country. </p>
    </div>
</div>

<h1 class="title">All Our Tours</h1>
<div class="tourList">
    <!-- creates a new info box for every item in the database -->
    <?php  // creates a tour overview for each tour in the Tour database
    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){  // loops round and give next row of table
            // displays details in same format as home page
            echo "<div class='tourItem'>";
            echo "<img src='data:image/jpeg;base64,".base64_encode($row['image'])."' alt='".$row["title"]." view'>";  // credit in tourDetail page
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
            echo "</div>";  // button close
            echo "</div>";  // tourItem close
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
</div> <!-- tourList close -->

<ul class="pagination">
    <?php
    $sql = "SELECT COUNT(*) as total FROM `Tours`;";
    $result = $conn->query($sql);  // issue the query
    $row = $result->fetch_assoc();

    $numPages = ceil($row['total']/8);  // number of pages is the number of tours/number displayed ROUNDED UP
    $link = "";

    for($i=1; $i<=$numPages; $i++){  // https://www.geeksforgeeks.org/php/php-pagination-set-2/ (previous lines were done without)
        if($i==$pageNum){
            $link = $link . "<li class='active'><a href='tours.php?page=".$i."'>".$i."</a></li>";
        }
        else{
            $link = $link . "<li><a href='tours.php?page=".$i."'>".$i."</a></li>";
        }
    };
    echo $link
    ?>
</ul>

<footer>
    <a href="#">Back to Top</a>
    <p>nadairtours@mail.com | 01415678910</p>
    <p>© 2025 Nàdair Tours, All Rights Reserved</p>
</footer>
</body>
</html>