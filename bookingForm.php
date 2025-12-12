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
<div class="bookingDetails">
    <h1>Booking for <?php echo $row["title"] ?></h1>
    <h3><?php echo "£".$row["price"]."pp" ?></h3>
<br>
</div>

<div id="form" class="inputContainer">
    <form id="bookForm" action="bookingInsert.php" method="post">
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
                <td><label for="dateBeginning">Date: </label></td>
                <td><input name="dateBeginning" id="dateBeginning" type="date" min="<?php echo date("Y-m-d"); ?>"><span id="dateError"></span></td></tr>
            <tr>
                <td><label for="participants">Number of Participants: </label></td>
                <td><input name="participants" id="participants" type="number" min="1" value="1" onchange="updatePrice()"><span id="participantError"></span></td></tr>
            <tr>
                <td></td>
                <td><input type="submit"></td>
            </tr>
        </table>
        <input type="hidden" id="tourID" name="tourID" value = <?php echo '"'.$row["tourID"].'"' ?>>
        <input type="hidden" id="totalCost" name="totalCost" value = <?php echo '"'.$row["price"].'"' ?>>
    </form>
    <h3>The total price is: £<span id="total"><?php echo $row["price"] ?></span></h3>
</div>

<script>
    // checks for form input
    function check(event) {
        let errs = "";
        const custName = document.getElementById("customerName");
        const custEmail = document.getElementById("customerEmail");
        const custPhone = document.getElementById("customerPhone");
        const date = document.getElementById("dateBeginning");
        const participants = document.getElementById("participants");

        const nameError = document.getElementById("nameError");
        const emailError = document.getElementById("emailError");
        const phoneError = document.getElementById("phoneError");
        const dateError = document.getElementById("dateError")
        const participantError = document.getElementById("participantError");

        // reset the error displays
        custName.style.backgroundColor = "white";  // resets background colour to be white
        custEmail.style.backgroundColor = "white";
        custPhone.style.backgroundColor = "white";
        date.style.backgroundColor = "white";
        participants.style.backgroundColor = "white";
        nameError.innerHTML = "";  // gets rid of the warning sign
        emailError.innerHTML = "";
        phoneError.innerHTML = "";
        dateError.innerHTML = "";
        participantError.innerHTML = "";

        // presence checks
        if (custName.value === "") {
            errs += "• Please complete 'Full Name' field.\n";
            custName.style.backgroundColor = "pink";
            nameError.innerHTML = " ⚠";  // gives the object that represents the contents of that span
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
        if (date.value === "") {  // this shouldn't come up as there is already a value
            errs += "• Please complete 'Date' field.\n";
            date.style.backgroundColor = "pink";
            dateError.innerHTML = " ⚠";
        }
        if (participants.value === "") {  // again this shouldn't come up as value is 1 but just in case
            errs += "• Please complete 'Number of Participants' field.\n";
            participants.style.backgroundColor = "pink";
            participantError.innerHTML = " ⚠";
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

        if (errs !== "") {
            window.alert("Please complete the following errors before submission:\n" + errs);
            event.preventDefault();  // prevent the default action of the form (submitting) if something goes wrong
        }
        return (errs === "");
    }

    // for the total price updating based on number of participants (https://stackoverflow.com/questions/65828171/how-to-update-a-number-in-html-using-js)
    function updatePrice(){
        const costPP = Number(<?php echo $row["price"] ?>);
        const participants = Number(document.getElementById("participants").value);

        document.getElementById("total").innerHTML = (costPP * participants).toFixed(2);
        document.getElementById("totalCost").value = (costPP * participants).toFixed(2);  // probably not the most efficient way to do this but it will do for now
    }

    document.getElementById("bookForm").addEventListener("submit", check);
</script>
</body>
</html>
