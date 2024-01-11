<?php
    include('php/sql.php');
    session_start();

    // on form submission
    if($_SERVER["REQUEST_METHOD"] == "POST") {

        // get username sent from form, in mysqli-safe way
        $myusername = mysqli_real_escape_string($db,$_POST['login']);
        
        // get user and pass hash from the db
        $sql = "SELECT login, pass_hash FROM user WHERE login = '$myusername'";
        $result = mysqli_query($db,$sql);
        $row = mysqli_fetch_array($result,MYSQLI_ASSOC);
        $pass_hash = $row["pass_hash"];
        
        // If result matched $myusername, table row must be 1 row
        // Also check the password
        $count = mysqli_num_rows($result);
        if($count == 1 && password_verify($_POST['pass'], $pass_hash)) {
            
            // if ok, save the username to session, move to 1.php
            $_SESSION['login_user'] = $myusername;
            header("Location: 1.php");
        }else {

            // if bad, set $check_failed to the notice message
            $check_failed = "Your Login Name or Password is invalid";
        }
     }
?>

<html>
    <head>

    <title>Some cool title</title>

    </head>
    <body>

    <div>
        <form name="frmUser" method="post" action="" align="center">
            <h3>Login:</h3> <br />
            <input type="text" name="login" value="" required>
            <br />

            <h3>Password:</h3><br />
            <input type="password" name="pass" value="" required>
            <br />

            <input type="submit" name="go" value="Login">
        </form>

        <?php
        ##print the message if log/pass check is failed
            if(isset($check_failed)){
                echo $check_failed;
            }
        ?>
    </div>
    
    </body>
</html>