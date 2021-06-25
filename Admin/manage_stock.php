<?php 
    session_start();
    include "../db_connection.php";
?>


<?php
    //if not an admin and user gets here by url, redirect
    if(!isset($_SESSION["admin"]) || !$_SESSION["admin"])
    {
        header('Location: ../index.php');
        exit;
    }

    //update existing item
    if(isset($_POST["update_item"]))
    {
        $item_id = $_POST["item_id"];
        $item_description = $_POST["item_desc"];
        $item_information = $_POST["item_info"];
        $item_type = $_POST["item_type"];
        $item_category = $_POST["item_category"];
        $item_price = $_POST["item_price"];
        $item_stock = $_POST["item_stock"];

        $sql = "update wines_stock set type = $item_type, category = $item_category, price = $item_price, items_in_stock = $item_stock, description = '$item_description', information = '$item_information' where id = $item_id";
        $result = mysqli_query($db, $sql);
        unset($_POST);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }

    //insert new item
    if(isset($_POST["insert_item"]))
    {
        $item_description = $_POST["item_desc"];
        $item_information = $_POST["item_info"];
        $item_type = $_POST["item_type"];
        $item_category = $_POST["item_category"];
        $item_price = $_POST["item_price"];
        $item_stock = $_POST["item_stock"];
        
        $sql = "insert into wines_stock values(null, $item_type, $item_category, $item_price, $item_stock, '$item_description', '$item_information', CURRENT_DATE())";
        $result = mysqli_query($db, $sql);
        unset($_POST);
        header("Location: ".$_SERVER['PHP_SELF']);
        exit;
    }
?>


