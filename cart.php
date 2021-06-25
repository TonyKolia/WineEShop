<?php 
    session_start();
?>

<!doctype html>
<html lang="en">
  <head>
  <?php include "head.php"?>
  <?php include "db_connection.php"?>
  <!-- The basket screen -->
  <body>
      <div class='container basket-container '>
        <div>
        <h5 class='purple-label'><i class='fas fa-shopping-basket'></i>Το καλάθι μου</h5>
        <table class="table table-hover basket-table">
            <thead>
                <tr>
                <th scope="col"></th>
                    <th scope="col">Προϊόν</th>
                    <th scope="col" class='items-table-label'>Τεμάχια</th>
                    <th scope="col">Τιμή</th>
                    <th scope="col">Σύνολο</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <?php
                if(empty($_SESSION['cart']) || count($_SESSION['cart']) == 0)
                {
                    echo "<tr><td colspan='5'>Δεν υπάρχουν προϊόντα στο καλάθι<td></tr>";
                }
                else
                {
                    echo "<tbody>";
                    //if the basket has items
                    //create an sql query to retrieve information about the item ids in the session variable
                    $cart_items = $_SESSION['cart'];
                    $item_clause ="";
                    foreach($cart_items as $item)
                    {
                        $item_clause .= ",".$item['itemId'];
                    }
                    $item_clause = ltrim($item_clause, ',');
                    //for example, the above will have a value of 3,4,5 to be used in the 'in' clause
                    $sql = "select * from wines_stock where id in ($item_clause)";
                    $result = mysqli_query($db, $sql);
                    if(mysqli_num_rows($result) > 0)
                    {
                        //display the information retrieved as rows in the table
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
                            //a counter is created as well for the quantity controlled only using + & - buttons
                            //the click event and the update of the counter is handled using javascript
                            //also a button to remove the item completely is added
                            echo "<tr>
                                    <td><img class='cart-img' src='./Content/Images/Items/$item_code.jpg' alt='Card image cap'></td>
                                    <td><a href='./item_details.php?itemId=$item_code' class='register-link'>$item_name</a></td>
                                    <td  class='items-table-label'>
                                        <div>
                                            <i class='fas fa-minus' data-field='$item_code-counter' onclick='removeItemFromCart($item_code,true);'></i>
                                                <input type='number' name=$item_code-counter id=$item_code-counter class='item-counter' readonly value=$quantity>
                                            <i class='fas fa-plus' data-field='$item_code-counter' onclick='addItemToCart($item_code,true);'></i>
                                        </div>
                                    </td>
                                    <td>$price €</td>
                                    <td>$total €</td>
                                    <td style='text-align:center;'><button type='button' id='$item_code-delete' class='btn btn-primary btn-purple'  title='Διαγραφή' onclick='deleteItemFromCart($item_code);'><i class='fas fa-trash-alt'></i></button></td>
                                </tr>";
                        }
                        //compute the member discount and total
                        //the discount % could probably be saved in db and managed by admin to be dynamic
                        if(isset($_SESSION['user']))
                        {
                            $discount = $grand_total*0.1;
                            $grand_total -= $discount;
                            
                            $discount = number_format($discount, 2);
                           

                            echo "<tr><td colspan='4' style='pointer-events:none;text-align:right;'><b>Έκτπωση μέλους:</b></td><td colspan='2' style='pointer-events:none;'><b>$discount €</b></td></tr>";  
                        }
                        $grand_total = number_format($grand_total, 2);
                        echo "<tr><td colspan='4' style='pointer-events:none;text-align:right;'><b>Γενικό σύνολο:</b></td><td colspan='2' style='pointer-events:none;'><b>$grand_total €</b></td></tr>";  
                    }

                    echo "</tbody>";   
                }
            ?>
            
            </table>
        
        </div>
        <?php
            //action buttons to either clear the basket (session variable) or proceed to checkout
            if(isset($_SESSION['cart']) && !empty($_SESSION['cart']))
            {
                echo "<div class='basket-buttons'>
                    <input type='submit' name='clearBasket' class='btn btn-primary btn-purple' id='clear_basket' value='ΚΑΘΑΡΙΣΜΟΣ ΚΑΛΑΘΙΟΥ' onclick='clearBasket();'></button>
                    <a href='./checkout.php'  name='checkout' class='btn btn-primary btn-purple' id='continue_checkout'>ΣΥΝΕΧΕΙΑ ΠΡΟΣ CHECKOUT</a>
                    </div>";
            }
        ?>
        
        

      

      </div>
    



  </body>
</html>