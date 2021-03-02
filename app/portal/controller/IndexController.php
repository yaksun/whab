<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use app\portal\model\PortalCategoryModel;
use think\Db;

class IndexController extends HomeBaseController
{
    public function index()
    {   

        // $top_slide_id=empty($theme_vars['portal_index'])?1:$theme_vars['portal_index'];
        // var_dump($top_slide_id);

        // 顶部导航信息
        $portalCategoryModel = new PortalCategoryModel();
       $categorys =  $portalCategoryModel
       ->order('id')
       ->where('delete_time', 0)
       ->where('parent_id',0)
       ->select();
        $this->assign('categorys', $categorys);

      //    协会要闻
        $this->getAssociationNews();

        // 通知公告
        $this->getAnnouncementsList();
       
        // 行业新闻 
        $this->getIndustryNews();

        // 会员新闻 
        $this->getMemberNews();

        // 会员风貌
        $this->getMemberStyle();

        // 党建之窗
        $this->getPartyBuilding();

        // 协会服务
        $this->getAssociationServices();

        // 教育培训
        $this->getEducationTraining();

        // 能力评价
        $this->getAbilityEvaluation();

        // 协会概况
        $this->getAssociationProfile();

 

        // 幻灯片
        $slides = Db::table('cmf_slide_item')
        ->where('status',1)
        ->order('id')
        ->select();


        $this->assign('slides',$slides);

        // var_dump($enterpriseCulture->isEmpty());

        $this->assign('categoryId', '0');
     
  
        return $this->fetch(':index');
    }

    // 协会要闻
   public function getAssociationNews(){
        
         $associationNews = $this->getFirstChildList('协会要闻','协会要闻',4);
         $associationNews =  $this->changeList($associationNews,3);
         $this->assign('associationNews',$associationNews);
    }

    // 通知公告
   public function getAnnouncementsList(){
         // 通知公告
         $announcementsList = $this->getFirstChildList('通知公告','通知公告',8);
        
         $temp = [];

         foreach( $announcementsList as $k=>$v){
             array_push($temp,$v);
         }

         $rightAnnouncementsList =  $temp;
         if(count($announcementsList)>4){
            $rightAnnouncementsList = array_slice($temp,4);
            $announcementsList = array_slice($temp,0,4);
         }

         $this->assign('announcementsList',$announcementsList);
         $this->assign('rightAnnouncementsList',$rightAnnouncementsList);
    }

    // 行业新闻
    public function getIndustryNews()
    {   
        $industryNews = $this->getFirstChildList('行业动态','行业新闻',3);
        $this->assign('industryNews',$industryNews);
    }

    // 会员新闻
    public function getMemberNews()
    {   
        $memberNews = $this->getFirstChildList('行业动态','会员新闻',3);
        $this->assign('memberNews',$memberNews);
    }


    // 会员风貌
    public function getMemberStyle(){

        $portalCategoryModel = new PortalCategoryModel();

         $memberParent =  $portalCategoryModel
          ->where('name','会员名录')
          ->where('parent_id',0)
         ->where('delete_time', 0)
         ->find();   

         $memberIds =  $portalCategoryModel
         ->order('id')
         ->where('parent_id',$memberParent['id'])
         ->where('delete_time', 0)
         ->select();
     
         $MemberStyle=[];
          foreach($memberIds as $k=>$v){
            $tempMember=Db::table('cmf_portal_category')
            ->where('parent_id',$v['id'])
            ->select();


            foreach($tempMember as $m=>$n){
                $flag = false;
                if($flag){
                    $temp1=Db::table('cmf_portal_category')
                    ->where('parent_id',$n['id'])
                    ->find();
    
                    $temp2=Db::table('cmf_portal_category')
                    ->where('parent_id',$temp1['id'])
                    ->find();
                   
                    $n['thumbnail'] = json_decode($temp2['more'])->{'thumbnail'};
                    array_push($MemberStyle,$n);
                }else{
                    $n['thumbnail'] = json_decode($n['more'])->{'thumbnail'};
                    array_push($MemberStyle,$n);
                }
               
            }

          }  

          $MemberStyle = $this->changeList($MemberStyle,8);

          $this->assign('MemberStyle',$MemberStyle);
          $this->assign('memberParent',$memberParent);
      
    }

    // 党建之窗
    public function getPartyBuilding(){
       
        $partyBuilding = $this->getAllList('党建之窗',6);
        $this->assign('partyBuilding',$partyBuilding);
        $partyBuildingStatus = count($partyBuilding)>6;
        $this->assign('partyBuildingStatus',$partyBuildingStatus);
    }

