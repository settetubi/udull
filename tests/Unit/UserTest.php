<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Swift_Events_EventListener;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\FactoryBuilder;

class UserTest extends TestCase
{
    use DatabaseMigrations, RefreshDatabase;

    protected $emails = [];

    public function setUp(): void
    {
        parent::setUp();


//        $swiftMailer = app('mailer')->getSwiftMailer();
//        $swiftTransport = $swiftMailer->getTransport();
//
//        $swiftTransport->registerPlugin(new TestingMailEventListener($this));
    }


    public function testGetUserList()
    {
        $response = $this->json('get', '/users?per_page=30' );
        $response->assertSee("name");
        $response->assertJsonCount(30, 'data');

    }


    public function testGetOneUser()
    {
        $response = $this->json( 'get', '/users/10');
        $response->assertJsonCount( 1 );
        $response->assertJsonStructure([ 'data' => [
            'identifier','name','email','verified','admin','created_at','updated_at','deleted_at','categories' => [[ 'identifier', 'name', 'description']]
        ]]);
    }


    public function testCreateOneUser()
    {
        $response = $this->json( 'post', '/users', [
            'name' => 'djeembodjango',
            'email' => 'djeembo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([ 'data' => [
            'identifier','name','email','verified','admin','created_at','updated_at'
        ]]);

        $response = $this->json( 'get', '/users/'.(\DatabaseSeeder::USERS_QUANTITY_SEEDER+1));
        $response->assertStatus(200);
        $response->assertJson([ 'data' => [
            'name' => 'djeembo1',
            'identifier' => \DatabaseSeeder::USERS_QUANTITY_SEEDER+1,
            'email' => 'djeembo@gmail.com',
            'verified' => '0',
            'admin' => "false"
        ]]);
    }


    public function testCreateOneUserWithCategories(){

        $cat = [];
        for( $i=0, $until=rand(2,5) ; $i<$until ; $v=rand(1,10), $cat[$v]=$v, $i++ ) ;

        $until = count( $cat );

        $response = $this->json( 'post', '/users', [
            'name' => 'djeembox',
            'email' => 'djeembox@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'categories' => $cat
        ]);
        $response->assertStatus(201 );

        $id = $response->json('data')['identifier'];
        $response = $this->json( 'get', "/users/$id");

        $response->assertJsonCount( 1 );
        $response->assertJsonStructure([ 'data' => [
            'identifier','name','email','verified','admin','created_at','updated_at','deleted_at','categories' => [[ 'identifier', 'name', 'description']]
        ]]);

        $response->assertJsonCount($until, 'data.categories');

        $this->assertEmpty(
            array_diff_key($cat, array_column(data_get($response->json(), 'data.categories'), 'identifier', 'identifier'))
        );

    }


    public function testUpdateOneUser() {

        $id = rand(1,\DatabaseSeeder::USERS_QUANTITY_SEEDER);

        $firstResponse = $this->json( 'get', "/users/$id" );
        $firstResponse->assertStatus(200);

        $pass = 'nuovaPassword';
        $newResponse = $this->json( 'patch', "/users/$id", [
            'name' => 'nuovoDjeembo',
            'email' => 'nuovoDjeembo@ramarro.com',
            'password' => $pass,
            'password_confirmation' => $pass,
            'categories' => [8,9]
        ]);

        $newResponse->assertStatus(200);

        $newResponse = $this->json( 'get', "/users/$id" );

        $newResponse->assertJson(['data' => [
            'name' => 'nuovoDjeembo',
            'email' => 'nuovoDjeembo@ramarro.com',

        ]]);
        $user = User::findOrFail($id);


        $this->assertTrue(password_verify($pass, $user->password));

        $cats = [];
        $newResponse->assertJsonCount(2, 'data.categories');

        foreach($user->categories as $cat) {
            $cats[$cat->id] = $cat->id;
        }

        $this->assertTrue( key_exists(8, $cats));
        $this->assertTrue( key_exists(9, $cats));

    }


    public function testUpdateOneUserDirty() {


        $response = $this->json( 'post', '/users', [
            'name' => 'djeembodelete1',
            'email' => 'djeembo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([ 'data' => [
            'identifier','name','email','verified','admin','created_at','updated_at'
        ]]);
        $data = $response->decodeResponseJson();
        $newResponse = $this->json( 'patch', "/users/{$data['data']['identifier']}", [
            'name' => 'djeembodelete1',
            'email' => 'djeembo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        $newResponse->assertStatus(422);

    }


    public function testDeleteUser() {
        $response = $this->json( 'post', '/users', [
            'name' => 'djeembodelete1',
            'email' => 'djeembo@gmail.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([ 'data' => [
            'identifier','name','email','verified','admin','created_at','updated_at'
        ]]);

        $response->assertJson([ 'data' => [
            'name' => 'djeembodelete1',
            'identifier' => (\DatabaseSeeder::USERS_QUANTITY_SEEDER+1),
            'email' => 'djeembo@gmail.com',
            'verified' => '0',
            'admin' => "false"
        ]]);

        $response = $this->json( 'delete', '/users/'.(\DatabaseSeeder::USERS_QUANTITY_SEEDER+1));
        $response->assertStatus(200);

        $response = $this->json( 'get', "/users/".(\DatabaseSeeder::USERS_QUANTITY_SEEDER+1) );
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



    /////////////////////////////////
    /// mail testing management
    ///
    /// testMail test to try
//    public function testMail() {
//
////        $response = $this->json( 'get', '/prova');
//
//
//        Mail::raw('ciaone', function($message) {
//            $message->to('gnappo@gnappo.com');
//            $message->from('laltrognappo@gnappo.com');
//        });
//
//        $this->seeEmailWasSent();
//
//        $response = $this->json( 'post', '/users', [
//            'name' => 'djeembo1',
//            'email' => 'djeembo@gmail.com',
//            'password' => 'password',
//            'password_confirmation' => 'password'
//        ]);
//
//        $response->assertStatus(201);
//        $response->assertJsonStructure([ 'data' => [
//            'identifier','name','email','verified','admin','created_at','updated_at'
//        ]]);

//        $this->assertNotEmpty($this->emails, 'No emails have been sent');
//        $mail = $this->popEmail();
//        dd( $mail );
//        $this->assertEquals('djeembo@gmail.com', $mail->getTo());


//    }

//    protected function seeEmailWasSent(){
//        $this->assertNotEmpty($this->emails, 'No emails have been sent');
//    }
//
//    public function addEmail(\Swift_Message $emails){
//        $this->emails[] = $emails;
//    }
//
//    public function popEmail(){
//        return array_pop($this->emails);
//    }
//
//    public function emptyEmails() {
//        $this->emails = [];
//    }


}





//class TestingMailEventListener implements Swift_Events_EventListener
//{
//
//    protected $test;
//
//    public function __construct($test){
//        $this->test = $test;
//    }
//
//
//    public function beforeSendPerformed($event){
//        $this->test->addEmail($event->getMessage());
//    }
//}
