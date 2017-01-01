#thickas/crud-generator

为z-song/laravel-admin编写的CRUD生成器，可以自动生成迁移、模型、控制器、并添加路由。

#安装z-song/laravel-admin
   https://github.com/z-song/laravel-admin/blob/master/docs/zh/README.md

#安装thickas/crud-generator
1、终端下运行 <br>
composer require thickas/crud-generator

2、编辑config/app.php,在providers数组中添加 <br>
 Thickas\CrudGenerator\CrudGeneratorServiceProvider::class,

#使用例子
#1、一次生成所有：
php artisan crud:generate Post --fields=title#string#req;content#text;category#select --controller-namespace=Home  --model-namespace=Models<br>
之后运行php artisan migrate<br>
在浏览器中直接访问：yourwebsit/admin/home/posts,即可<br>
其中model-name可选，不提供则直接使用App\Post<br>
controller-namespace可选，不提供，则直接为App\Admin<br>

#2、生成migration
php artisan crud:migration Post --fields=title#string#req;content#text;category#select

#3、生成Model
php artisan crud:model Post --fields=title#string#req;content#text;category#select --model-namespace=Models<br>
model-namespace 可选，不提供则直接在App\ <br>
或者：<br>
php artisan crud:model Models\Post --fields=title#string#req;content#text;category#select<br>
#4、生成Controller
php artisan crud:generate PostController --fields=title#string#req;content#text;category#select --controller-namespace=Home  --model-name=App\Models\Post<br>
model-name可选，不提供则直接使用App\Post<br>
controller-namespace可选，不提供，则直接为App\Admin<br>
也可直接在名称中指定controller-namespace，比如<br>
php artisan crud:generate Home\PostController --fields=title#string#req;content#text;category#select --model-name=App\Models\Post
#注意，如果分步生成，需要自行添加路由，并运行composer dump-autoload。
