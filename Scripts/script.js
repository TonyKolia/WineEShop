//apply dark mode on load if selected
$(document).ready(function(){
    var darkMode = localStorage.getItem('dark_mode'); //check the browser local storage
    if(darkMode == "true"){ //if dark mode should be enabled
        $('body').addClass('dark'); //add class
        //manage button
        $('#dark_mode_icon').attr('title', 'Απενεργοποίηση dark mode');
        $('#dark_mode_icon').removeClass('fa-moon');
        $('#dark_mode_icon').addClass('fa-sun');
    }
});

//fill the hidden form fields and submit the form on user type/category selection
function selectCategory(ids){
    var idsArray = ids.split("-");
    if(idsArray.length == 2){
        $('#type_choice').val(idsArray[0]);
        $('#category_choice').val(idsArray[1]);
        $('#wineSelectionForm').submit();
    }
}

//used by the "add button" and the + button in cart to increase counter
function addItemToCart(itemId, refresh=false){
    $.post("/WineShop/cart_setup.php", { itemId: itemId, add: true },
    function(data){
        $('body').append(data); //append the returned message
        $('.alert').delay(1000).fadeOut('slow');
        if(refresh){
            location.reload();
        }
        else{ //update the total cart items counter in the nav bar "manually", otherwise the refresh above makes the update while in cart
            $.get("/WineShop/cart_setup.php", { updateBasketCounter: true },
                function (data) {
                    $('#numberOfItemsInCart').text(data);
            });
        }
    });
}

//used by the - button in the cart to decrease item counter
function removeItemFromCart(itemId, refresh){
    $.post("/WineShop/cart_setup.php", { itemId: itemId, remove: true },
    function(data){
        $('body').append(data); //append the returned message
        $('.alert').delay(1000).fadeOut('slow');
        if(refresh){
            location.reload();
        }
    });
}

//used by the delete item in the cart to remove the item completely
function deleteItemFromCart(itemId){
    $.post("/WineShop/cart_setup.php", { itemId: itemId, delete: true },
        function (data) {
            $('body').append(data); //append the returned message
            $('.alert').delay(1000).fadeOut('slow');
            location.reload();
    });
}

//toggle dark mode
function toggleDarkMode(){
    if($('body').hasClass('dark')){ //if already in dark mode
        $('body').removeClass('dark'); //remove it
        localStorage.setItem('dark_mode', false); //mark the removal in browser storage to be always available
        //configure the button
        $('#dark_mode_icon').attr('title', 'Ενεργοποίηση dark mode');
        $('#dark_mode_icon').removeClass('fa-sun');
        $('#dark_mode_icon').addClass('fa-moon');
    }
    else{
        $('body').addClass('dark'); //add dark mode class
        localStorage.setItem('dark_mode', true);//mark the removal in browser storage to be always available
        //configure the button
        $('#dark_mode_icon').attr('title', 'Απενεργοποίηση dark mode');
        $('#dark_mode_icon').removeClass('fa-moon');
        $('#dark_mode_icon').addClass('fa-sun');
    }
}

//completely empty the cart
function clearBasket(){
    $.post("/WineShop/cart_setup.php", {clearBasket: true},
        function (data) {
            window.location.href = "/WineShop/cart.php";
    });
}

function redirectToCart(){
    window.location.href = "/WineShop/cart.php";
}

function redirectToLogin(){
    window.location.href = "/WineShop/login_page.php";
}

function reloadHome(){
    window.location.href='/WineShop/index.php';
}

//set the home screen product section based on the user choice (popular, best, new)
function setPopularSectionContent(btnName){
    $.get("/WineShop/popular_content.php", { section: btnName },
        function (data) {
            $('#popular_content').html(data); //append the returned html to the home screen
        });
}

//set the home screen product screen selected option based on the user choice (popular, best, new)
function setPopularSectionSelected(btnName){
    $('#popular').removeClass('active');
    $('#best').removeClass('active');
    $('#new').removeClass('active');
    $('#'+btnName).addClass('active');
}

//home screen item section manage
function popularButtonClick(btnName){
    setPopularSectionSelected(btnName);
    setPopularSectionContent(btnName);
}

function removeItem(itemId){
    var counter = parseInt(document.getElementById(itemId+"-counter").value);
}

//show the order details modal
function showOrderDetails(orderId){
    $.get("/WineShop/order_details.php", {orderId: orderId},
        function (data) {
            $('#orderNumber').text('Αριθμός παραγγελίας: '+orderId);
            $('.modal-body').html(data); //append the returned html
            $('#orderDetails').modal('show'); //show the modal
    });
}

//unlock the save button on admin selection of order status from list
function unlockSaveButton(orderId){
    $('#'+orderId+'-save').removeAttr('disabled');
}

//cancel edit, lock the fields and the save button, hide the cancel button and show the edit button
function cancelEditing(itemId){
    $('#'+itemId+'-desc').attr('disabled','disabled');
    $('#'+itemId+'-info').attr('disabled','disabled');
    $('#'+itemId+'-type').attr('disabled','disabled');
    $('#'+itemId+'-category').attr('disabled','disabled');
    $('#'+itemId+'-price').attr('disabled','disabled');
    $('#'+itemId+'-stock').attr('disabled','disabled');
    $('#'+itemId+'-save').attr('disabled','disabled');
    $('#'+itemId+'-edit').show();
    $('#'+itemId+'-cancel').hide();
}

//enable edit, unlock the fields and the save button, hide the edit button and show the cancel button
function enableEditing(itemId){
    $('#'+itemId+'-desc').removeAttr('disabled');
    $('#'+itemId+'-info').removeAttr('disabled');
    $('#'+itemId+'-type').removeAttr('disabled');
    $('#'+itemId+'-category').removeAttr('disabled');
    $('#'+itemId+'-price').removeAttr('disabled');
    $('#'+itemId+'-stock').removeAttr('disabled');
    $('#'+itemId+'-save').removeAttr('disabled');
    $('#'+itemId+'-edit').hide();
    $('#'+itemId+'-cancel').show();
}

//show the add new user modal 
function showUserAdditionScreen(){
    $('#userAdditionScreen').modal('show');
}

//enable user editing on admin, same logic as above enable edit
function enableEditingUser(userId){
    $('#'+userId+'-email').removeAttr('disabled');
    $('#'+userId+'-save').removeAttr('disabled');
    $('#'+userId+'-edit').hide();
    $('#'+userId+'-cancel').show();
}

//disable user editing on admin, same logic as above disable edit
function disableEditingUser(userId){
    $('#'+userId+'-email').attr('disabled','disabled');
    $('#'+userId+'-save').attr('disabled','disabled');
    $('#'+userId+'-edit').show();
    $('#'+userId+'-cancel').hide();
}

//show the add new item modal
function showItemAdditionScreen(){
    $('#itemAdditionScreen').modal('show');
}

//like - remove like from item
function likeItem(ItemId, add){
    $.post("/WineShop/rate_item.php", {ItemId: ItemId, like: add},
        function (data) {
            $('body').append(data); //append the return message
            $('.alert').delay(1000).fadeOut('slow');
            window.location.reload();
        });
}