# PHP Rota Sistemi

MVC yapınız içerisinde kullanbileceğiniz basit rota sistemi. Yazım kolaylığı ve sadelikten dolayı Laravel'in rota yapısına benzer bir kullanım sunar.

Rota yapınız içerisinde **{id}** gibi URL'den gelen isteklerdeki parametreleri alabiliriniz

```
/user/{id}/edit 
```

# Kullanılabilir methodlar
### **GET, POST, PUT, PATCH, DELETE**
**PUT, PATHC, DELETE** methodlarını kullanabilmeniz için HTML 
Form etiketinin içinde adı **_method** olan ve değeri methodunuz bir input kullanmanız gerekmektedir.
## Örnek Kullanım
```html
<form action="/user" method="POST">
    <input type="hidden" name="_method" value="PUT"/>
</form>
```

# Rota Kullanımı
```php
Route::get('/kullanici/{id}/duzenle', function($id){
    echo "GET: Kullanıcı ID:". $id;
});
Route::post('/kullanici/{id}/duzenle', function($id){
    echo "POST: Kullanıcı ID:". $id;
});
```

### Aynı rota birden fazla istek methoduyla çalışabilir. Buarada **match** methoduna dizi olarak girdiğiniz istek methodları işlem sırasında izin verilir aksi takdirde rota eşleşmez.
```php
Route::match(['get', 'post'], '/kullanici', function(){
    echo "";
});
```

### Tüm istek methodlarında çalışan bir rota için **any** sınıf methodunu kullanabiliriz.
```php
Route::any('/makale', function(){
    echo "İstek Methodu: " . $_SERVER['REQUEST_METHOD'];
});
```

# Methodlar
| Method | Kullanım                       | 
| ------ | ------------------------------ |
| GET    | Route::get($route, $action)    |
| POST   | Route::post($route, $action)   |
| PUT    | Route::put($route, $action)    |
| PATCH  | Route::patch($route, $action)  |
| DELETE | Route::delete($route, $action) |

# Yapılacaklar
- Opsiyonel parametreler

