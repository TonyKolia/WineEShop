<div class="card-deck">
  <?php 
    //home page product display (popular, best, new)
    //the produced html is appended in the homepage using javascript
    if(session_status() === PHP_SESSION_NONE)
      session_start();
    include "db_connection.php";
    include "functions.php";
    $section = "popular"; //set as default
    if(isset($_GET['section']))
    {
      $section = $_GET['section'];
    }
    
    //create an sql query based on the user selection
    switch($section)
    {
      case "popular": //top 4 products with most sales
        $sql = "select id, items_in_stock, description, price, sum(quantity)as number from order_details a
                join wines_stock b on a.item_id = b.id
                group by item_id order by number desc limit 4";
        break;
      case "best"://top 4 products with most likes, left join is used to bring items with 0 likes to fill the screen in case of less than 4 with any likes
        $sql = "select id, description, items_in_stock, price, COUNT(item_id) as likes_count from wines_stock a 
                left join ratings b on a.id = b.item_id
                GROUP by id order by likes_count desc limit 4";
        break;
      case "new"://top 4 newest added products
        $sql ="select * from wines_stock order by id desc, date_of_addition DESC limit 4";
        break;
      default:
        break;
    }

    $result = mysqli_query($db, $sql);
    if(mysqli_num_rows($result) > 0)
    {
      while($row = mysqli_fetch_assoc($result))
      {
        $wine_id = $row['id'];
        $wine_desc = $row['description'];
        $wine_price = $row['price'];
        $disabled = "";
        //check if there is available stock, else lock the "add to cart button"
        if($row['items_in_stock'] == 0)
        {
          $disabled = "disabled";
        }
        echo "<div class='card'>";
        if(isset($_SESSION['user_id']))
        {
          getLikeButton($wine_id);
        }
        echo"
        <img class='card-img-top popular-menu-image' src='./Content/Images/Items/$wine_id.jpg' alt='Card image cap'>
        <div class='card-body'>
          <a href='./item_details.php?itemId=$wine_id' class='register-link'><h5>$wine_desc</h5></a>
          <p class='card-text'>$wine_price €</p>
          <div>
            <button type='button' class='btn btn-primary btn-purple card-btn' name='addToCart' id='addToCart_$wine_id'onclick='addItemToCart($wine_id);' $disabled>
              <i class='fas fa-shopping-basket' aria-hidden='true'></i>ΣΤΟ ΚΑΛΑΘΙ
            </button>
          </div>
          
        </div>
      </div>";
      }
    }

    if(mysqli_num_rows($result) == 0)
    {
      echo "<p> Δεν βρέθηκαν αντικείμενα για τα συγκεκριμένα κριτήρια</p>";
    }

  ?>
</div>