    // 协会服务
    public function getAssociationServices(){
        $associationServices = $this->getFirstChildList('协会服务','协会服务',6);
        $this->assign('associationServices',$associationServices);
        $associationServicesStatus = count($associationServices)>6;
        $this->assign('associationServicesStatus',$associationServicesStatus);
    }

    // 教育培训
    public function getEducationTraining(){
        $educationTraining = $this->getAllList('教育培训',6);
        $this->assign('educationTraining',$educationTraining);
        $educationTrainingStatus = count($educationTraining)>6;
        $this->assign('educationTrainingStatus',$educationTrainingStatus);
    }

    // 能力评价
    public function getAbilityEvaluation(){
        $abilityEvaluation = $this->getFirstChildList('能力评价','能力评价',6);
        $this->assign('abilityEvaluation',$abilityEvaluation);
        $abilityEvaluationStatus = count($abilityEvaluation)>6;
        $this->assign('abilityEvaluationStatus',$abilityEvaluationStatus);
    }

    // 协会概况
    public function getAssociationProfile(){
        // $portalCategoryModel = new PortalCategoryModel();
         
    //   $asociationProfileParent = $portalCategoryModel
    //   ->where('delete_time', 0)
    //   ->where('parent_id',0)
    //   ->where('name','协会概况')
    //   ->find();

    // //   协会概况下的分类
    //   $associationProfileCates=$portalCategoryModel
    //   ->where('delete_time', 0)
    //   ->where('parent_id',$asociationProfileParent['id'])
    //   ->limit(1,4)
    //   ->select(); 

    //   $this->assign('associationProfileCates',$associationProfileCates);


      $profileFirst = $this->getFirstChildList('协会概况','协会简介',1);
      $this->assign('profileFirst',$profileFirst[0]);

    //   $membershipApplicationParent = $portalCategoryModel
    //   ->where('delete_time', 0)
    //   ->where('parent_id',0)
    //   ->where('name','入会申请')
    //   ->find();

    //   $membershipApplication = $portalCategoryModel
    //   ->where('delete_time', 0)
    //   ->where('parent_id',$membershipApplicationParent['id'])
    //   ->find();
    //   $this->assign('membershipApplication',$membershipApplication);

    }


    // 获取顶级分类下级的所有数据
    public function getAllList($name,$len){
        $portalCategoryModel = new PortalCategoryModel();
         
        //  获取顶级分类
        $newsParent = $portalCategoryModel
        ->where('delete_time', 0)
        ->where('parent_id',0)
        ->where('name',$name)
        ->find();

        
      $news = Db::table('cmf_portal_category_post')
      ->alias('a')
      ->join('cmf_portal_post w','a.post_id = w.id')
      ->join('cmf_portal_category c','a.category_id = c.id')
      ->where('c.parent_id',$newsParent['id'])
      ->where('w.delete_time',0)
      ->where('w.post_status',1)
      ->order('w.published_time desc')
      ->limit($len)
      ->select();

      return $news;

    }


       // 获取顶级分类的第一个下级分类的文章
    public function getFirstChildList ($name,$son,$len)
    {
      $portalCategoryModel = new PortalCategoryModel();
         
        //  获取顶级分类
      $newsParent = $portalCategoryModel
      ->where('delete_time', 0)
      ->where('parent_id',0)
      ->where('name',$name)
      ->find();
          //  获取二级分类的
      $newsChildren = $portalCategoryModel
      ->where('delete_time', 0)
      ->where('parent_id',$newsParent['id'])
      ->where('name',$son)
     ->find();

   
        // 根据第一个分类元素获取相关的文章
      $news = Db::table('cmf_portal_category_post')
      ->alias('a')
      ->join('cmf_portal_post w','a.post_id = w.id')
      ->join('cmf_portal_category c','a.category_id = c.id')
      ->where('c.id',$newsChildren['id'])
      ->where('w.delete_time',0)
      ->where('w.post_status',1)
      ->order('w.published_time desc')
      ->limit($len)
      ->select();

      return $news;

     
    }

  

    // 公共的改变数组方法
    public function changeList($arr,$len){
        $allList=[];
        if(count($arr) && count($arr)<$len){
          
            $count =  floor($len/count($arr));
            $mini = $len%count($arr); 

            
            for($i=0;$i<$count;$i++){
                foreach($arr as $v){
                    array_push($allList,$v);
                }
            }

            for($j=0;$j<$mini;$j++){
                array_push($allList,$arr[$j]);
            }

        }else{
            $allList = $arr;
        }

        return $allList;
    }
}
