<?php

namespace App\Http\Controllers;

use Validator;
use App\User;
use App\Token;
use Carbon\Carbon;
use Hash;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|max:190',
            'age'      => 'required|numeric|max:150',
            'username' => 'required|max:190|unique:users',
            'email'    => 'required|unique:users|email',
            'password' => 'required|max:190|min:6',
        ]);

        if ($validator->fails()) {
            $errorMsg = $validator->errors()->first();
            return ['type'  => 'error',
                    'value' => $errorMsg];
        }

        $name     = $request->input('name');
        $age      = $request->input('age');
        $username = $request->input('username');
        $email    = $request->input('email');
        $password = $request->input('password');

        $success_token = str_random(100);
        $user_id       = str_random(100);

        while(User::where('id', $user_id)->count() > 0)
        {
            $user_id = str_random(100);
        }

        while(Token::where('id', $success_token)->count() > 0) 
        {
            $success_token = str_random(100);
        }

        $user = new User(['id'          => $user_id,
                          'name'        => $name,
                          'age'         => $age,
                          'username'    => $username,
                          'email'       => $email,
                          'password'    => Hash::make($password)]);
        $user->save();

        $token = new Token(['id'            => $success_token,
                            'expires_on'    => Carbon::now()->toDateTimeString()]);
        
        $token = $user->token()->save($token);

        return ['type'  => 'success_token',
                'value' => $success_token];
        
        

    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMsg = $validator->errors()->first();
            return ['type'  => 'error',
                    'field' => null,
                    'value' => $errorMsg];
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $dbUser = User::where('email', $email)->first();

        if($dbUser != null) {
            if(Hash::check($password, $dbUser->password)){
                $success_token = str_random(100);
                $token = $dbUser->token;
                $token->id = $success_token;
                $token->expires_on = Carbon::now()->toDateTimeString();
                $token->save();
                return ['type'  => 'success_token',
                        'field' => null,
                        'value' => $success_token];
            }
            else 
                return ['type'  => 'error',
                        'field' => 'password',
                        'value' => 'La contraseña ingresada es incorrecta.'];
        }
        else 
            return ['type'  => 'error',
                    'field' => 'email',
                    'value' => 'El email es incorrecto.'];
        
    }
}
