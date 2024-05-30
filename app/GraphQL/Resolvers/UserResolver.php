<?php

namespace App\GraphQL\Resolvers;

use App\Exceptions\GraphQLException;
use App\Models\User;
use App\Models\VerifyCode;
use Hash;

class UserResolver
{

    public static function LoginByPassword($phone): array
    {
        $user = User::query()->where(
            'phone', $phone,
        );
        if(Hash::check($phone,$user->phone)){
            return [
                'user'=>$user,
                'token'=>$user->createToken($phone)->plainTextToken
            ];
        }
        throw  new GraphQLException('密码错误，重新再试试');
    }

    public static function  LoginByPhone($phone,$code): array
    {
        if(\App::isLocal()){
            $user = User::query()->where('phone', $phone)->first();
            if(!$user){
                $newUser = User::create([
                    'name'     => "用户" . $phone,
                    'password' => "null",
                    'phone'    => $phone
                ]);
                return [
                    'token'=>$newUser->createToken($phone)->plainTextToken,
                    'user'=>$newUser
                ];
            }
            return [
                'token'=>$user->createToken($phone)->plainTextToken,
                'user'=>$user
            ];
        }

        $code1 = VerifyCode::query()->where('phone', $phone)->first();
        if ($code!==$code1){
          throw new GraphQLException('验证码错误');
        }
        $user = User::query()->where('phone', $phone)->first();
        if(!$user){
            $newUser = User::create([
                'name'     => "用户" . $phone,
                'password' => "null",
                'phone'    => $phone
            ]);
            return [
                'token'=>$newUser->createToken($phone)->plainTextToken,
                'user'=>$newUser
            ];
        }
        return [
            'token'=>$user->createToken($phone)->plainTextToken,
            'user'=>$user
        ];
    }

}
