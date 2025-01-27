<?php

if (isset($_POST['signup-submit'])){
    require 'dbhandler.php';

    $username = $_POST['uname'];
    $email = $_POST['email'];
    $password = $_POST['pwd'];
    $pass_rep = $_POST['con-pwd'];
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];

    if ($password !== $pass_rep){
        header("Location: ../signup.php?error=diffPasswords&fname=".$fname."&lname=".$lname."&uname".$username);
        exit();
    }
    else{
        $sql = "SELECT uname FROM users WHERE uname=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)){
            header("Location: ../signup.php?error=SQLInjection");
            exit();
        }
        else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $check = mysqli_stmt_num_rows($stmt);

            if ($check > 0){
                header("Location: ../signup.php?error=UsernameTaken");
                exit();
            }

            else{
                $sql = "INSERT INTO users (lname, fname, email, uname, password) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)){
                    header("Location: ../signup.php?error=SQLInjection");
                    exit();
                }
                else{
                    $hashedPass = password_hash($password, PASSWORD_BCRYPT);

                    mysqli_stmt_bind_param($stmt, "sssss", $lname, $fname, $email, $username, $hashedPass);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);

                    $sqlImg = "INSERT INTO profile(uname) VALUES ('$username')";
                    mysqli_query($conn, $sqlImg);

                    header("Location: ../signup.php?signup=success");
                    exit();
                }
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
    }
}
else{
    header("Location: ../signup.php");
    exit();
}