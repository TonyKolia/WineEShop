<?php 
    session_start();
    include "../db_connection.php";
?>

<!doctype html>
<html lang="en">
  <head>
      <?php include "../head.php"; ?>
  </head>
  <!-- User screen where the user's non-pending orders (completed, cancelled) are displayed 
       from here the user can see the order details through the "order details" modal  -->
  <body>
      <div class='container basket-container'>
      <h3 class='purple-label order-management-label'><i class='fas fa-file-alt'></i>Οι παραγγελίες μου</h3>
        <div id = "order_container">
        <h5 class='purple-label'><i class='fas fa-check-circle'></i>Ολοκληρωμένες παραγγελίες</h5>
        <table class="table table-hover basket-table">
            <thead>
                <tr>
                    <th scope="col">Αριθμός Παραγγελίας</th>
                    <th scope="col">Κατάσταση</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
            <?php
                $userId = $_SESSION['user_id'];
                //get all the non-pending orders for the users
                $sql = "select a.id as id, description from orders a join order_statuses b on a.order_status = b.id where order_status in (4,5) and user_id = $userId";

                $result = mysqli_query($db, $sql);
                if(mysqli_num_rows($result) > 0)
                {
                    //display them as table rows
                    while($row = mysqli_fetch_assoc($result))
                    {
                        $order_id = $row['id'];
                        $order_status = $row['description'];
                        echo "<tr>
                                <form name = 'item_form'>
                                <td>$order_id</td>
                                <td>$order_status</td>
                                <td style='text-align:center;'><button type='button' class='btn btn-primary btn-purple' onclick='showOrderDetails($order_id);' title='Λεπτομέρειες'><i class='fas fa-info-circle'></i></button></td>
                                </form>
                            </tr>";
                    }
                }
                else
                {
                    echo "<tr><td colspan='3'>Δεν υπάρχουν ολοκληρωμένες παραγγελίες.<td></tr>";
                }
            ?>
            </tbody>
            </table>
    
            <div class='basket-buttons'>
                <a href='./user_orders.php' class='btn btn-primary btn-purple'><i class='fas fa-exclamation-circle'></i>ΕΚΚΡΕΜΕΙΣ</a>
            </div>
            
            <!-- the order details modal, its content is set using javascript-->
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

