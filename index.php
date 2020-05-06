<?php
// Include router class
include('Route.php');
define('BASE_URL', 'http://localhost/telemedicine');

// Add base route (startpage)
Route::add('/',function(){
    header("Location: ./controllers/C_booking.php");
});


Route::run('/');