<?php
    include("php/session.php");

    // password check function
    function checkpass($password){
        // match uppercase
        $uppercase = preg_match('@[A-Z]@', $password);
        //match numbers
        $number    = preg_match('@[0-9]@', $password);

        return (!$uppercase || !$number || strlen($password) < 5);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!($_POST['pass'] == $_POST['pass_c'])){
            $checkfail = "Passwords don't match";
        } else {
            if (checkpass($_POST['pass'])){
                $checkfail = 'Password has to include at least 1 uppercase letter, 1 number and be at least 5 characters long';
            } else {
                $login = $_SESSION['login_user'];
                $pass_hash = password_hash($_POST['pass'], PASSWORD_DEFAULT);
                $sql = "UPDATE user SET pass_hash='$pass_hash' WHERE login = '$login'";
                $result = mysqli_query($db,$sql) or die(mysqli_error());
                header("Location: 1.php");
            }
        }
    }
?>

<html>
    <head>

        <title> Change Pass </title>

    </head>

    <body>
        <h2>Zmienić hasło</h2>
        <h2><a href="2.php">Tabela 1</a><h2>
        <h2><a href="3.php"> Tabela 2</a><h2>
        
        <form name="frmUser" method="post" action="" align="center">
            <h3>Changing password</h3> <br />

            <h3>New Password:</h3><br />
            <input type="password" name="pass" value="" required>
            <br />

            <h3>Confirm Password:</h3><br />
            <input type="password" name="pass_c" value="" required>
            <br />

            <input type="submit" name="save" value="Change password">
        </form>

        <?php
        ##print the message if pass check is failed
            if(isset($checkfail)){
                echo $checkfail;
            }
        ?>

    </body>
</html>