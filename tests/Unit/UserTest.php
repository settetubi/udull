<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\App;
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


    public function testUpdateOneUser() {

        $id = rand(1,200);

        $firstResponse = $this->json( 'get', "/users/$id" );
        $firstResponse->assertStatus(200);

        $pass = 'nuovaPassword';
        $newResponse = $this->json( 'patch', "/users/$id", [
            'username' => 'nuovoDjeembo',
            'email' => 'nuovoDjeembo@ramarro.com',
            'password' => $pass,
            'password_confirmation' => $pass,
        ]);

        $newResponse->assertStatus(200);

        $newget = $this->json( 'get', "/users/$id" );
//        $newget->dump();
        $newget->assertJson(['data' => [
            'username' => 'nuovoDjeembo',
            'email' => 'nuovoDjeembo@ramarro.com',
        ]]);

        $user = User::findOrFail($id);
        $this->assertTrue(password_verify($pass, $user->password));

    }

    public function testUpdateOneUserDirty() {


        $response = $this->json( 'post', '/users', [
            'username' => 'djeembodelete1',
            'email' => 'djeembo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([ 'data' => [
            'id','username','email','verified','admin','created_at','updated_at'
        ]]);
        $data = $response->decodeResponseJson();


        $newResponse = $this->json( 'patch', "/users/{$data['data']['id']}", [
            'username' => 'djeembodelete1',
            'email' => 'djeembo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $newResponse->assertStatus(422);

    }


    public function testDeleteUser() {
        $response = $this->json( 'post', '/users', [
            'username' => 'djeembodelete1',
            'email' => 'djeembo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([ 'data' => [
            'id','username','email','verified','admin','created_at','updated_at'
        ]]);

        $response->assertJson([ 'data' => [
            'username' => 'djeembodelete1',
            'id' => 201,
            'email' => 'djeembo@gmail.com',
            'verified' => '0',
            'admin' => "false"
        ]]);

        $response = $this->json( 'delete', '/users/201');
        $response->assertStatus(200);

        $response = $this->json( 'get', "/users/201" );
        $response->assertStatus(404);

    }



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
