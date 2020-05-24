<?php

use App\Category;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Traits\ManageForeignKeys;

class DatabaseSeeder extends Seeder
{

    use ManageForeignKeys;

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

//        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->disable();

        User::truncate();
        Category::truncate();
        DB::table('category_user')->truncate();

        $usersQuantity = 200;
        $categoriesQuantity = 10;

        factory(Category::class, $categoriesQuantity)->create();
        factory(User::class, $usersQuantity)->create()->each(
            function ( $user ) {
                $categories = Category::all()->random(mt_rand(1,4))->pluck('id');
                $user->categories()->attach($categories);
            }
        );

    }
}
