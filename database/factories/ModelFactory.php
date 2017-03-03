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

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Group::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->unique()->name,
        'type' => $faker->randomElement(['academic', 'committee']),
        'privacy' => $faker->randomElement(['open', 'closed']),
        'interaction' => $faker->randomElement(['informative', 'interactive']),
    ];
});

$factory->define(\Laravel\Passport\Client::class, function (Faker\Generator $faker) {
    return [
        'name' => 'test-client',
        'secret' => $faker->text(100),
        'redirect' => '',
        'personal_access_client' => false,
        'password_client' => true,
        'revoked' => false
    ];
});

$factory->define(App\Asset::class, function (Faker\Generator $faker) {
    return [
        'description' => $faker->text(100),
        'type' => $faker->fileExtension,
        'size' => $faker->randomNumber(5),
        'filepath' => '/tmp/' . $faker->text(10) . $faker->fileExtension
    ];
});