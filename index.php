<?php
// Include router class
include('Route.php');
define("BASE_URL", "http://" . $_SERVER['HTTP_HOST'] . "/adminRSHJ");

// Add base route (startpage)
Route::add('/',function(){
    header("Location: ./controllers/C_dashboard.php");
});

Route::add('/admin',function(){
    // header("Location: ./controllers/C_dashboard.php");
},'get');

// http://103.231.200.60/
Route::run('/');