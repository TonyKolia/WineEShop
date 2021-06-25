<?php
    session_start();
    include "db_connection.php";

    if(isset($_GET['itemId']))
    {
        //used as a "Product Screen" where more details are available for the product 
        $itemId = $_GET['itemId'];
        $sql = "select a.items_in_stock as items_in_stock, a.description as description, a.information as information, a.price as price, b.description as type, c.description as category from wines_stock a 
        join winetypes b on a.type = b.id 
        join winecategories c on a.category = c.id
        where a.id = $itemId";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_assoc($result);

        $item_desc = $row['description'];
        $item_info = $row['information'];
        $item_price = $row['price'];
        $item_type = $row['type'];
        $item_category = $row['category'];
        $disabled = "";
        //check the stock, if 0 then the "add to cart" button is disabled
        if($row['items_in_stock'] == 0)
        {
            $disabled = "disabled";
        }
    }
    else
    {

    }


?>

<!doctype html>
<html lang="en">
  <head>
  <?php include "head.php";
        include "functions.php";
  ?>
  
  
  <body>
    <div class='card item-details'>
        <?php 
        if(isset($_SESSION['user_id']))
        {
          getLikeButton($itemId);
        }
        ?>
        <img class='card-img-top popular-menu-image' src='./Content/Images/Items/<?php echo $itemId?>.jpg' alt='Card image cap'>
        <div class='card-block px-2'>
        <div class='card-body'>
        <h5><?php echo $item_desc?></h5>
        <p class='card-text'><?php echo $item_info?></p>
        <p class='card-text'><?php echo $item_type.', '.$item_category?></p>
        <p class='card-text'><?php echo $item_price.' €'?></p>
          <button type='button' class='btn btn-primary btn-purple card-btn' name='addToCart' id='addToCart_<?php echo $itemId ?>'onclick='addItemToCart(<?php echo $itemId?>);' <?php echo $disabled?>>
            <i class='fas fa-shopping-basket' aria-hidden='true'></i>ΣΤΟ ΚΑΛΑΘΙ
          </button>
        </div>
        
        </div>
    </div>

  </body>
</html>