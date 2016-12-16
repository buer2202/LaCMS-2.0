# LaCMS 2.0
### 基于laravel的CMS系统
***
后台管理有3个模块

**1.栏目管理：**
> 无限层级分类，有4种类型：

> 1.列表；
> 2.专题;
> 3.混合；
> 4.群组

**2.文档管理**
>可上传附件

**3.附件清理**
>用户清理无效附件以及关联

***
###安装方法
```
composer update
composer dump-autoload
php artisan migrate
php artisan db:seed
```

管理员：admin 密码：admin