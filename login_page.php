<?php 
    session_start();
?>
<?php
    include "db_connection.php";
    $error = "";
    //user login
    if(isset($_POST['sub_login']))
    {
        $email = $_POST['email'];
        $pass = $_POST['password'];
        //if any validation fails, the corresponding message is returned to user
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) //email validation
        {
            $error = "Το email δεν έχει σωστή μορφή";
        }
        else
        {
            $sql = "select * from users where email = '$email'"; //check if user actually exists
            
            $result = mysqli_query($db, $sql);
        
            if($result)
            {
                if(mysqli_num_rows($result) > 0) //if exists, compared the stored hashed password with the hash of the pasword input
                {
                    $row = mysqli_fetch_assoc($result);
                    $hashed_password = md5($pass);
                    if($hashed_password == $row['password'])
                    {   
                        $_SESSION['user'] = $email;
                        $_SESSION['user_id'] = $row['id'];
                        if($row['is_admin'] == 1) //if admin, mark that to enable admin functions
                        {
                            $_SESSION['admin'] = true;
                        }
                        header('Location: ./index.php');
                    }
                    else
                    {
                        $error = "Tα στοιχεία σύνδεσης δεν είναι έγκυρα.";
                    }
                }
                else
                {
                    $error = "Tα στοιχεία σύνδεσης δεν είναι έγκυρα.";
                }
            }
            else
            {
                $error = "Σφάλμα κατά τη σύνδεση. Παρακαλώ δοκιμάστε ξανά.";
            }
            
        }
    }


?>


<!doctype html>
<html lang="en">
  <head>
  <?php include "head.php"?>
  <?php include "db_connection.php"?>
  
  <body>

  <div class='container' id='LoginContainer'>

  <form class='login-form' name="LoginForm" id="LoginForm" method="post" action="./login_page.php">
    <h5 class='purple-label'>Σύνδεση στο λογαριασμό σας</h5>
  <div class="form-group">
    <label for="exampleInputEmail1">Διεύθυνση email</label>
    <input type="email" class="form-control" id="email" name = "email" aria-describedby="emailHelp" placeholder="Enter email" required>
  </div>
  <div class="form-group">
    <label for="exampleInputPassword1">Κωδικός πρόσβασης</label>
    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
  </div>
  <a href="./register_page.php" class='register-link'>Δεν είστε μέλος; Εγγραφθείτε!</a>
  <?php 
        if($error != "")
        {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    ?>
  <input type="submit" class="btn btn-primary btn-purple btn-login" name="sub_login" value="ΣΥΝΔΕΣΗ"></button>
</form>


  </div>



  </body>
</html>