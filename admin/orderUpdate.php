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
$sql = "SELECT * FROM `Orders`;";
$result = $conn->query($sql);  // issue the query
if($result->num_rows > 0){
    $row = $result->fetch_assoc();
}

if(!$result){
    die("Query failed ".$conn->error);
}

$id = safePost($conn, "id");
$stage = safePost($conn, "stage");

if($stage === "3"){  // stage 3 update table for specified order
    $name = safePost($conn, "name");
    $email = safePost($conn, "email");
    $phone = safePost($conn, "phone");
    $totalCost = safePost($conn, "cost");

    echo "<p>Update for Order $id</p>";

    $stmt = $conn->prepare("UPDATE `Orders` SET 
            `customerName`  = ?,
            `customerEmail` = ?,
            `customerPhone` = ?,
            `totalCost`     = ?
            WHERE `orderID` = ?");
    $stmt->bind_param("ssidi", $name, $email, $phone, $totalCost, $id);
    $stmt->execute();

    echo "<div class='idSelector'>";
    echo "<h1>Update for Order $id successful</h1>";
    echo "<br>";
    echo "<a href='../admin.php' id='cartButton'>Back to Home</a>";
    echo "</div>";
}
else if($stage === "2"){  // stage 2 show the form for user $id

    $sql = "SELECT * FROM `Orders` WHERE `orderID` = $id";
    $result = $conn->query($sql);  // issue the query
    if(!$result){
        die("Query failed ".$conn->error);
    }

    // first output as paragraphs
    if($result->num_rows===1){
        while($row = $result->fetch_assoc()){
            ?>
        <div class="inputContainer">
            <h1>Updating order id: <?php echo $id?></h1>
            <form action="orderUpdate.php" method="post" id="updateForm">
                <input type="hidden" name="stage" value="3"> <!-- when this is submitted it will be stage 3 -->
                <input type="hidden" name="id" value="<?php echo $row["orderID"]?>">
                <table>
                    <tr>
                        <td><label for="name">Customer Name: </label></td>
                        <td><input name="name" id="name" type="text" value="<?php echo $row["customerName"]?>"><span id="nameError"></span></td></tr>
                    <tr>
                        <td><label for="email">Customer Email: </label></td>
                        <td><input name="email" id="email" type="text" value="<?php echo $row["customerEmail"]?>"><span id="emailError"></span></td></tr>
                    <tr>
                        <td><label for="phone">Customer Phone: </label></td>
                        <td><input name="phone" id="phone" type="text" value="<?php echo $row["customerPhone"]?>"><span id="phoneError"></span></td></tr>
                    <tr>
                        <td><label for="cost">Total Cost: </label></td>
                        <td><input name="cost" id="cost" type="number" min="0.01" step="0.01" value="<?php echo $row["totalCost"]?>"><span id="costError"></span></td></tr>
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
            const custName = document.getElementById("name");
            const custEmail = document.getElementById("email");
            const custPhone = document.getElementById("phone");
            const totalPrice = document.getElementById("cost");

            const nameError = document.getElementById("nameError");
            const emailError = document.getElementById("emailError");
            const phoneError = document.getElementById("phoneError");
            const costError = document.getElementById("costError");

            // reset error displays
            custName.style.backgroundColor = "white";  // background colour reset
            custEmail.style.backgroundColor = "white";
            custPhone.style.backgroundColor = "white";
            totalPrice.style.backgroundColor = "white";


            nameError.innerHTML = "";  // warning reset
            emailError.innerHTML = "";
            phoneError.innerHTML = "";
            costError.innerHTML = "";


            if (custName.value === "") {
                errs += "• Please ensure 'Customer Name' field has a value.\n";
                custName.style.backgroundColor = "pink";
                nameError.innerHTML = " ⚠";
            }
            if (custEmail.value === "") {
                errs += "• Please ensure 'Customer Email' field has a value.\n";
                custEmail.style.backgroundColor = "pink";
                emailError.innerHTML = " ⚠";
            }
            if (custPhone.value === "") {
                errs += "• Please ensure 'Customer Phone' field has a value.\n";
                custPhone.style.backgroundColor = "pink";
                phoneError.innerHTML = " ⚠";
            }
            if (totalPrice.value === "") {
                errs += "• Please ensure 'Total Cost' field has a value.\n";
                totalPrice.style.backgroundColor = "pink";
                costError.innerHTML = " ⚠";
            }
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
    <form method="post" action="orderUpdate.php">
        <input type="hidden" name="stage" value="2"> <!-- when this is submitted it will be stage 2 -->
        <p><label for="id">Choose an order to edit:</label>

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