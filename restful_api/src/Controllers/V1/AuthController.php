<?php
namespace App\Controllers\V1;

use App\Models\BlacklistToken;
use App\Models\RefreshToken;
use App\Models\User;
use App\Transformers\UserTransformer;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Requtize\QueryBuilder\Exception\Exception;
use System\Core\Auth;

class AuthController
{
    public function login()
    {
        $email = input('email');
        $password = input('password');
        if (!$email || !$password) {
            return errorResponse(status: 400, message: "Vui lòng nhập email và mật khẩu");
        }

        $userModel = new User;
        $user = $userModel->getOne($email, 'email');
        if (!$user) {
            return errorResponse(status: 404, message: "Tài khoản không tồn tại");
        }

        $passwordHash = $user->password;

        if (!password_verify($password, $passwordHash)) {
            return errorResponse(status: 401, message: "Mật khẩu không chính xác");
        }

        //Tạo token
        $payload = [
            'sub' => $user->id,
            'exp' => time() + env('JWT_EXPIRE'),
            'iat' => time(),
        ];
        $accessToken = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
        $refreshToken = JWT::encode([
            'exp' => time() + env('JWT_REFRESH_EXPIRE'),
            'sub' => $user->id,
            'iat' => time(),
        ], env('JWT_REFRESH_SECRET'), 'HS256');
        (new RefreshToken())->create(
            [
                'user_id' => $user->id,
                'refresh_token' => $refreshToken,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
        return successResponse(data: [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);
    }

    public function profile()
    {
        return successResponse(data: Auth::user());
    }

    public function refresh()
    {
        $refreshToken = input('refresh_token');
        if (!$refreshToken) {
            return errorResponse(status: 401, message: "Unauthorize");
        }

        try {
            $decoded = JWT::decode($refreshToken, new Key(env('JWT_REFRESH_SECRET'), 'HS256'));
            $refreshTokenModel = new RefreshToken;
            $token = $refreshTokenModel->find($refreshToken, 'refresh_token');
            if (!$token) {
                throw new \Exception("Token is valid");
            }

            $userId = $decoded->sub;

            $payload = [
                'sub' => $userId,
                'exp' => time() + env('JWT_EXPIRE'),
                'iat' => time(),
            ];
            $accessToken = JWT::encode($payload, env('JWT_SECRET'), 'HS256');
            return successResponse(data: [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ]);

        } catch (\Exception $e) {
            return errorResponse(status: 401, message: 'Unauthorize', errors: $e->getMessage());
        }
    }

    public function logout()
    {
        $token = Auth::user()->token;
        $expire = Auth::user()->expire;
        if ($token && $expire) {
            $blacklist = new BlacklistToken;
            try {
                $blacklist->create([
                    'token' => $token,
                    'expire' => $expire,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $userTransformer = new UserTransformer(Auth::user());
                return successResponse(data: $userTransformer);
            } catch (Exception $e) {
                return errorResponse(status: 401, message: "User logged out");
            }

        }
    }
}