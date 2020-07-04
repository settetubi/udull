<?php

namespace App\Http\Controllers\User;

use App\Category;
use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends ApiController
{

    const USERNAME_ARG = 'username';
    const EMAIL_ARG = 'email';
    const PASSWORD_ARG = 'password';
    const CATEGORIES_ARG = 'categories';

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse|Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {

       $this->validate($request, [
           self::USERNAME_ARG => 'required|alpha_dash|unique:users|max:30',
           self::EMAIL_ARG => 'required|email:dns|unique:users',
           self::PASSWORD_ARG => 'required|min:6|confirmed',
           self::CATEGORIES_ARG => 'array|distinct',
           self::CATEGORIES_ARG.".*" => 'exists:categories,id|integer'
       ]);

//       $data = $request->all();
       $user = new User;
       $user->username = $request->username;
       $user->email = $request->email;
       $user->password = bcrypt($request->getPassword());
       $user->verified = User::UNVERIFIED_USER;
       $user->verification_token = User::generateVerificationCode();
       $user->admin = User::REGULAR_USER;

       DB::transaction( function() use ($user, $request) {
           $user->save();
           $user->categories()->sync($request->categories);
       });

       return $this->showOne($user, 201);

    }

    /**
     * Display the specified resource.
     *
     * @param $user
     * @return JsonResponse|Response
     */
    public function show(User $user)
    {
        return $this->showOne($user->load('categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param User $user
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, User $user)
    {

        $this->validate($request, [
            self::USERNAME_ARG => "alpha_dash|unique:users|max:30",
            self::EMAIL_ARG => "email:dns|unique:users,email,$user->id",
//            'admin' => "in:".User::ADMIN_USER.",".User::REGULAR_USER,
            self::PASSWORD_ARG => 'min:6|confirmed',

        ]);

        if ( $request->has('username') ) {
            $user->username = $request->username;
        }

        // generate verification code...
        if ( $request->has('email') ) {
            $user->email = $request->email;
        }

        if ( $request->has('password') ) {
            $user->password = bcrypt($request->password);
        }

        if ( $request->has(self::CATEGORIES_ARG) ) {
            $user->categories()->sync($request->categories);
        }

        if ( !$user->isDirty() ){
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $user->save();

        return $this->showOne($user, 200);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param $user
     * @return JsonResponse
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();
        return $this->showOne($user);

    }
}
