<?php 
//used to get the correct like icon - button depending if user has already liked the product or not
function getLikeButton($itemId)
{
    if(isset($_SESSION['favorites']))
    {
      $likeButton = "<div><i class='far fa-thumbs-up like-btn' onclick='likeItem($itemId, true)' title = 'Μου αρέσει'></i></div>";
      foreach($_SESSION['favorites'] as $favoriteItem)
      {
        if($itemId == $favoriteItem)
        {
          $likeButton = "<div><i class='fas fa-thumbs-up like-btn' onclick='likeItem($itemId, false)' title='Δεν μου αρέσει'></i></div>";
          break;
        }
      }
      echo $likeButton;
    }
}

?>