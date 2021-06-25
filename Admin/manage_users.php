<?php
    session_start();
    include "../db_connection.php";
    $error = "";

    //if not an admin and user gets here by url, redirect
    if(!isset($_SESSION["admin"]) || !$_SESSION["admin"])
    {
        header('Location: ../index.php');
        exit;
    }

    //update existing user
    if(isset($_POST["update_user"]))
    {
        $user_id = $_POST["user_id"];
        $user_email = $_POST["user_email"];

        //if any validation fails, the corresponding message is returned to the user
        if(!filter_var($user_email, FILTER_VALIDATE_EMAIL))
        {
            $error = "Ενημέρωση χρήστη $user_id: Το email δεν έχει σωστή μορφή";
        }
        else
        {
            $sql = "select count(*) as count from users where email = '$user_email'";
            $result = mysqli_query($db, $sql);
        
            if(mysqli_num_rows($result) > 0) //check if user exists
            {
                $row = mysqli_fetch_assoc($result);
                if($row['count'] > 0)
                {
                    $error = "Ενημέρωση χρήστη $user_id: Το email υπάρχει ήδη.";
                }
                else
                {
                    $sql = "update users set email = '$user_email' where id = $user_id";
                    $result = mysqli_query($db, $sql);
                    if($result)
                    {
                        unset($_POST);
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit;
                    }
                    else
                    {
                        $error = "Ενημέρωση χρήστη $user_id: Σφάλμα κατά την ενημέρωση λογιαρασμού. Δοκιμάστε ξανά.";
                    }   
                }
            }
        } 
    }

    //insert new user
    if(isset($_POST["insert_user"]))
    {
        $user_email = $_POST["user_email"];
        $user_pass = $_POST["user_pass"];
        $user_pass_confirm = $_POST["user_pass_confirm"];

        if(isset($_POST['user_admin']) && $_POST['user_admin'] == '1')
        {
            $user_admin = 1;
        }
        else
        {
            $user_admin = 0;
        }
        
        //if any validation fails, the corresponding message is returned to the user
        if(!filter_var($user_email, FILTER_VALIDATE_EMAIL))
        {
            $error = "Εισαγωγή νέου χρήστη: Το email δεν έχει σωστή μορφή";
        }
        else
        {
            $sql = "select count(*) as count from users where email = '$user_email'";
            $result = mysqli_query($db, $sql);
        
            if(mysqli_num_rows($result) > 0) //check if user already exists
            {
                $row = mysqli_fetch_assoc($result);
                if($row['count'] > 0)
                {
                    $error = "Εισαγωγή νέου χρήστη: Το email υπάρχει ήδη.";
                }
                else if($user_pass != $user_pass_confirm)
                {
                    $error = "Εισαγωγή νέου χρήστη: Οι κωδικοί πρόσβασης δεν ταιριάζουν";
                }
                else
                {
                    //hash the input password and insert the new user in db
                    $hashed_password = md5($user_pass); 
                    $sql = "insert into users values (null, '$user_email', '$hashed_password', $user_admin, CURRENT_DATE())";
                    $result = mysqli_query($db, $sql);
                    if($result)
                    {
                        unset($_POST);
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit;
                    }
                    else
                    {
                        $error = "Εισαγωγή νέου χρήστη: Σφάλμα κατά τη δημιουργία λογιαρασμού. Δοκιμάστε ξανά.";
                    }   
                }
            }
        }
    }
    
?>

