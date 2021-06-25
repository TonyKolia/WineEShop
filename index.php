<?php 
    session_start();
?>
<!doctype html>
<html lang="en">
  <head>
  <?php include "head.php"?>
  <?php include "db_connection.php"?>
  <!-- Home page -->
  <body>
  <div class=container id='MainContainer'>
  <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img class="d-block" src="./Content/Images/krasi1.jpg" alt="First slide">
    </div>
    <div class="carousel-item">
      <img class="d-block" src="./Content/Images/krasi2.jpg" alt="Second slide">
    </div>
    <div class="carousel-item">
      <img class="d-block" src="./Content/Images/krasi3.jpg" alt="Third slide">
    </div>
  </div>
  <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>

<div class="popular-section">

  <div style="text-align:center">
    <ul class="nav nav-tabs list-inline">
      <li class="nav-item">
        <a class="nav-link active popular-text" id='popular' href="#" onclick="popularButtonClick('popular')">ΔΗΜΟΦΙΛΗ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link popular-text" id='best' href="#" onclick="popularButtonClick('best')">ΚΟΡΥΦΑΙΑ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link popular-text" id='new' href="#" onclick="popularButtonClick('new')">ΝΕΑ</a>
      </li>
    </ul>
  </div>
  <div id='popular_content'>
    <?php include "popular_content.php"?>
  </div>
  






  </div>
  





  </body>
</html>