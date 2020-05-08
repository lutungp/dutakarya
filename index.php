<?php
// Include router class
include('Route.php');
define('BASE_URL', 'http://10.1.5.3/telemedicineRSHJ');

// Add base route (startpage)
Route::add('/',function(){
    header("Location: ./controllers/C_booking.php");
});


Route::run('/');