<!doctype html>
<html lang="en">
  <head>
      <?php include "../head.php"; ?>
  </head>
  <!-- Admin screen with all the existing items 
       From here an existing item can be updated (name, information, price, stock etc)
       Also from here a new item can be added to the stock by inserting the data to the "new item" modal -->
  <body>
      <div class='container basket-container'>
      <h3 class='purple-label order-management-label'><i class='fas fa-boxes'></i>Διαχείρηση αποθέματος</h3>
        <div id = "stock_container">
        <table class="table table-hover basket-table">
            <thead>
                <tr>
                    <!-- the first th contains a + button that when clicked makes the "add new" modal appear -->
                    <th scope="col" class="numeric-input">Κωδικός <i style="padding:0;" class="fas fa-plus-circle new-item-btn" title="Προσθήκη" onclick="showItemAdditionScreen();"></i></th>
                    <th scope="col">Περιγραφή</th>
                    <th scope="col">Πληροφορίες</th>
                    <th scope="col">Τύπος</th>
                    <th scope="col">Κατηγορία</th>
                    <th scope="col" class="numeric-input">Τιμή</th>
                    <th scope="col" class="numeric-input">Τεμάχια</th>
                    <th scope="col" colspan="2"  class="numeric-input"  style='text-align:center;'>Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
            <?php
                
                $sql = "select * from wines_stock";

                $result = mysqli_query($db, $sql);
                if(mysqli_num_rows($result) > 0)
                {
                    //get all the type to make list for admin for update
                    $sql = "select * from winetypes";
                    $resultTypes = mysqli_query($db, $sql);
                    $types = array();
                    while($row = mysqli_fetch_assoc($resultTypes))
                    {
                        $types[] = $row;
                    }
                    //get all the categories to make list for admin for update
                    $sql = "select * from winecategories";
                    $resultCategories = mysqli_query($db, $sql);
                    $categories = array();
                    while($row = mysqli_fetch_assoc($resultCategories))
                    {
                        $categories[] = $row;
                    }

                    while($row = mysqli_fetch_assoc($result))
                    {
                        $item_id = $row['id'];
                        $item_description = $row['description'];
                        $item_information = $row['information'];
                        $item_type = $row['type'];
                        $item_category = $row['category'];
                        $item_price = $row['price'];
                        $item_stock = $row['items_in_stock'];
                        //a form is created for every row and its input is posted to the server to update the selected item
                        echo "<tr>
                                <form name = 'item_form' method = 'post' action='manage_stock.php'>
                                <td><input style='width:fit-content;' name = 'item_id' class='order-id-manage' id=item_$item_id value = $item_id  readonly></input></td>
                                <td><input class='form-control' style='width:100%;' type='text' id='$item_id-desc' name = 'item_desc' value = '$item_description' disabled></input></td>
                                <td><input class='form-control' style='width:100%;' type='text' id='$item_id-info' name = 'item_info' value = '$item_information' disabled></input></td>
                                <td>
                                    <select class='form-control' name='item_type' id='$item_id-type' disabled>";
                                    foreach($types as $type)
                                    {
                                        //set as default in the list the existing item type
                                        $selected = "";
                                        $type_id = $type['id'];
                                        $type_desc = $type['description'];
                                        if($type_id == $item_type)
                                        {
                                            $selected = "selected";
                                        }
                                        echo "<option value=$type_id $selected>$type_desc</option>";
                                    }
                            echo"   </select>
                                </td>";
                            echo"<td>
                                    <select class='form-control' name='item_category' id='$item_id-category' disabled>";
                                    foreach($categories as $category)
                                    {
                                        //set as default in the list the existing item category
                                        $selected = "";
                                        $category_id = $category['id'];
                                        $category_desc = $category['description'];
                                        if($category_id == $item_category)
                                        {
                                            $selected = "selected";
                                        }
                                        echo "<option value=$category_id $selected>$category_desc</option>";
                                    }
                            echo"   </select>
                            </td>";
                            echo"<td><input class='form-control numeric-fields' type='number' name = 'item_price' id='$item_id-price' step='any' min='0' value = '$item_price' disabled></input></td>";
                            echo"<td><input class='form-control numeric-fields' type='number' name = 'item_stock' id='$item_id-stock' min='0' value = '$item_stock' disabled></input></td>"; 
                            //edit button is added which unlocks all the fields for edit for this row, this also unlocks the save button
                            //also after edit button click a cancel edit button appears which locks the fields again and the save button as well to avoid accidental updates
                            echo"<td style='text-align:center;'><button type='button' id='$item_id-edit' class='btn btn-primary btn-purple' onclick='enableEditing($item_id);' title='Επεξεργασία' name = 'save_item'><i class='fas fa-pen'></i></button>
                            <button style='display:none;' type='button' id='$item_id-cancel' class='btn btn-primary btn-purple' onclick='cancelEditing($item_id);' title='Ακύρωση' name = 'cancel_edit'><i class='fas fa-times-circle'></i></button>
                            </td>
                            
                            <td style='text-align:center;'><button id='$item_id-save' type='submit' class='btn btn-primary btn-purple'  title='Αποθήκευση' name = 'update_item' disabled><i class='fas fa-save'></i></button></td>
                                </form>
                            </tr>";
                    }
                }
                else
                {
                    echo "<tr><td colspan='3'>Δεν υπάρχουν παραγγελίες σε εκκρεμότητα.<td></tr>";
                }
            ?>
            </tbody>
            </table>
    
            <!-- The add new item modal -->
            <div class="modal fade" id="itemAdditionScreen"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content" style="width: max-content;">
                        <div class="modal-header">
                            <h5 class="modal-title" id="orderNumber">Προσθήκη νέου προϊόντος</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        <table class='table table-hover order-details'>
                            <thead>
                                <th scope="col">Κωδικός</th>
                                <th scope="col">Περιγραφή</th>
                                <th scope="col">Πληροφορίες</th>
                                <th scope="col">Τύπος</th>
                                <th scope="col">Κατηγορία</th>
                                <th scope="col" class="numeric-input">Τιμή</th>
                                <th scope="col" class="numeric-input">Τεμάχια</th>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                <tr>
                                    <!-- a form is created and the post is handled in this file at the top -->
                                    <form name = 'new_item_form' method = 'post' action='manage_stock.php'>
                                        <?php 
                                            $sql = "select max(id) as id from wines_stock";
                                            $result = mysqli_query($db, $sql);
                                            $row = mysqli_fetch_assoc($result);
                                            if($row['id'] == null || $row['id'] == 0)
                                            {
                                                $newItemId = 1;
                                            }
                                            else
                                            {
                                                $newItemId = $row['id'] + 1;
                                            }
                                        ?>
                                        <td><?php echo $newItemId ?></td>
                                        <td><input class='form-control' style='width:100%;' type='text' name = 'item_desc' placeholder="Περιγραφή" required></input></td>
                                        <td><input class='form-control' style='width:100%;' type='text' name = 'item_info' placeholder="Πληροφορίες" required></input></td> 
                                        <td>
                                            <select class='form-control' name='item_type'>
                                                <?php 
                                                    foreach($types as $type)
                                                    {
                                                        $type_id = $type['id'];
                                                        $type_desc = $type['description'];
                                                        echo "<option value=$type_id>$type_desc</option>";
                                                    }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select class='form-control' name='item_category'>
                                                <?php 
                                                   foreach($categories as $category)
                                                   {
                                                       $category_id = $category['id'];
                                                       $category_desc = $category['description'];
                                                       echo "<option value=$category_id>$category_desc</option>";
                                                   }
                                                ?>
                                            </select>
                                        </td>
                                        <td><input class='form-control numeric-fields' type='number' name = 'item_price' step='any' min='0' required></input></td>
                                        <td><input class='form-control numeric-fields' type='number' name = 'item_stock' min='0' required></input></td>
                                        <td style='text-align:center;'><button type='submit' class='btn btn-primary btn-purple'  title='Αποθήκευση' name = 'insert_item'><i class='fas fa-save'></i></button></td>
                                    </form>
                                </tr>
                            </tbody>
                        </table>
                        <i class="fas fa-info-circle"></i><small><i>Μετά την προσθήκη, μην ξεχάσετε να προσθέσετε την αντίστοιχη εικόνα στον φάκελο items με όνομα <?php echo "'$newItemId.jpg'"?></i></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        

      

    </div>
    



  </body>
</html>