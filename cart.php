<?php
    session_start();

if(empty($_SESSION["cart"])) {
    $_SESSION["cart"] = [];  // creates an array
}

if(isset($_POST["addCart"])){
    $id = $_POST["tourID"];

    if(!isset($_SESSION['cart'][$id])) {  // adds tourid to assoc array if its not already in (details added later in this page)
        $_SESSION["cart"][$id] = ['participants'=>1, 'date'=>"", 'cost'=>0.00];
        echo "<script> window.alert('Tour successfully added to cart');</script>";
    }
    else{
        echo "<script> window.alert('Tour already in cart');</script>";
    }
}

// on submit for each tour, the details get saved to the session array
if(isset($_POST["cartForm"])){
    $id = $_POST["tourID"];
    $participants = $_POST["participants"];
    $date = $_POST["dateBeginning"];
    $price = $_POST["tourCost"];

    $_SESSION["cart"][$id]["participants"] = $participants;
    $_SESSION["cart"][$id]["date"] = $date;
    $_SESSION["cart"][$id]["cost"] = $price;
    $_SESSION["cart"][$id]["completed"] = 1;  // marks the tour details as fully completed so user cant submit invalid bookings as part of an order
}

// deletes the specified tour from the array
if(isset($_POST["deleteTour"])){
    $id = $_POST["tourID"];

    unset($_SESSION["cart"][$id]);
}

// clear entire cart
if(isset($_POST["deleteCart"])){
    foreach ($_SESSION["cart"] as $id=>$tour){  // goes through every tour and removes it from array
        unset($_SESSION["cart"][$id]);
    }
}
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
$sql = "SELECT * FROM `Tours`;";
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
 <!-- this is very messy, everything has the tourid at the end of it so it's unique -->
<div class="cartPage">
<div class="cartContainer">
    <h1>Your Tours</h1>
    <form method="post">
        <button type='submit' id='tourButton' name='deleteCart'>Empty Cart</button>
    </form>
    <?php
    if(!empty($_SESSION["cart"])){  // if there is items in cart get the number of items

        foreach($_SESSION["cart"] as $id=>$tour){

            $sql = "SELECT * FROM `Tours` WHERE `tourID` = $id;";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()){
                echo "<div class='cartItem'>";
                    $length = $row["duration"];
                    echo "<img src='data:image/jpeg;base64,".base64_encode($row['image'])."' alt='".$row["title"]." view'>"; // credit in tourDetail page
                    echo "<div class='cartItemText'>";
                        echo "<h2>".$row['title']."</h2>";
                        if($length == 1){  // if it's 1 day
                            echo $row['location']." | ".$length." day";
                        } else {  // if it's multiple days
                            echo $row['location'] . " | " . $length . " days";
                        }?>

                        <!-- this is a lot of going in and out of php
                             all have tour id for uniqueness -->
                        <form class="cartItemForm" method="post" action="cart.php">
                            <input type="hidden" name="tourID" value="<?php echo $id; ?>">
                            <input type="hidden" name="initialPrice" id="initialPrice<?php echo $id; ?>" value="<?php echo $row["price"];?>">  <!-- for js -->
                            <input type="hidden" name="completed" id="completed<?php echo $id; ?>" value="<?php echo $_SESSION['cart'][$id]['completed'] ?>">
                            <input type="hidden" id="tourCost<?php echo $id; ?>" name="tourCost" value="<?php echo $row["price"];?>">
                            <table>
                                <tr>
                                    <td><label for="date<?php echo $id; ?>">Date: </label></td>
                                    <td><input required name="dateBeginning" id="date<?php echo $id; ?>" type="date" min="<?php echo date("Y-m-d"); ?>" value="<?php echo $_SESSION['cart'][$id]['date']; ?>"><span id="dateError<?php echo $id; ?>"></span></td></tr> <!-- from week 2 lab -->
                                <tr>
                                    <td><label for="participants<?php echo $id; ?>">Number of Participants: </label></td>
                                    <td><input required name="participants" id="participants<?php echo $id; ?>" type="number" min="1" value="<?php echo $_SESSION['cart'][$id]['participants']; ?>" onchange="updatePrice(<?php echo $id; ?>)"><span id="participantError<?php echo $id; ?>"></span></td></tr>
                                <tr>
                                    <td></td>
                                    <td><input type="submit" name="cartForm" id="tourButton" value="Confirm Changes" onclick="markCompleted(<?php echo $id; ?>)"></td>
                                </tr>
                            </table>
                        </form>
                        <br>
                        <h3>The total price for this tour is: £<span id="tourTotal<?php echo $id;?>">
                                <?php
                                    if($_SESSION['cart'][$id]['cost'] === 0.00){  // if the price has never been set
                                        echo $row['price'];
                                    }
                                    else{
                                        echo $_SESSION['cart'][$id]['cost'];
                                    }
                            echo "</span></h3>";
                            echo "<form method='post'>";  // remove tour button
                                echo "<input type='hidden' name='tourID' value = '".$id."'>";
                                echo "<button type='submit' id='tourButton' name='deleteTour'>Remove Tour</button>";
                            echo "</form>";
                    echo "</div>";
                echo "</div>";

            }
        }
    }
    else {
        echo "<h1>No cart items to view</h1>";
    }

    ?>


