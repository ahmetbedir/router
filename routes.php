<?php

use BDR\Route;

Route::get('/', function(){
	include('form.php');
});

Route::post('/', function(){
	echo "<pre>";
	print_r($_REQUEST);
});

Route::get('/user/{id}/edit', function ($id) {
	echo "Düzenleme sayfası: ID: $id";
});

Route::get('/user/profile/{id}', function ($id) {
 	echo "Üye profil sayfası: ID =" . $id;
});

// Rotaların hiç biri eşleşmez ise çalışır
Route::fallback(function(){
	echo "Sayfa bulunamadı!";
});