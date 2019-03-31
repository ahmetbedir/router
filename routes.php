<?php

use BDR\Route;

Route::get('/', function(){
	return include('form.php');
});

Route::match(['get', 'post'] ,'/', function(){
	echo "<pre>";
	print_r($_REQUEST);
});

Route::get('/user/{id}/edit', function ($id) {
	echo "Düzenleme sayfası: ID: $id";
});

Route::get('/user/profile/{id}', function ($id) {
 	echo "Üye profil sayfası: ID =" . $id;
});