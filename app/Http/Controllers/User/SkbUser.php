<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Traits\{Tool, Session};
use App\Models\User\SkbUsersModel as SkbUsers;
use Illuminate\Http\Request;
use QCloud_WeApp_SDK\Conf as Config,
    QCloud_WeApp_SDK\Auth\LoginService as LoginService,
    QCloud_WeApp_SDK\Constants as Constants;

class SkbUser extends Controller
{
    public function __construct()
    {
        Config::setup(config('services.wechat'));
    }

    /**
     * @param Session $ssn
     * @return $this
     */
    public function getUserInfo(Session $ssn)
    {
        $user_id = $ssn->get('id');

        if ($user_id && is_int($user_id)) {
            $dat = SkbUsers::find($user_id);
        }

        $dat = $dat ?? [];
        $err = $dat ? 0 : 404;
        $msg = $dat ? 'success' : 'fail';

        return Tool::jsonR($err, $msg, $dat);
    }

    public function index()
    {
        $result = LoginService::check();

        if ($result['loginState'] === Constants::S_AUTH) {
            return Tool::jsonResp([
                'err' => 0,
                'dat' => $result['userinfo']
            ]);
        } else {
            return Tool::jsonResp([
                'err' => -1,
                'msg' => []
            ]);
        }
    }

    public function login(Session $ssn)
    {
        $result = LoginService::login();

        if ($result['loginState'] === Constants::S_AUTH) {

            $users  = new SkbUsers();

            $user   = $users->where('openid','=',$result['userinfo']['userinfo']->openId)
                            ->first();

            if($user){
                $user->toArray();
                $ssn->set('user' , $user);

                return Tool::jsonResp([
                    'err' => 0,
                    'dat' => [
                        'dat' => $ssn->get('user'),
                        'ssn' => session_id()
                    ]
                ]);
            }

            //注册用户 by jizw
            $user = [
                'username'      =>  '',
                'openid'        =>  $result['userinfo']['userinfo']->openId,
                'nickname'      =>  $result['userinfo']['userinfo']->nickName,
                'avatar'        =>  $result['userinfo']['userinfo']->avatarUrl,
                'created_at'    =>  date('Y-m-d H:i:s'),
                'updated_at'    =>  date('Y-m-d H:i:s')
            ];

            $user[] = ['id' => $users->insertGetId($user)];

            if (!$user['id']) return Tool::jsonResp([
                'err' => -1,
                'msg' => '服务器开小差了,请稍后再试'
            ]);

            $ssn->set('user',$user);

            return Tool::jsonResp([
                'err' => 0,
                'dat' => $user
            ]);
        }

        return Tool::jsonResp([
            'err' => -1,
            'msg' => $result['error']
        ]);
    }

    public function testLogin(Request $req, Session $ssn)
    {
        $users  = new SkbUsers();
        $params = $req->post('id');

        $user   = $users->where('id', $params)
                        ->first()
                        ->toArray();

        if($user){
            $ssn->set('user',$user);

            return Tool::jsonResp([
                'err' => 0,
                'dat' => $user,
                'ssn' => session_id()
            ]);
        }

        return Tool::jsonResp([
            'err' => -1,
            'msg' => 'you 挂了!!!!',
            'dat' => null
        ]);
    }
}