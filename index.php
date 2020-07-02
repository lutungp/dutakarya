<?php
// Include router class
include('Route.php');
define("BASE_URL", "http://" . $_SERVER['HTTP_HOST'] . "/dutakarya");

// Add base route (startpage)
Route::add('/',function(){
    header("Location: controllers/C_dashboard");
});

Route::run('/');