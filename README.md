laravel-admin extension
======
```
php artisan vendor:publish --provider="Encore\OrgRbac\OrgRbacServiceProvider"
```
After run command you can find config file in `config/org.php`, in this file you can change the install db connection or table names.

At last run following command to finish init.
```
php artisan orgRbac:init
```
After run command you can load basic database data.

Open `http://localhost/admin/` in browser,use username `admin` and password `admin` to login.


