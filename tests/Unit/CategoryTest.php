<?php

namespace Tests\Unit;

use App\Category;
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
    public function testErrorArgumentsCreateCategory()
    {

        $dummyString = function ($howlong = 1){
            $tmp = "";
            for( $i = 1; $i<=$howlong; $i++ )
                $tmp .= "012 345678 9qazxswe dcvf rtgbn nhyuj mkiopl "[rand(1, 43)];

            return $tmp;
        };

        $response = $this->json('post', "/categories", [
            'name' => $dummyString(191),
            'description' => 'categoria figa'
        ]);

        $response->assertStatus(422);
        $response = $this->json('post', "/categories", [
            'name' => 'gnappo',
            'description' => $dummyString(1000)
        ]);

        $response->assertStatus(422);
        
    }

}
