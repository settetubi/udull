<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\User;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return $this->showAll($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function store(Request $request)
    {

       $this->validate($request, [
           'username' => 'required|alpha_dash|unique:users|max:30',
           'email' => 'required|email:dns|unique:users',
           'password' => 'required|min:6|confirmed'
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
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show($id)
    {
//        $users = User::where('id', $id)->get();
        $user = User::findOrFail($id);
        return $this->showOne($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $user)
    {

        $this->validate($request, [
            'username' => "alpha_dash|unique:users|max:30",
            'email' => "email:dns|unique:users,email,$user->id",
//            'admin' => "in:".User::ADMIN_USER.",".User::REGULAR_USER,
            'password' => 'min:6|confirmed'
        ]);

        $user = User::findOrFail($user->id);

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

        if ( !$user->isDirty() ){
            var_dump('ciccia');
        }

        $user->save();

        return $this->showOne($user);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
