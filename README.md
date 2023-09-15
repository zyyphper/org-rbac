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

```
Encore\OrgRbac\Layout\Content
```
During the development process, the Content class provided by the current component needs to be used
```
config/admin/auth.providers.admin.model >> \Encore\OrgRbac\Models\User::class
```
In the configuration file of admin, it is necessary to change the object provision of the user model to the user model provided by the current component

Some bugs in laravel-admin can be directly solved by using the current component's secondary encapsulation class

<img width="1120" alt="屏幕截图 2023-09-15 110720" src="https://github.com/zyyphper/org-rbac/assets/140879077/6ea22ab4-4934-4bf8-992d-a06ab6420121">

