<?php

use Illuminate\Database\Seeder;

use App\Models\Category;
use App\Models\Genre;

class GenresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::all();

        factory(Genre::class, 100)
            ->create()
            ->each(function (Genre $genre) use ($categories) {
                $categoriesId = $categories->random(rand(1, 5))->pluck('id')->toArray();
                $genre->categories()->attach($categoriesId);
            });
    }
}
