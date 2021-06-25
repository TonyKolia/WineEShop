<?php
    session_start();

    include "db_connection.php";

    if(isset($_POST['complete_order']))
    {
        //complete order logic
        if(isset($_SESSION['cart']))
        {
            //find out if user is member or visitor
            if(isset($_SESSION['user']))
            {
                //if member get his id
                $customerType = "U";
                $email = $_SESSION['user'];
                $sql = "select id from users where email = '$email'";
                $result = mysqli_query($db, $sql);
                if(mysqli_num_rows($result) > 0)
                {
                    $row = mysqli_fetch_assoc($result);
                    $user_id = $row['id'];
                    $visitor_id = null;
                }
            }
            else
            {   
                //if visitor, insert new visitor in db and then get his id
                $customerType = "V";
                $email = $_POST['email'];
                $sql = "insert into visitors values(null, '$email', CURRENT_DATE())";
                $result = mysqli_query($db, $sql);

                $sql = "select MAX(id) as id from visitors where email = '$email'";
                $result = mysqli_query($db, $sql);
                if(mysqli_num_rows($result) > 0)
                {
                    $row = mysqli_fetch_assoc($result);
                    $visitor_id = $row['id'];
                    $user_id = null;
                }
            }


            $cart_items = $_SESSION['cart'];
            $item_clause ="";
            foreach($cart_items as $item)
            {
                $item_clause .= ",".$item['itemId'];
            }
            $item_clause = ltrim($item_clause, ',');
            //same logic as in cart, retrieve item infomration for the items in session variable to add to the order
            $sql = "select * from wines_stock where id in ($item_clause)";
            $result = mysqli_query($db, $sql);
            if(mysqli_num_rows($result) > 0)
            {
                $grand_total = 0.0;
                while($row = mysqli_fetch_assoc($result))
                {   
                    $item_code = $row['id'];
                    $price = $row['price'];
                    $total = 0.0;
                    foreach($cart_items as $i)
                    {
                        if($i['itemId'] == $item_code)
                        {   
                            $quantity = $i['quantity'];
                            $total = $quantity * $price;
                            $grand_total += $total;
                        }
                    }
                }
                if(isset($_SESSION['user']))
                {
                    $discount = $grand_total*0.1;
                    $grand_total -= $discount;
                }

                $payment_method = $_POST['payment_method'];
                $name = $_POST['name'];
                $surname = $_POST['surname'];
                $address = $_POST['address'];
                $city = $_POST['city'];
                $postal = $_POST['postal'];
                $telephone = $_POST['telephone'];

                //insert the order in db including user details
                $sql = "insert into orders values(null, '$user_id', '$visitor_id', '$customerType', $grand_total, CURRENT_DATE(), $payment_method, 1, '$name', '$surname', '$address', '$city', '$postal', '$telephone')";

                $result = mysqli_query($db, $sql);

                //get the new order id 
                //the used_id is used to get THIS order and not another order that may be inputed in the meantime by using MAX
                if(isset($_SESSION['user']))
                {
                    $sql = "select MAX(id) as id from orders where user_id = '$user_id'";
                }
                else
                {
                    $sql = "select MAX(id) as id from orders where visitor_id = '$visitor_id'";
                }

                $result = mysqli_query($db, $sql);

                $row = mysqli_fetch_assoc($result);

                $order_id = $row['id'];

                foreach($cart_items as $cart_item)
                {
                    $itemId = $cart_item['itemId'];
                    $quantity = $cart_item['quantity'];

                    $sql = "select items_in_stock from wines_stock where id = '$itemId'";
                    $result = mysqli_query($db, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $new_stock_quantity = $row['items_in_stock'] - $quantity;
                    //update the stock 
                    $sql = "update wines_stock set items_in_stock = $new_stock_quantity where id = $itemId";
                    $result = mysqli_query($db, $sql);
                    //insert the order details - items
                    $sql = "insert into order_details values($order_id, $itemId, $quantity)";
                    $result = mysqli_query($db, $sql);
                }
            }   

            unset($_POST['complete_order']);
            unset($_SESSION['cart']);

            header('Location: ./index.php');

        }
    }
        

?>


<!doctype html>
<html lang="en">
  <head>
  <?php include "head.php"?>
  <?php include "db_connection.php"?>
  <!-- The checkout screen 
        a non editable basket appears as well as a form for the user details -->
  <body>
      <div class='container checkout-container'>
        <div id='checkout_basket'>
        <h5 class='purple-label' style='text-align:center;'><i class='fas fa-shopping-basket'></i>Το καλάθι μου</h5>
        <table class="table table-hover">
            <thead>
                <tr>
                <th scope="col"></th>
                    <th scope="col">Προϊόν</th>
                    <th scope="col">Τεμάχια</th>
                    <th scope="col">Τιμή</th>
                    <th scope="col">Σύνολο</th>
                </tr>
            </thead>
            <?php
                if(empty($_SESSION['cart']) || count($_SESSION['cart']) == 0)
                {
                    echo "<tr class='list-group-item'>Δεν υπάρχουν προϊόντα στο καλάθι</tr>";
                }
                else
                {
                    echo "<tbody>";
                    $cart_items = $_SESSION['cart'];
                    $item_clause ="";
                    foreach($cart_items as $item)
                    {
                        $item_clause .= ",".$item['itemId'];
                    }
                    $item_clause = ltrim($item_clause, ',');
                    
                    $sql = "select * from wines_stock where id in ($item_clause)";
                    $result = mysqli_query($db, $sql);
                    if(mysqli_num_rows($result) > 0)
                    {
                        $grand_total = 0;
                        while($row = mysqli_fetch_assoc($result))
                        {   
                            $item_name = $row['description'];
                            $item_code = $row['id'];
                            $price = $row['price'];
                            $total = 0.0;
                            foreach($cart_items as $i)
                            {
                                if($i['itemId'] == $item_code)
                                {   
                                    $quantity = $i['quantity'];
                                    $total = number_format($quantity * $price,2);
                                    $grand_total += $total;
                                }
                            }
                            echo "<tr>
                                    <td><img class='cart-img' src='./Content/Images/Items/$item_code.jpg' alt='Card image cap'></td>
                                    <td>$item_name</td>
                                    <td>$quantity</td>
                                    <td>$price €</td>
                                    <td>$total €</td>
                                </tr>";
                        }
                        //same logic as in cart, calculate discount
                        if(isset($_SESSION['user']))
                        {
                            $discount = $grand_total*0.1;
                            $grand_total -= $discount;

                            $discount = number_format($discount, 2);
                            
                            
                            echo "<tr><td colspan='4' style='pointer-events:none;text-align:right;'><b>Έκτπωση μέλους:</b></td><td style='pointer-events:none;'><b>$discount €</b></td></tr>";  
                        }
                        $grand_total = number_format($grand_total, 2);
                        echo "<tr><td colspan='4' style='pointer-events:none;text-align:right;'><b>Γενικό σύνολο:</b></td><td style='pointer-events:none;'><b>$grand_total €</b></td></tr>";       
                    }
                    echo "</tbody>";   
                }
            ?>
            
            </table>
                <div style="width:auto">
                <a href='./cart.php' class='btn btn-primary btn-purple' id='editBasket'>ΕΠΕΞΕΡΓΑΣΙΑ ΚΑΛΑΘΙΟΥ</a>
                </div>
        </div>
        
        <div id='checkout_form'>
            <form name='checkout' method='post' action='./checkout.php'>
            <h5 class='purple-label' style='text-align:center;'><i class='fas fa-user'></i>Προσωπικά στοιχεία</h5>
                <div class = "form-group">
                    
                </div>
                <div class="form-group">
                    <label style="margin-top:0px !important;" for="name">Όνομα</label>
                    <input  class="form-control" type="text" name="name" required>
                    <label for="surname">Επώνυμο</label>
                    <input  class="form-control" type="text" name="surname" required>
                    <label for="address">Διεύθυνση</label>
                    <input  class="form-control" type="text" name="address" required>
                    <label for="city">Πόλη</label>
                    <input  class="form-control" type="text" name="city" required>
                    <label for="postal">Ταχυδρομικός κώδικας</label>
                    <input  class="form-control" type="number" name="postal" required>
                    <label for="postal">Τηλέφωνο</label>
                    <input  class="form-control" type="number" name="telephone" required>
                    <label for="payment_methods">Τρόπος πληρωμής</label>
                    <select class="form-control" id="payment_methods" name="payment_method">
                        <?php
                            $sql = "select * from payment_methods where active = 1";
                            $result = mysqli_query($db, $sql);
                            if(mysqli_num_rows($result) > 0)
                            {
                                while($row = mysqli_fetch_assoc($result))
                                {
                                    $value = $row['id'];
                                    $desc = $row['description'];
                                    echo "<option value=$value>$desc</option>";
                                }
                            }
                        ?>
                    </select>
                </div>
                <?php 
                    //different buttons - fields appear for user if member or if visitor
                    if(!isset($_SESSION['user']))
                    {
                        echo "<div class='form-group'>
                                <label for='email'>Διεύθυνση email</label>
                                <input type='email' class='form-control' id='email' name = 'email' aria-describedby='emailHelp' placeholder='Enter email' required>
                             </div>";
                    }
                ?>
                <div class='button-container'>
                    <?php
                        if(!isset($_SESSION['user']))
                        {   
                            echo "<p><small><i>Τα μέλη μας απολαμβάνουν έκπτωση 10% στις αγορές τους!&emsp;</i><a href='./register_page.php' style='color:var(--main-color);'>Εγγραφή</a></small></p>";
                            echo "<a href='./login_page.php' class='btn btn-primary btn-purple' id='login'>ΣΥΝΔΕΣΗ</a>";
                        }
                    ?>
                    
                    <?php
                        if(isset($_SESSION['user']))
                        {
                            $value = "ΟΛΟΚΛΗΡΩΣΗ ΠΑΡΑΓΓΕΛΙΑΣ";
                        }
                        else
                        {
                            $value = "ΟΛΟΚΛΗΡΩΣΗ ΣΑΝ ΕΠΙΣΚΕΠΤΗΣ";
                        }
                        echo "<input type='submit' class='btn btn-primary btn-purple' id='complete_order' name='complete_order' value='$value'>";
                    ?>
                    </button>
                    
                </div>
                
            </form>
        </div>
        

        
        

      

    </div>
    



  </body>
</html>


