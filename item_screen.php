<?php 

        session_start();
?>
<?php
    include "db_connection.php";
    include "functions.php";
    //Item screen based on user selection of type / category
    if(isset($_GET['categoryId']) && isset($_GET['typeId']))
    {
        include "head.php";
        $categoryId = $_GET['categoryId'];
        $typeId = $_GET['typeId'];
        //if the user selects the "all" option the id of that type / category is said as all to mark that
        if($categoryId == "all" && $typeId == "all")
        {
            $title = "ΟΛΑ ΤΑ ΚΡΑΣΙΑ";
        } 
        else
        {
            //else the corresponding id is selected as retrieved from db
            if($typeId != "all")
            {
                $sql = "select description from winetypes where id = $typeId";
                $result = mysqli_query($db, $sql);
                $row = mysqli_fetch_assoc($result);
                $type = $row['description'];
            }
            else
            {
                $type = "ΟΛΑ";
            }
            
            if($categoryId != "all")
            {
                
                $sql = "select description from winecategories where id = $categoryId";
                $result = mysqli_query($db, $sql);
                $row = mysqli_fetch_assoc($result);
                $category = $row['description'];
            }
            else
            {
                $category = "ΟΛΑ";
            }

            $title = $type." - ".$category;
        }

        


        echo "<h5 class='purple-label items-label'>$title</h5>";

        $sql = "select * from wines_stock";
        //construct the query by adding clauses based on user selection
        if($typeId != "all")
        {
            $sql .= " where type = $typeId";
            if($categoryId != "all")
            {
                $sql .= " and category = $categoryId";
            }
        }


        
        
        $result = mysqli_query($db, $sql);
        $resCount = mysqli_num_rows($result);
        if($resCount > 0)
        {
            //display the retrieved items in cards
            $counter = 0;
            while($row = mysqli_fetch_assoc($result))
            {
                if($counter == 0)
                {
                    echo "<div class='row'>";
                }
                //every 5 cards create a new row, that's to keep card size consistent
                if($counter%5 == 0 && $counter != 0 && $counter != $resCount)
                {
                    echo "</div><div class='row'>";
                }

                $disabled = "";
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
                      <img class='card-img-top popular-menu-image' src='./Content/Images/Items/$wine_id.jpg' alt='Card image cap'>
                      <div class='card-body'>
                      <a href='./item_details.php?itemId=$wine_id' class='register-link'><h5>$wine_desc</h5></a>
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
            echo "<h6 class='purple-label items-label'>Δεν βρέθηκαν προϊόντα για τα συγκεκριμένα κριτήρια.</h6>";
        }
    }
?>