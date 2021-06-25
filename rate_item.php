<?php
    session_start();
    include "db_connection.php";

    if(isset($_POST['ItemId']))
    {
        //like - remove like item logic
        $itemId = $_POST['ItemId'];
        $userId = $_SESSION['user_id'];

        if(isset($_POST['like']))
        {
            if($_POST['like'] == "true")
            {
                //if like, insert the like in db
                $sql = "insert into ratings values ($userId, $itemId)";
               
            }
            else
            {   
                //else remove the like from db
                $sql = "delete from ratings where user_id = $userId and item_id = $itemId";
            }
            $result = mysqli_query($db, $sql);
            
            //return the corresponding message to user
            if($result)
            {
                if($_POST['like'] == "true")
                {
                    $text = "Προστέθηκε στα αγαπημένα.";
                }
                else
                {
                    $text = "Αφαιρέθηκε από τα αγαπημένα.";
                }
                echo "<div class='alert alert-success alert-position' role='alert'>
                $text   
                </div>";
            }
            else
            {
                echo "<div class='alert alert-danger alert-position' role='alert'>
                Παρουσιάστηκε σφάλμα, παρακαλώ προσπαθήστε ξανά.
                </div>";
            }
        }
    }

?>