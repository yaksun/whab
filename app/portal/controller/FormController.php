<?php 
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalCategoryModel;
use app\portal\service\MsgService;
use think\Db;

class FormController extends HomeBaseController{
    public function index(){
          // 顶部导航信息
          $portalCategoryModel = new PortalCategoryModel();
          $categorys =  $portalCategoryModel
          ->order('id')
          ->where('delete_time', 0)
          ->where('parent_id',0)
          ->select();
           $this->assign('categorys', $categorys);
           $id= $this->request->param('cid', 0, 'intval');
           $this->assign('categoryId',  $id);
        return $this->fetch('/form' );
    }

    public function submit(){
        if($this->request->isPost()){
         
         $data = $this->request->post();
          if(!$this->checkConfirm($data)){
            $this->error('确认密码和密码不一致');
          }elseif(session('code'.$data['phone']) != $data['captcha2']) {
                //  判断手机验证码
                 $this->error('验证码不正确');
          }

          if($this->doAddMember($data)){
            $this->success('提交申请成功');
          }else{
            $this->error('网络超时,请稍后重试');
          }
          
        }else {
            $this->error("请求错误");
        }
      
    }

    // 检查密码和确认密码
    public function checkConfirm($data){
        if($data['pass'] === $data['confirm_pass']){
            return true ;
        }
    }

    // 会员申请添加
    public function doAddMember($data){
        $params   = [
            'company'   => $data['company'],
            'user' => $data['user'],
            'pass'  => $data['pass'],
            'phone'        => $data['phone']
        ];
        try {
            //code...
            $res = Db::name('member')->insert($params);
            if($res){
                return true ;
            }

            return false;
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }


    // 发送验证码
    public function getCode(){
        if($this->request->isPost()){
            $data = $this->request->post();
            $phone = $data['phone'];
            $msgService = new MsgService();
            $code = rand(100000, 999999);
            $res = $msgService->sendTemplateSMS($phone,array($code,1),1);
            session('code'.$phone,$code);
        }else{
            $this->error("请求错误"); 
        }
       
    }

}