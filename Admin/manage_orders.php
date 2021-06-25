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

    //update order status
    if(isset($_POST["order_id"]) && isset($_POST["order_status"]))
    {
        $order_id = $_POST["order_id"];
        $order_status = $_POST["order_status"];

        $sql = "update orders set order_status = $order_status where id = $order_id";
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
  <!-- Admin screen with ALL the pending orders where the status of the order can be updated -->
  <body>
      <div class='container basket-container'>
      <h3 class='purple-label order-management-label'><i class='fas fa-file-alt'></i>Διαχείρηση παραγγελιών</h3>
        <div id = "order_container">
        <h5 class='purple-label'><i class='fas fa-exclamation-circle'></i>Εκκρεμείς παραγγελίες</h5>
        <table class="table table-hover basket-table">
            <thead>
                <tr>
                    <th scope="col">Αριθμός Παραγγελίας</th>
                    <th scope="col">Κατάσταση</th>
                    <th scope="col" colspan='2' class="numeric-input" style='text-align:center;'>Ενέργειες</th>
                </tr>
            </thead>
            <tbody>
            <?php
                //retrieve all the non pending orders
                $sql = "select id, order_status from orders where order_status not in (4,5)";

                $result = mysqli_query($db, $sql);
                if(mysqli_num_rows($result) > 0)
                {
                    //retrieve all the statuses to create a list for the admin to select
                    $sql = "select * from order_statuses";
                    $resultStatuses = mysqli_query($db, $sql);
                    $statuses = array();
                    while($row = mysqli_fetch_assoc($resultStatuses))
                    {
                        $statuses[] = $row;
                    }

                    while($row = mysqli_fetch_assoc($result))
                    {
                        //display all the retrieved order information in table
                        //an "order details" button is present which when clicked makes a modal appear with the order details (items, date, user info etc.)
                        //also a save button exists to update the order to the new status selected. The button is unlocked through javascript when the admin selects a different status
                        $order_id = $row['id'];
                        $order_status = $row['order_status'];
                        echo "<tr>
                                <form name = 'item_form' method = 'post' action='manage_orders.php'>
                                <td ><input name = 'order_id' class='order-id-manage' id=$order_id value = $order_id readonly></input></td>
                                <td>
                                    <select class='form-control' name='order_status' onchange='unlockSaveButton($order_id);'>";
                                    foreach($statuses as $status)
                                    {
                                        $selected = "";
                                        $status_id = $status['id'];
                                        $status_desc = $status['description'];
                                        if($status_id == $order_status)
                                        {
                                            $selected = "selected";
                                        }
                                        echo "<option value=$status_id $selected>$status_desc</option>";
                                    }
                            echo"   </select>
                                </td>
                                <td style='text-align:center;'><button type='submit' id='$order_id-save' class='btn btn-primary btn-purple'  title='Αποθήκευση' name = 'update_order' disabled><i class='fas fa-save'></i></button></td>
                                <td style='text-align:center;'><button type='button' class='btn btn-primary btn-purple' onclick='showOrderDetails($order_id);' title='Λεπτομέρειες'><i class='fas fa-info-circle'></i></button></td>
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
    
            <div class='basket-buttons'>
                <a href='completed_orders.php' class='btn btn-primary btn-purple'><i class='fas fa-check-circle'></i>ΟΛΟΚΛΗΡΩΜΕΝΕΣ</a>
            </div>
            
            <!-- The order details modal -->
            <div class="modal fade" id="orderDetails"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="orderNumber"></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        

      

    </div>
    



  </body>
</html>

