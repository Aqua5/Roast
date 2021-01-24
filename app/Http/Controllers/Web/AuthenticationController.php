<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    //
    public function getSocialRedirect($account){
        try {
            //code...
            return Socialite::with($account)->redirect();
        } catch (\InvalidArgumentException $e) {
            //throw $th;
            return redirect('/login');
        }
    }

    public function getSocialCallback($account){

        //从第三方 OAuth 回调中获取用户信息
        $socialUser = Socialite::with($account)->user();
        //在本地判断一下
        $user = User::where('provide_id','=',$socialUser->id )
                ->where('provider','=',$account)
                ->first();

        if ($user == null ) {
            # code...
            $newUser = new User();
            
            $newUser->name        = $socialUser->getName();
            $newUser->email       = $socialUser->getEmail() == '' ? '' : $socialUser->getEmail();
            $newUser->avatar      = $socialUser->getAvatar();
            $newUser->password    = '';
            $newUser->provider    = $account;
            $newUser->provider_id = $socialUser->getId();

            $newUser->save();
            $user = $newUser;
    }

    // 手动登录该用户
    Auth::login( $user );

    // 登录成功后将用户重定向到首页
    return redirect('/');
    }

}
