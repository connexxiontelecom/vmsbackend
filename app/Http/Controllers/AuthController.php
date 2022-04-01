<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Auth;
use Illuminate\Support\Facades\Hash;
use Exception;
use function PHPUnit\Framework\isEmpty;

class AuthController extends Controller
{

    private Auth\JwtAuthServices $jwtService ;

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'password' => 'required',
            'phone' => 'required',
            'username' => 'required|string',
        ]);

        $exists = User::where("username", $request->input('username'))->first();

        if ($exists != null) {
            return response()->json(['message' => 'email is taken already','code'=>'200', 'data'=>[] ], 200);
        } else {
            //return response()->json(['message' => 'email is not taken already'], 409);
            $user = new User;
            $user->name = $request->input('name');
            $user->username = $request->input('username');
            $user->phone = $request->input('phone');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);
            $user->uuid = substr(sha1(time()), 28, 40);
            $user->save();
            return response()->json(['message' => 'Registration Successfull','code'=>'200', 'data'=>[] ], 200);
        }

    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $response = $this->attemptLogin($credentials["username"], $credentials["password"]);
        if ($response!=false)
        {
            return response()->json(['message' => 'Login Successfull', "data"=>$response], 200);
        }
        else{
            return response()->json(['message' => 'Login Failed', 'code'=>200], 200);
        }
    }

    public function attemptLogin($username, $password)
    {
        $user = User::where('username', $username)->first();
        if (Hash::check($password, $user->password)) {
            $this->jwtService = new Auth\JwtAuthServices();
           $token =  $this->jwtService->init($user->uuid, $username);
           return ["user"=>$user, "token"=>$token];
        }
        else{
            return false;
        }
    }




}
