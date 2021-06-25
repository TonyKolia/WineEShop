<head>
    <title>
        Wine Shop
    </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="/WineShop/Content/CSS/style.css"/>   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">                
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script src="/WineShop/Scripts/script.js"></script>

    <?php include "db_connection.php"?>
    <?php
        //this is the navigation bar present in every page
        //since this is used in every page here we have data that we always need
        //depending on the user kind (visitor, member, admin) different options are available
        if(isset($_SESSION['user_id']))
        {
            //member favorites retrieve
            //we need them kept in session to setup the like button in the products
            $user_id = $_SESSION['user_id'];
            $sql = "select item_id from ratings where user_id = $user_id";
            $result = mysqli_query($db, $sql);
            $_SESSION['favorites'] = array();
            while($row = mysqli_fetch_assoc($result))
            {
                array_push($_SESSION['favorites'], $row['item_id']);
            }
        }

        $sql = "select * from winetypes";
        $wine_types_result = mysqli_query($db, $sql);
    ?>
    <nav class="navbar navbar-expand-sm navbar-dark bg-primary"  id='mynavbar'>
        <div onclick='reloadHome()' class='logo' title='Αρχική'>
            <img src='/WineShop/Content/Images/wine.svg' class=" img-logo" onclick='reloadHome()'/><i>Καλάάά...κρασιά!</i>
        </div>

        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId"
            aria-expanded="false" aria-label="Toggle navigation"></button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0 center-navbar-items">
            <?php 
                    echo "<li class='nav-item navbar-items-distance'>
                    <a class='nav-link' href='#' id='all-all' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' onclick=\"selectCategory('all-all');\">
                      ΟΛΑ ΤΑ ΚΡΑΣΙΑ
                    </a>";
                    if(mysqli_num_rows($wine_types_result) > 0)
                    {
                      while($row = mysqli_fetch_assoc($wine_types_result))
                      {
                        $wine_type = $row['description'];
                        $id = $row['id'];
                        echo "<li class='nav-item dropdown navbar-items-distance'>
                        <a class='nav-link dropdown-toggle' href='#' id='navbarDropdown_$id' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                          $wine_type
                        </a>
                        <div class='dropdown-menu' aria-labelledby='navbarDropdown_$id'>";
                        $sql = "select * from winecategories";
                        $wine_categories_result = mysqli_query($db, $sql);
                        if(mysqli_num_rows($wine_categories_result) > 0)
                        {
                            echo "<a class='dropdown-item' href='#' id='$id-all' onclick=\"selectCategory('$id-all');\">ΟΛΑ</a>";
                            while($row2 = mysqli_fetch_assoc($wine_categories_result))
                            {
                                $wine_category = $row2['description'];
                                $id2 = $row2['id'];
                                echo "<a class='dropdown-item' href='#' id='$id-$id2' onclick=\"selectCategory('$id-$id2');\">$wine_category</a>";
                            }
                        }
                        echo "</div>
                        </li>";
                      }
                    }
                ?>
                
            </ul>
           
            <?php
                if(isset($_SESSION['admin']) && $_SESSION['admin'] == true)
                {
                    echo "<div class='nav-item dropdown navbar-items-distance'>
                                <a class='nav-link dropdown-toggle login-link' href='#' id='navbarDropdown_$id' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                <i class='fas fa-tools'></i>ΔΙΑΧΕΙΡΙΣΗ
                                </a>
                                <div class='dropdown-menu' aria-labelledby='navbarDropdown_$id'>
                                <a class='dropdown-item' href='/WineShop/Admin/manage_orders.php' id='manage_orders'><i class='fas fa-file-alt'></i>ΠΑΡΑΓΓΕΛΙΕΣ</a>
                                <a class='dropdown-item' href='/WineShop/Admin/manage_stock.php' id='manage_stock'><i class='fas fa-boxes'></i>ΑΠΟΘΕΜΑ</a>
                                <a class='dropdown-item' href='/WineShop/Admin/manage_users.php' id='manage_users'><i class='fas fa-user'></i>ΧΡΗΣΤΕΣ</a>
                                </div>
                            </div>";
                }
                else
                {   
                     //items in cart counter setup
                    $numberOfItemsInCart = 0;
                    if(isset($_SESSION['cart']))
                    {
                        foreach($_SESSION['cart'] as $item)
                        {
                            $numberOfItemsInCart += $item['quantity'];
                        }
                    }
                   
                    echo "<button type='button' class='btn btn-link login-link' onclick='redirectToCart();'>
                        <i class='fas fa-shopping-basket'></i><a class='shopping-cart-btn' aria-hidden='true' name='addToCart' id='cart_btn'>ΚΑΛΑΘΙ
                        <span class='badge badge-light' id='numberOfItemsInCart'>$numberOfItemsInCart</span>
                        </a>
                    </button>";
                }

                if(isset($_SESSION['user']))
                {   
                    $user = $_SESSION['user'];
                    if(isset($_SESSION['admin']) && $_SESSION['admin'] == true)
                    {
                        $icon = "fa-user-cog";
                    }
                    else
                    {
                        $icon ="fa-user";
                    }
                    echo "<div class='nav-item dropdown navbar-items-distance'>
                                <a class='nav-link dropdown-toggle login-link' href='#' id='navbarDropdown_$user' role='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
                                <i class='fas $icon'></i>$user
                                </a>
                                <div class='dropdown-menu' aria-labelledby='navbarDropdown_$user'>";
                                if(!isset($_SESSION['admin']) || $_SESSION['admin'] != true)
                                {
                                    echo "<a class='dropdown-item' href='/WineShop/User/user_orders.php' id='user_orders'><i class='fas fa-file-alt'></i>ΠΑΡΑΓΓΕΛΙΕΣ</a>";
                                    echo "<a class='dropdown-item' href='/WineShop/User/favorites.php' id='favorites'><i class='fas fa-thumbs-up'></i>ΑΓΑΠΗΜΕΝΑ</a>";
                                }
                            echo"   <a class='dropdown-item' href='/WineShop/logout.php' id='manage_users'><i class='fas fa-sign-out-alt'></i>ΑΠΟΣΥΝΔΕΣΗ</a>
                                </div>
                             </div>";
                }
                else
                {
                    echo "<button type='button' class='btn btn-link login-link' onclick='redirectToLogin();'>
                            <i class='fas fa-sign-in-alt'></i>ΣΥΝΔΕΣΗ
                        </button>";
                }
            ?>
            <i id="dark_mode_icon" class='fas fa-moon' style="color:white;cursor:pointer;" title = "Ενεργοποίηση dark mode" onclick="toggleDarkMode();"></i>
        </div>
    </nav>
    <!-- Hidden form used to redirect the user to the item screen based on his selection
        The form fields are filled and the form is submitied using javascript on a type / category selection -->
    <form id='wineSelectionForm' method='get' action='/WineShop/item_screen.php'>
                <input type = 'hidden' id = 'type_choice' name ='typeId'>
                <input type = 'hidden' id = 'category_choice' name ='categoryId'>
    </form>
</head>