</div>
<!-- get customer details for order -->
<div class="cartDetailsContainer">
    <form id="cartForm" method="post" action="cartInsert.php">
    <table>
        <tr>
            <td><label for="customerName">Full Name: </label></td>
            <td><input name="customerName" id="customerName" type="text"><span id="nameError"></span></td></tr>
        <tr>
            <td><label for="customerEmail">Email: </label></td>
            <td><input name="customerEmail" id="customerEmail" type="text"><span id="emailError"></span></td></tr>
        <tr>
            <td><label for="customerPhone">Phone Number: </label></td>
            <td><input name="customerPhone" id="customerPhone" type="text"><span id="phoneError"></span></td></tr>
        <tr>
            <td></td>
            <td><input type="submit"></td>
        </tr>
    </table>
        <!-- total cost of cart is calculated by adding up the total costs of each cart item -->
        <input type="hidden" name="totalCartCost" id="totalCartCost" value="<?php $total = 0.00;
                                                             foreach ($_SESSION['cart'] as $id=>$tour){$total += $_SESSION['cart'][$id]['cost'];}
                                                             echo $total; ?>">

    <h3>The total price for confirmed items in the cart is: £<span id="cartTotal"><?php $total = 0;
                                                                                    foreach ($_SESSION['cart'] as $id=>$tour){$total += $_SESSION['cart'][$id]['cost'];}
                                                                                    echo $total; ?></span></h3>
    <input name="countCart" id="countCart" type="hidden" value="<?php echo count($_SESSION['cart']);?>">
    </form>
</div>
</div>

<script>
    // checks user details form input
    function check(event) {
        // shouldnt be able to submit if there are no tours in the cart
        const countCart = document.getElementById("countCart");

        if(countCart.value === "0"){
            window.alert("Cannot create an Order without any Tours");
            event.preventDefault();
            return null;
        }

        let errs = "";
        const custName = document.getElementById("customerName");
        const custEmail = document.getElementById("customerEmail");
        const custPhone = document.getElementById("customerPhone");

        const nameError = document.getElementById("nameError");
        const emailError = document.getElementById("emailError");
        const phoneError = document.getElementById("phoneError");

        // reset the error displays
        custName.style.backgroundColor = "white";  // resets background colour to be white
        custEmail.style.backgroundColor = "white";
        custPhone.style.backgroundColor = "white";
        nameError.innerHTML = "";  // gets rid of the warning sign
        emailError.innerHTML = "";
        phoneError.innerHTML = "";

        // presence checks
        if (custName.value === "") {
            errs += "• Please complete 'Full Name' field.\n";
            custName.style.backgroundColor = "pink";
            nameError.innerHTML = " ⚠";
        }
        if (custEmail.value === "") {
            errs += "• Please complete 'Email' field.\n";
            custEmail.style.backgroundColor = "pink";
            emailError.innerHTML = " ⚠";
        }
        if (custPhone.value === "") {
            errs += "• Please complete 'Phone Number' field.\n";
            custPhone.style.backgroundColor = "pink";
            phoneError.innerHTML = " ⚠";
        }

        // further checks
        if(!(custEmail.value.includes("@")) || !(custEmail.value.includes("."))){
            errs += "• Please ensure that 'Email' is of the correct format.\n";
            custEmail.style.backgroundColor = "pink";
            emailError.innerHTML = " ⚠";
        }
        if(isNaN(custPhone.value)) {
            errs += "• Please ensure that 'Phone Number' is a number.\n";
            custPhone.style.backgroundColor = "pink";
            phoneError.innerHTML = " ⚠";
        }

        // checks that all tours in cart have participants and a date
        const allComplete = document.getElementsByName("completed");
        for(i = 0; i < allComplete.length; i++){
            if(allComplete[i].value == 0){  // if there is a 0 the booking detials haven't been put in
                errs += "• Please ensure that all tours have all details selected and CONFIRMED.\n";
                break;
            }
        }

        if (errs !== "") {
            window.alert("Please complete the following errors before submission:\n" + errs);
            event.preventDefault();  // prevent the default action of the form (submitting) if something goes wrong
        }
        return (errs === "");
    }

    function updatePrice(id){
        const costPP = Number(document.getElementById("initialPrice"+id).value);
        const participants = Number(document.getElementById("participants"+id).value);

        document.getElementById("tourTotal"+id).innerHTML = (costPP * participants).toFixed(2);  // updates the innerHTML of the tour total price dependant on the participant number
        document.getElementById("tourCost"+id).value = (costPP * participants).toFixed(2);  // does the same for the value passed into the session array
    }

    function markCompleted(id){
        document.getElementById("completed"+id).value = 1;  // marks the tour as fully completed
    }

    document.getElementById("cartForm").addEventListener("submit", check);  // runs check when cart is submitted
</script>


</body>
</html>
