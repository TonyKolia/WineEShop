<?php
    //test
    session_start();
    include "db_connection.php";

    //cart setup
    //here is the logic for all the actions that the user can perform involving the cart
    //adding item from product screen, removing from cart, increase / decrease the counter of certain item
    if(isset($_GET['updateBasketCounter']))
    {
        $numberOfItemsInCart = 0;
        if(isset($_SESSION['cart']))
        {
            foreach($_SESSION['cart'] as $item)
            {
                $numberOfItemsInCart += $item['quantity'];
            }
        }
        echo $numberOfItemsInCart;
        exit;
    }

    if(isset($_POST['clearBasket']))
    {
        unset($_SESSION['cart']);
        exit;
    }

    if(empty($_SESSION['cart']))
    {
        $_SESSION['cart'] = array();
    }

    if(isset($_POST['itemId']))
    {
        $itemId = $_POST['itemId'];

        $sql = "select items_in_stock from wines_stock where id = $itemId";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_assoc($result);
        $itemsInStock = $row['items_in_stock'];

        $text = "";
        $item_exists = false;
        $items = count($_SESSION['cart']);
        //we loop all the items in the cart (held in session variable) to check if the product selected already exists 
        for($i=0; $i<$items;$i++)
        {
            //if it exists
            if($_SESSION['cart'][$i]['itemId'] == $itemId)
            {
                //and adding is the action
                if(isset($_POST['add']))
                {
                    //increase the existing counter ONLY IF the available stock allows
                    if($itemsInStock >= $_SESSION['cart'][$i]['quantity'] + 1)
                    {
                        $_SESSION['cart'][$i]['quantity'] = $_SESSION['cart'][$i]['quantity'] + 1;
                        $added = true;
                    }
                    else
                    {
                        $added = false;
                    }
                }

                //if the action is remove (reduce the counter inside the basket)
                if(isset($_POST['remove']))
                {
                    //reduce the existing counter but not below 0
                    if($_SESSION['cart'][$i]['quantity'] - 1 > 0)
                    {
                        $_SESSION['cart'][$i]['quantity'] = $_SESSION['cart'][$i]['quantity'] - 1;
                        $removed = true;
                    }
                    else
                    {
                        $removed = false;
                    }
                }

                //if the action is delete (completely remove from basket)
                if(isset($_POST['delete']))
                {
                    //remove from session table using splice to rebuild the indexes since we loop using an indexer ($i)
                    array_splice($_SESSION['cart'], $i, 1);
                    $deleted = true;
                }

                //mark the item as a existing (used below)
                $item_exists = true;
                break;
            }
        }
       
        //if the item doesnt exist and the action is add
        if(!$item_exists && isset($_POST['add']))
        {
            //add the item in the basket ONLY IF the available stock allows
            if($itemsInStock >= 1)
            {
                array_push($_SESSION['cart'], ['itemId' => $itemId, 'quantity' => 1]);
                $added = true;
            }
            else
            {
                $added = false;
            }
        }

        //return the right message to the user based on the action & result of that action
        $class = "success";
        if(isset($_POST['add']))
        {
            if($added)
            {
                $text = "Προστέθηκε στο καλάθι!";
            }
            else
            {
                $text = "Έχετε υπερβεί τον αριθμό του αποθέματος για το συγκεκριμένο προϊόν.";
                $class = "danger";
            }
        }
        else if(isset($_POST['remove']))
        {
            if($removed)
            {
                $text = "Αφαιρέθηκε από το καλάθι!";
            }
        }
        else if(isset($_POST['delete']))
        {
            if($deleted)
            {
                $text = "Διαγράφθηκε από το καλάθι!";
            }
        }

        if($text != "")
        {
            echo "<div class='alert alert-$class alert-position' role='alert'>
            $text
            </div>";
        }
    }

?>