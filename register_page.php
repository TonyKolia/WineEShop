<?php 
    session_start();
?>
<?php
    include "db_connection.php";
    $error = "";
    //user register page
    //note: admin user cannot be registerd like this, see user_management.php in Admin for that
    if(isset($_POST['sub_register']))
    {
        $email = $_POST['registerEmail'];
        $pass = $_POST['registerPass'];
        $passConfirm = $_POST['registerPassConfirm'];

        //if any validation fails, the corresponding message is returned to the user
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) //validate email
        {
            $error = "Το email δεν έχει σωστή μορφή";
        }
        else
        {
            $sql = "select count(*) as count from users where email = '$email'";
            $result = mysqli_query($db, $sql);
        
            if(mysqli_num_rows($result) > 0) //make sure the user doesnt already exist
            {
                $row = mysqli_fetch_assoc($result);
                if($row['count'] > 0)
                {
                    $error = "Το email υπάρχει ήδη.";
                }
                else if($pass != $passConfirm)
                {
                    $error = "Οι κωδικοί πρόσβασης δεν ταιριάζουν";
                }
                else
                {
                    //if no validation fails occur, hash the pasword and insert the new user in db
                    $hashed_password = md5($pass);
                    $sql = "insert into users values (null, '$email', '$hashed_password', 0, CURRENT_DATE())";
                    $result = mysqli_query($db, $sql);
                    if($result)
                    {
                        header('Location: ./login_page.php');
                    }
                    else
                    {
                        $error = "Σφάλμα κατά τη δημιουργία λογιαρασμού. Δοκιμάστε ξανά.";
                    }   
                }
            }
        }
    }
?>
<!doctype html>
<html lang="en">
  <head>
  <?php include "head.php"?>
  
  
  <body>

  <div class='container' id='RegisterContainer'>

  <form class='login-form' id="RegistrationForm" name="RegistrationForm" action="./register_page.php" method="post" required>
    <h5 class='purple-label'>Δημιουργία λογαριασμού</h4>
  <div class="form-group">
    <label for="registerEmail">Διεύθυνση εmail</label>
    <input type="email" class="form-control" name="registerEmail" aria-describedby="emailHelp" required placeholder="Enter email">
  </div>
  <div class="form-group">
    <label for="registerPass">Κωδικός πρόσβασης</label>
    <input type="password" class="form-control" name="registerPass" required placeholder="Enter password">
  </div>
  <div class="form-group">
    <label for="registerPassConfirm">Επιβεβαίωση κωδικού πρόσβασης</label>
    <input type="password" class="form-control" name="registerPassConfirm" placeholder="Confrim password">
  </div>
    <?php 
        if($error != "")
        {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    ?>
  <input name ="sub_register" type="submit" class="btn btn-primary btn-purple btn-login" value="ΕΓΓΡΑΦΗ"></button>
</form>


  </div>
  


  </body>
  
</html>