<!doctype html>
<html lang="en">
    <head>
      <?php include "../head.php"; ?>
    </head>
    <!-- Admin screen with ALL the users
         From here the email of an existing user can be updated
         Also a new user can be created (that includes an admin user) -->
    <body>
    <div class='container basket-container'>
      <h3 class='purple-label order-management-label'><i class='fas fa-user'></i>Διαχείρηση χρηστών</h3>
        <?php if($error != "")
        {
            echo "<div class='alert alert-danger'>$error</div>";
        }?>
        <div id = "stock_container">
        <table class="table table-hover basket-table">
            <thead>
                <tr>
                    <th scope="col" class="numeric-input">Κωδικός <i style="padding:0;" class="fas fa-plus-circle new-item-btn" title="Προσθήκη" onclick="showUserAdditionScreen();"></i></th>
                    <th scope="col">Εmail</th>
                    <th scope="col">Ημερομηνία εγγραφής</th>
                    <th scope="col">Διαχειριστής</th>
                    <th scope="col" colspan="2"  class="numeric-input"  style='text-align:center;'>Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $sql = "select * from users";
                    $result = mysqli_query($db, $sql);
                    while($row = mysqli_fetch_assoc($result))
                    {
                        //retrieve all users
                        $user_id = $row['id'];
                        $user_email = $row['email'];
                        $user_date = new DateTime($row['registration_date']);
                        $user_date = $user_date->format('d/m/Y');
                        $user_admin = $row['is_admin'];
                        
                        //check if user is admin to display it
                        if($row['is_admin'] == 1)
                        {
                            $user_admin = "<i class='fas fa-check'></i>";
                        }
                        else
                        {
                            $user_admin = "<i class='fas fa-times'></i>";
                        }
                        //each row is a form that is posted to use the input to update the user
                        echo"<tr>
                                <form name = 'user_form' method = 'post' action='manage_users.php'>
                                <td><input style='width:fit-content;' name = 'user_id' class='order-id-manage' id=user_$user_id value = $user_id  readonly></input></td>
                                <td><input class='form-control' style='width:100%;' type='email' id='$user_id-email' name = 'user_email' value = '$user_email' disabled required></input></td>
                                <td>$user_date</td>
                                <td>$user_admin</td>
                                <td style='text-align:center;'>
                                    <button type='button' id='$user_id-edit' class='btn btn-primary btn-purple' onclick='enableEditingUser($user_id);' title='Επεξεργασία' name = 'save_user'><i class='fas fa-pen'></i></button>
                                    <button style='display:none;' type='button' id='$user_id-cancel' class='btn btn-primary btn-purple' onclick='disableEditingUser($user_id);' title='Ακύρωση' name = 'cancel_edit'><i class='fas fa-times-circle'></i></button>
                                </td>
                                <td style='text-align:center;'><button id='$user_id-save' type='submit' class='btn btn-primary btn-purple'  title='Αποθήκευση' name = 'update_user' disabled><i class='fas fa-save'></i></button></td>
                                </form>
                            </tr>";
                    }
                    
                ?>
            </tbody>
        </table>

        <!-- The create new user modal -->
        <div class="modal fade" id="userAdditionScreen"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content" style="width: max-content;">
                        <div class="modal-header">
                            <h5 class="modal-title" id="orderNumber">Προσθήκη νέου χρήστη</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <table class='table table-hover order-details'>
                            <thead>
                                <th scope="col">Κωδικός</th>
                                <th scope="col">Email</th>
                                <th scope="col">Κωδικός</th>
                                <th scope="col">Επιβεβαίωση κωδικού</th>
                                <th scope="col">Διαχειριστής</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <!-- each row is a form and when posted, its input is used to create new user -->
                                    <form name = 'new_user_form' method = 'post' action='manage_users.php'>
                                        <?php 
                                            $sql = "select max(id) as id from users";
                                            $result = mysqli_query($db, $sql);
                                            $row = mysqli_fetch_assoc($result);
                                            if($row['id'] == null || $row['id'] == 0)
                                            {
                                                $newUserId = 1;
                                            }
                                            else
                                            {
                                                $newUserId = $row['id'] + 1;
                                            }
                                        ?>
                                        <td><?php echo $newUserId ?></td>
                                        <td><input class='form-control' style='width:100%;' type='email' name = 'user_email' required></input></td>
                                        <td><input class='form-control' style='width:100%;' type='password' name = 'user_pass' required></input></td>
                                        <td><input class='form-control' style='width:100%;' type='password' name = 'user_pass_confirm' required></input></td> 
                                        <!--here is the option to mark the new user as admin -->
                                        <td><input class='form-control' style='width:100%;' type='checkbox' name = 'user_admin' value = "1"></input></td> 
                                        <td style='text-align:center;'><button type='submit' class='btn btn-primary btn-purple'  title='Αποθήκευση' name = 'insert_user'><i class='fas fa-save'></i></button></td>
                                    </form>
                                </tr>
                            </tbody>
                        </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </body>


</html>