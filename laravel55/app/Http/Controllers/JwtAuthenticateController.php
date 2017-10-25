<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Validator;
use Log;
use GuzzleHttp\Client;

/**
 * @resource JWT and Access Control
 */
class JwtAuthenticateController extends Controller
{
    public function index()
    {
        return response()->json(['user'=>Auth::user()]);
    }

    /**
     * Login
     */

    public function authenticate(Request $request)
    {
      if($request->has('accessToken')){
        $client = new Client();
        $res = $client->request('GET', 'https://graph.facebook.com/me?fields=id,name,email&access_token='.$request->accessToken);
        $data = json_decode($res->getBody());
        $user = User::where('facebook_id',strtolower($data->id))->where('email',$data->email)->get()->first();
        if(!$user) {
          $user = User::create([
            'name' => $data->name,
            'facebook_id' => $data->id,
            'email' => $data->email,
            'access_token' => $request->accessToken
          ]);
        }

        $token = JWTAuth::fromUser($user);
      } else {
        $credentials = $request->only('email', 'password');
      

        try {
           if(Auth::once($credentials)){
                 $user = User::where('email',strtolower($credentials['email']))->get()->first();
                 $claim = [
                     'name' => $user->name,
                     'picture' => $user->picture,
                 ];
                 
                 $token = JWTAuth::fromUser($user,$claim);
             } else {
                 return response()->json(['error' => 'invalid_credentials'], 203);
              }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 203);
        }
      }

        // if no errors are encountered we can return a JWT
        return response()->json(compact('token'));
    }

    /**
     * Register
     */

    
    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'email' => 'required|string|email|max:60|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

}