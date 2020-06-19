<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class UserController extends ApiController
{
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
           'username' => 'required|alpha_dash|unique:users|max:30',
           'email' => 'required|email:dns|unique:users',
           'password' => 'required|min:6|confirmed',
//           'category' => 'array'
       ]);

//       $data = $request->all();
       $user = new User;
       $user->username = $request->username;
       $user->email = $request->email;
       $user->password = bcrypt($request->getPassword());
       $user->verified = User::UNVERIFIED_USER;
       $user->verification_token = User::generateVerificationCode();
       $user->admin = User::REGULAR_USER;
       $user->save();

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
        return $this->showOne($user);
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
            'username' => "alpha_dash|unique:users|max:30",
            'email' => "email:dns|unique:users,email,$user->id",
//            'admin' => "in:".User::ADMIN_USER.",".User::REGULAR_USER,
            'password' => 'min:6|confirmed',
            'categories' => "array|max:10",
            'categories.*' => "integer|categories,id"
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

        if ( $request->has('categories') ) {
            $user->categories()->sync($request->categories);
        }

        if ( !$user->isDirty() ){
            return $this->errorResponse('You need to specify a different value to update', 422);
        }

        $user->save();

        return $this->showOne($user);


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
