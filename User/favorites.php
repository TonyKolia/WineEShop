<?php
    session_start();
    include "../head.php";
    include "../functions.php";
    include "../db_connection.php";
    //User screen where the user can view all his liked items, represented in cards
    $user_id = $_SESSION['user_id'];
    $sql = "select item_id as id, description, items_in_stock, price from ratings a inner join wines_stock b on a.item_id = b.id where a.user_id = $user_id";
    
    $result = mysqli_query($db, $sql);
    $resCount = mysqli_num_rows($result);
    echo "<h4 class='purple-label items-label'><i class='fas fa-thumbs-up'></i>Αγαπημένα</h5>";
    if($resCount > 0)
    {
        $counter = 0;
        //if the user has favorites, loop over them
        while($row = mysqli_fetch_assoc($result))
        {
            if($counter == 0)
            {
                echo "<div class='row'>";
            }
            if($counter%5 == 0 && $counter != 0 && $counter != $resCount) //every 5 cards, create new row to keep the card size consistent
            {
                echo "</div><div class='row'>";
            }
            $disabled = "";
            //if there is no available stock, disable the "add to cart" button
            if($row['items_in_stock'] == 0)
            {
                $disabled = "disabled";
            }
            $wine_id = $row['id'];
            $wine_desc = $row['description'];
            $wine_price = $row['price'];
            echo "<div class='card'>";
            if(isset($_SESSION['user_id']))
            {
              getLikeButton($wine_id);
            }
            echo"
                  <img class='card-img-top popular-menu-image' src='../Content/Images/Items/$wine_id.jpg' alt='Card image cap'>
                  <div class='card-body'>
                  <a href='../item_details.php?itemId=$wine_id' class='register-link'><h5>$wine_desc</h5></a>
                  <p class='card-text'>$wine_price €</p>
                    <button type='button' class='btn btn-primary btn-purple card-btn' name='addToCart' id='addToCart_$wine_id'onclick='addItemToCart($wine_id);' $disabled>
                      <i class='fas fa-shopping-basket' aria-hidden='true'></i>ΣΤΟ ΚΑΛΑΘΙ
                    </button>
                  </div>
                </div>";
            
            if($counter == $resCount)
            {
                echo "</div>";
            }
            $counter++;
        }
    }
    if($resCount == 0)
    {
        echo "<h6 class='purple-label items-label'>Δεν υπάρχουν προϊόντα στα αγαπημένα.</h6>";
    }

?>