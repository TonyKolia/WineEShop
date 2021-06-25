<?php
    session_start();
    if(isset($_GET['orderId']))
    {
        //all the following html produced is appeneded to the "order details" modal using javascript
        include "db_connection.php";
        $orderId = $_GET['orderId'];

        //the following sql could probably be a stored procedure in db for cleaner management
        //oh well, mistakes were made

        
        $sql = "select customer_type from orders where id = $orderId";
        $result = mysqli_query($db, $sql);
        $row = mysqli_fetch_assoc($result);

        $customerType = $row['customer_type'];

        //the correct query is created depending if the user that made the order was a member or a visitor
        //this is needed for the join clause with the users OR visitors table, depending on the situation
        $sql = "select a.id as id, b.description as order_status, c.item_id as item_id, c.quantity as quantity, d.description as desciption, d.price as price,
        a.name as name, a.surname as surname, a.address as address, a.city as city, a.postal as postal, a.telephone as telephone,";
        if($customerType == "U")
        {
            $customerTypeDesc = "ΜΕΛΟΣ";
            $sql .= "a.user_id as user_id,";
        }
        else
        {
            $customerTypeDesc = "ΕΠΙΣΚΕΠΤΗΣ";
            $sql .= "a.visitor_id as user_id,";
        }

        $sql .= "a.total as total, e.email as email, f.description as payment_method , a.order_date as order_date
        from orders a 
        join order_statuses b on a.order_status = b.id 
        join order_details  c on a.id = c.order_id
        join wines_stock d on c.item_id = d.id
        join payment_methods f on a.payment_method = f.id";

        if($customerType == "U")
        {
            $sql .= " join users e on a.user_id = e.id";
            
        }
        else
        {
            $sql .= " join visitors e on a.visitor_id = e.id";
        }

        $sql.= " where a.id = $orderId";
        
        $result = mysqli_query($db, $sql);
        
        if($result)
        {
            //display the retrieved order details in tables for order - personal details
            $row = mysqli_fetch_assoc($result);
            $order_status = $row['order_status'];
            $order_total = $row['total'];
            $order_user = $row['email'];
            $order_paymentMethod = $row['payment_method'];
            $order_name = $row['name'];
            $order_surname = $row['surname'];
            $order_address = $row['address'];
            $order_city = $row['city'];
            $order_postal = $row['postal'];
            $order_telephone = $row['telephone'];

            $order_date = new DateTime($row['order_date']);
            $order_date = $order_date->format('d/m/Y');
            
            
            $html= "
                <h5 class='purple-label'><i class='fas fa-file-alt'></i>Στοιχεία παραγγελίας</h5>
                <table class='table table-hover order-details'>
                    <thead>
                        <tr>
                            <th scope='col'>Κατάσταση</th>
                            <th scope='col'>Ημερομηνία</th>
                            <th scope='col'>Τρόπος πληρωμής</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$order_status</td>
                            <td>$order_date</td>
                            <td>$order_paymentMethod</td>
                        </tr>
                    </tbody>
                </table>

                <table class='table table-hover order-details'>
                    <thead>
                        <tr>
                            <th scope='col'>Κωδικός</th>
                            <th scope='col'>Περιγραφή</th>
                            <th scope='col'>Τεμάχια</th>
                            <th scope='col'>Τιμή</th>
                            <th scope='col'>Σύνολο</th>
                        </tr>
                    </thead>
                    <tbody>";
                    do
                    {   
                        $itemId = $row['item_id'];
                        $itemDesc = $row['desciption'];
                        $quantity = $row['quantity'];
                        $price = $row['price'];
                        $total = number_format($quantity*$price,2);
                        $html .="
                        <tr>
                            <td>$itemId</td>
                            <td>$itemDesc</td>
                            <td>$quantity</td>
                            <td>$price €</td>
                            <td>$total €</td>
                        </tr>";
                        

                    }while($row = mysqli_fetch_assoc($result));


                $html .= "<tr><td colspan='4' style='pointer-events:none;text-align:right;'><b>Γενικό σύνολο:</b></td><td style='pointer-events:none;'><b>$order_total €</b></td></tr>"; 
                $html .="</tbody>
                </table>
                <h5 class='purple-label'><i class='fas fa-user'></i>Προσωπικά στοιχεία</h5>
                <table class='table table-hover order-details'>
                    <thead>
                        <tr>
                            <th scope='col'>Κατηγορία</th>
                            <th scope='col'>Email</th>  
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$customerTypeDesc</td>
                            <td>$order_user</td>
                        </tr>
                    </tbody>
                </table>
                <table class='table table-hover order-details'>
                    <thead>
                        <tr>
                            <th scope='col'>Όνομα</th>
                            <th scope='col'>Επώνυμο</th>
                            <th scope='col'>Διεύθυνση</th>
                            <th scope='col'>Πόλη</th>
                            <th scope='col'>ΤΚ</th>
                            <th scope='col'>Τηλέφωνο</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>$order_name</td>
                            <td>$order_surname</td>
                            <td>$order_address</td>
                            <td>$order_city</td>
                            <td>$order_postal</td>
                            <td>$order_telephone</td>
                        </tr>
                    </tbody>
                </table>";
                
                echo $html;
        }
        else
        {
            $html = "<h5>Σφάλμα κατά την ανάκτηση των λεπτομεριών της παραγγελίας.</h5>";
            $html .= "<h5>Παρακαλώ προσπαθήστε ξανά.</h5>";
        }



    }
?>