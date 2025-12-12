<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nàdair Tours</title>
    <link rel="stylesheet" type="text/css" href="style.css"/>
</head>
<body>
<ul class="navbar">
    <li><a href="home.php">Nàdair Tours</a></li>
    <li><a href="adminLogin.php" class="right">Admin Login</a></li>
    <li><a href="cart.php" class="right">Cart</a></li>
    <li><a href="tours.php" class="right">Tours</a></li>
    <li><a href="home.php" class="right">Home</a></li>
</ul>

<h1>Admin Login</h1>
<p>This is only for those who are permitted to manage tours</p><br>
<form id="myForm" method="get">
<tr>
    <td><label for="password">Please enter the Password</label></td>
    <td><input type="text" name="password" id="password"><span id = "passwordError"></span></td>
</tr>
<tr>
    <td></td>
    <td><input type="submit"></td>
</tr>
</form>

<script>
    const DEBUG = true;
    function debug(string){
        if(DEBUG){
            console.log(string)
        }
    }
    function check(event){
        debug("checking form");

        // variables
        let errs = "";
        const password = document.getElementById("password");
        const passwordError = document.getElementById("passwordError");

        // reset the error displays
        password.style.backgroundColor = "white";  // resets background colour to be white
        passwordError.innerHTML = "";  // gets rid of the warning sign

        if(password.value===""){  // checks if there is a password
            errs += "• Please enter the password.\n";
            password.style.backgroundColor = "pink";
            passwordError.innerHTML = " ⚠";
        }

        if(password && password.value){  // checks password is correct
            if(password.value !== "abc123"){
                errs += "• Password Incorrect: Please enter the correct password.\n";
                password.style.backgroundColor = "pink";
                passwordError.innerHTML = " ⚠";
            }
        }

        // we should return true if everything is okay and false if it's not
        if(errs!=="") {
            window.alert(errs);
            event.preventDefault();  // prevent the default action of the form (submitting) if something goes wrong
            return (errs === "");
        }

        // if it works go to admin page
        event.preventDefault();
        window.location.href = "../project/admin.php";
    }

    document.getElementById("myForm").addEventListener("submit", check);
</script>
</body>
</html>
