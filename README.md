#thickas/crud-generator

为z-song/laravel-admin编写的CRUD生成器，可以自动生成迁移、模型、控制器、并添加路由。

#安装z-song/laravel-admin
   https://github.com/z-song/laravel-admin/blob/master/docs/zh/README.md

#安装thickas/crud-generator
终端下运行 <br>
composer require thickas/crud-generator "v1.0" <br>

编辑config/app.php,在providers数组中添加 <br>
 Thickas\CrudGenerator\CrudGeneratorServiceProvider::class,

#使用例子
#一次生成所有，即迁移、模型、控制器、并添加路由

php artisan crud:generate Post --fields=title#string;content#text;category#select --controller-namespace=Home  --model-namespace=Models<br>
php artisan migrate<br><br>

在浏览器中直接访问：yourwebsit/admin/home/posts,即可<br><br>

其中model-name可选，默认为：App\Post<br>
controller-namespace可选，默认为：App\Admin<br><br>

#生成migration
php artisan crud:migration Post --fields=title#string;content#text;category#select

#生成Model
php artisan crud:model Post --fields=title#string;content#text;category#select --model-namespace=Models<br><br>

model-namespace 可选，默认为：App\ <br><br>

也可在模型名中指定model-namespace，例如：<br>
php artisan crud:model Models\Post --fields=title#string;content#text;category#select<br>

#生成Controller
php artisan crud:generate PostController --fields=title#string;content#text;category#select --controller-namespace=Home  --model-name=App\Models\Post<br>

model-name可选，默认为：App\Post，其中Post为控制器名称的前半部分<br>
controller-namespace可选，默认为：App\Admin<br><br>

与模型类似，也可控制器名称中指定controller-namespace，例如<br>
php artisan crud:generate Home\PostController --fields=title#string;content#text;category#select --model-name=App\Models\Post<br>
#注意
如果分步生成，需要自行添加路由，并运行composer dump-autoload。
