<?php 
namespace app\portal\controller;

use think\facade\Validate;
use cmf\controller\HomeBaseController;
use think\Db;
  
class LoginController extends HomeBaseController {
     /**
     * 首页登录提交
     * */ 
    public function index(){
      
        if($this->request->isPost()){
         
            $data = $this->request->post();
            
            if (!cmf_captcha_check($data['captcha'])) {
                $this->error(lang('CAPTCHA_NOT_RIGHT'));
            }elseif(!$this->doUser($data['user'])){
                $this->error('用户名或密码错误'); 
            }elseif(!$this->doPass($data['user'],$data['pass'])){
                $this->error('用户名或密码错误'); 
            }else{
                // return $this->fetch('/uploads'); 
                $this->success('登录成功');

            }
        }else {
            $this->error("请求错误");
        }
      
    }

    function doUser($user){
        $member = Db::table('cmf_member')
        ->where('user',$user)
        ->find();

        return $member;
    }

    function doPass($user,$pass){
        $member = Db::table('cmf_member')
        ->where('user',$user)
        ->find();
        $status = $member['pass'] === $pass;

        return $status;
    }
}
