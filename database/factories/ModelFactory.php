<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

// 创建示例文章
$factory->define(App\Document::class, function () {
    $faker = Faker\Factory::create('zh_CN');
    return [
        'id'             => uniqid(),
        'category_id'    => 2,
        'title'          => $faker->catchPhrase,
        'content'        => $faker->text,
        'user_id_create' => 1,
        'user_id_modify' => 1,
        'time_document'  => time(),
        'image'          => 1,
        'info_1'         => $faker->catchPhrase,
        'info_2'         => $faker->catchPhrase,
        'info_3'         => $faker->catchPhrase,
        'info_4'         => $faker->catchPhrase,
        'info_5'         => $faker->catchPhrase,
        'info_6'         => $faker->catchPhrase,
    ];
});

// 创建示例栏目
$factory->define(App\Category::class, function (Faker\Generator $faker) {
    return [
        'name'           => $faker->word,
        'parent_id'      => 0,
        'type'           => 1,
        'level'          => 1,
        'status'         => 1,
        'sortord'        => 1,
        'user_id_create' => 1,
        'user_id_modify' => 1,
    ];
});

// 创建示例附件
$factory->define(App\Attachment::class, function (Faker\Generator $faker) {
    return [
        'type'           => 1,
        'refer'          => 0,
        'description'    => $faker->url,
        'md5'            => '',
        'ext'            => '',
        'user_id_create' => 1,
        'user_id_modify' => 1,
        'uri'            => '/images/temp1.jpg',
    ];
});

// 创建示例附件关联
$factory->define(App\DocumentAttachment::class, function (Faker\Generator $faker) {
    return [
        'attachment_id' => 1,
        'effective'     => 0,
    ];
});

// 清除所有缓存
cache()->flush();
