<?php

use App\Category;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Traits\ManageForeignKeys;

class DatabaseSeeder extends Seeder
{

    use ManageForeignKeys;

    const USERS_QUANTITY_SEEDER = 100;
    const CATEGORIES_QUANTITY_SEEDER = 10;

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

        // per evitare linvio di mail quando viene rieseguito seeder
        User::flushEventListeners();
        Category::flushEventListeners();

        factory(Category::class, self::CATEGORIES_QUANTITY_SEEDER)->create();
        factory(User::class, self::USERS_QUANTITY_SEEDER)->create()->each(
            function ( $user ) {
                $categories = Category::all()->random(mt_rand(1,4))->pluck('id');
                $user->categories()->attach($categories);
            }
        );

    }
}
