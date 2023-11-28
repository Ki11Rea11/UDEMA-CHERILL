<?php

namespace Src\Controllers;

use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\ServerRequest;
use ORM;

class UserController
{
    public function comment(ServerRequest $request, int $course_id)
    {
        date_default_timezone_set('Asia/Krasnoyarsk');
        $params=$request->getParsedBody();
        ORM::forTable('comments')->create([
            'course_id'=>$course_id,
            'user_id'=>$_SESSION['user_id'],
            'description'=>$params['comment'],
            'date'=>date("Y-m-d H:i"),
            'mark'=>$params['rating'],
            'is_accept'=>0
            ])->save();
        return new RedirectResponse('/course_detail/'.$course_id);
    }

    public function updateUserInfo(ServerRequest $request)
    {
        $params = $request->getParsedBody();
        $user = ORM::forTable('users')->find_one($_SESSION['user_id']);
            if ($params['old_email'] == $user['mail'] || !$params['old_email']){
                if ($params['new_email'] == $params['confirm_new_email']){
                    if (empty($params['new_email'])){
                        $user->set(['mail'=>$user['mail']]);}
                    else $user->set(['mail'=>$params['new_email']]);
                    if (md5($params['old_password']) == $user['password'] || !$params['old_password']){
                        if ($params['new_password'] == $params['confirm_new_password']){
                            if (empty($params['new_password'])){
                                $user->set(['password'=>$user['password']]);}
                            else $user->set(['password'=>md5($params['new_password'])]);
                            $random = bin2hex(random_bytes(10));
                            $user->set([
                                        'name'      => $params['name'],
                                        'last_name' => $params['lastname'],
                                        'phone'     => $params['phone'],
                                        'info'      => $params['info'],
                                    ]);
                            $user->save();
                            $_SESSION['error'] = 'Successful';
                            return new RedirectResponse('/user/profile');
                        }
                        $_SESSION['error'] = 'Wrong new or confirm password';
                        return new RedirectResponse('/user/profile');
                    }
                    $_SESSION['error'] = 'Wrong old password';
                    return new RedirectResponse('/user/profile');
                }
                $_SESSION['error'] = 'Wrong new or confirm email';
                return new RedirectResponse('/user/profile');
            }
            $_SESSION['error'] = 'Wrong old email';
            return new RedirectResponse('/user/profile');

    }



}


