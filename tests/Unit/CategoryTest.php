<?php

namespace Tests\Unit;

use App\Category;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\User\UserController;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{

    use DatabaseMigrations, RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */

    // get categories
    public function testGetCategories()
    {
        $response = $this->json('get', "/categories" );
        $response->assertStatus(200);
    }

    // get category
    public function testGetCategory()
    {
        $id = rand(1, \DatabaseSeeder::CATEGORIES_QUANTITY_SEEDER);
        $response = $this->json('get', "/categories/$id" );
        $response->assertStatus(200);
        $response->assertJsonStructure([ 'data' => [
            'name', 'description', 'id'
        ]]);
    }
    public function testErrorNotFoundGetCategory()
    {
        $id = \DatabaseSeeder::CATEGORIES_QUANTITY_SEEDER + 10;
        $response = $this->json('get', "/categories/$id" );
        $response->assertStatus(404);

        $id = -20;
        $response = $this->json('get', "/categories/$id" );
        $response->assertStatus(404);

        $id = "pippo";
        $response = $this->json('get', "/categories/$id" );
        $response->assertStatus(404);

        $id = "\"echo 'ciao';";
        $response = $this->json('get', "/categories/$id" );
        $response->assertStatus(404);
    }

    // post
    public function testCreateCategory()
    {
        $response = $this->json('post', "/categories", [
            'name' => 'Gnappocat',
            'description' => 'categoria figa'
        ]);
        $response->assertStatus(201);
        $response->assertJsonStructure([ 'data' => [
            'name', 'id', 'description'
        ]]);
        $response->assertJson([ 'data' => [
            'name' => 'Gnappocat',
            'description' => 'categoria figa'
        ]]);
    }
    public function testCreateWithVeryLongFields()
    {
        $dummyString = function ($howlong = 1){
            $tmp = "";
            for( $i = 1; $i<=$howlong; $i++ )
                $tmp .= "012 345678 9qazxswe dcvf rtgbn nhyuj mkiopl "[rand(1, 43)];

            return $tmp;
        };


        $response = $this->json('post', "/categories", [
            'name' => $dummyString( Category::MAX_LEN_NAME ),
            'description' => $dummyString( Category::MAX_LEN_DESCRIPTION )
        ]);

        $response->assertStatus(201);

    }
    public function testErrorArgumentsCreateCategory()
    {

        $dummyString = function ($howlong = 1){
            $tmp = "";
            for( $i = 1; $i<=$howlong; $i++ )
                $tmp .= "012 345678 9qazxswe dcvf rtgbn nhyuj mkiopl "[rand(1, 43)];

            return $tmp;
        };

        $response = $this->json('post', "/categories", [
            'name' => $dummyString(Category::MAX_LEN_NAME+10),
            'description' => 'categoria figa'
        ]);
        $response->assertStatus(422);
        $response = $this->json('post', "/categories", [
            'name' => 'gnappo',
            'description' => $dummyString(Category::MAX_LEN_DESCRIPTION+10)
        ]);

        $response->assertStatus(422);

    }

    //patch
    public function testUpdateCategory()
    {
        $id = rand(1, \DatabaseSeeder::CATEGORIES_QUANTITY_SEEDER);
        $response = $this->json('PATCH', "categories/$id", [
            CategoryController::NAME_ARG => 'pippopluto',

        ]);
        $response->assertStatus(200);
        $newget = $this->json( 'get', "/categories/$id" );
        $newget->assertJson(['data' => [
            CategoryController::NAME_ARG => 'pippopluto',
        ]]);

        $response = $this->json('PATCH', "categories/$id", [
            CategoryController::DESCRIPTION_ARG => 'gnappolone',

        ]);
        $response->assertStatus(200);
        $newget = $this->json( 'get', "/categories/$id" );
        $newget->assertJson(['data' => [
            CategoryController::DESCRIPTION_ARG => 'gnappolone',
        ]]);

        $response = $this->json('PATCH', "categories/$id", [
            CategoryController::NAME_ARG => 'pippopluto',
            CategoryController::DESCRIPTION_ARG => 'gnappolone',
        ]);
        $response->assertStatus(200);
        $newget = $this->json( 'get', "/categories/$id" );
        $newget->assertJson(['data' => [
            CategoryController::NAME_ARG => 'pippopluto',
            CategoryController::DESCRIPTION_ARG => 'gnappolone',
        ]]);


    }

    //delete
    public function testDeleteCategory ()
    {
        $id = rand(1, \DatabaseSeeder::CATEGORIES_QUANTITY_SEEDER);

        $response = $this->json('DELETE', "categories/$id");
        $response->assertStatus(200);
    }

}
