<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FillCatalog extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [];
        for ($i = 0; $i <= 10; $i++){
            $faker = Factory::create('ru_RU');

            $categories[] = [
                'title' => $faker->company,
                'description' => $faker->realText
            ];
        }
        Category::insert($categories);

        $categoriesId = Category::query()
            ->pluck('id', 'id')
            ->toArray();


        for ($i = 0; $i <= 20; $i++){
            $faker = Factory::create('ru_RU');

            $title = $faker->name;

            $product = [
                'title' => $title,
                'price' => $faker->biasedNumberBetween,
                'vendor_code' => Str::lower($title) . '#' . $i,
                'description' => $faker->realText
            ];

            $product = new Product($product);
            $product->save();

            $product->categories()->attach(array_rand($categoriesId, 5));
        }
    }

}
