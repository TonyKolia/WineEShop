<?php
    session_start();
    //redirected here after logout selection
    //clears the session variabled regarding the user
    if(isset($_SESSION['user']))
    {
        unset($_SESSION['user']);
    }
    if(isset($_SESSION['admin']))
    {
        unset($_SESSION['admin']);
    }
    if(isset($_SESSION['user_id']))
    {
        unset($_SESSION['user_id']);
    }

    header('Location: ./index.php');

?>