<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\FactoryBuilder;

class UserTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    public function testGetUsers()
    {
        $response = $this->json('get', '/users' );
        $response->assertSee("username");
        $response->assertJsonCount(200, 'data');
    }

    public function testGetOneUser()
    {
        $response = $this->json( 'get', '/users/10');
        
        $response->assertJsonCount( 1 );
        $response->assertJsonStructure([ 'data' => [
            'id','username','email','verified','admin','created_at','updated_at','deleted_at'
        ]]);
    }



    public function testCreateOneUser()
    {
        $response = $this->json( 'post', '/users', [
            'username' => 'djeembo1',
            'email' => 'djeembo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
//        $response->dump();
        $response->assertStatus(201);
        $response->assertJsonStructure([ 'data' => [
            'id','username','email','verified','admin','created_at','updated_at'
        ]]);
        $response->assertJson([ 'data' => [
            'username' => 'djeembo1',
            'id' => 201,
            'email' => 'djeembo@gmail.com',
            'verified' => '0',
            'admin' => "false"
        ]]);

        $response = $this->json( 'get', '/users/201');
        $response->assertStatus(200);
        $response->assertJson([ 'data' => [
            'username' => 'djeembo1',
            'id' => 201,
            'email' => 'djeembo@gmail.com',
            'verified' => '0',
            'admin' => "false"
        ]]);


    }



//    public function testUpdateOneUser() {
//
//        $id = rand(1,200);
//
//        $firstResponse = $this->json( 'get', "/users/$id" );
//        $firstResponse->dump();
//        $firstResponse->assertStatus(200);
//
//
//
//        $newResponse = $this->json( 'patch', "/users/$id", [
//            'username' => 'nuovoDjeembo',
//            'email' => 'nuovoDjeembo@ramarro.com',
//            'password' => 'nuovaPassword',
//            'password_confirmation' => 'nuovaPassword',
//        ]);
//
//////        $response->assertJsonStructure([ 'data' => [
//////            'username', 'password' , 'email'
//////        ]]);
////
//        $newResponse->assertStatus(200);
//        $newResponse->dump();
//
////        $response->assertJson(['data' => [
////            'username' => 'nuovoDjeembo',
////            'email' => 'nuovoDjeembo@ramarro.com',
////            'password' => 'nuovaPassword'
////        ]]);
//
//
//    }

//////////////
///
// ERRORS && exceptions

    public function testErrorGetUnexistingUser()
    {
        $response = $this->json( 'get', '/users/gnappo');
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error', 'code'
        ]);
    }

    public function testErrorCreationEmptyUser()
    {
        $response = $this->json('post', '/users', []);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message', 'errors'
        ]);
    }

    public function testNotFound()
    {
        $response = $this->json('get', '/gnappo');
        $response->assertStatus(404);
        $response->assertJsonStructure([
            'error', 'code',
        ]);
    }

    public function testMethodNotAllowed()
    {
        $response = $this->json('post', '/users/10' );
        $response->assertStatus(405);
        $response->assertJsonStructure([
            'error', 'code',
        ]);
    }
}
