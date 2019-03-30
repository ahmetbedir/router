<?php

use BDR\Route;

/*Route::get('/home', function ($args) {
//
});*/

// Route::get('/user/{id}/edit/{name}', function ($id = null, $name = null) {
// 	echo "Düzenleme sayfası: ID = {$id}, name = $name";
// });

// Route::get('/user/profile/{id}', function ($id) {
// 	echo "Üye profil sayfası: ID =" . $id;
// });


Route::get('/user/profile/{id}/{deneme}', function ($id) {
	echo "Üye profil sayfası2: ID =" . $id;
});