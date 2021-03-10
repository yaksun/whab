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

class ListController extends HomeBaseController
{
    /***
     * 文章列表
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        $page=1;
        $pageSize=10;

        try{
            if($_GET['page'] && is_numeric($_GET['page'])){
               $page = $_GET['page'];
            }
           }catch(\Exception $e){
   
           }
        // 顶部导航信息
        $portalCategoryModel = new PortalCategoryModel();
        $categorys =  $portalCategoryModel
        ->order('id') ->where('parent_id',0)
        ->where('delete_time', 0)->select();
         $this->assign('categorys', $categorys);

       
        $cateid= $this->request->param('cid', 0, 'intval');

        $id= $this->request->param('sid', 0, 'intval');
        $mid=-1;
        try{
            if($this->request->param('mid', 0, 'intval')){
                $mid = $this->request->param('mid', 0, 'intval');
            }
        }catch(\Exception $e){

        }

        $cateItem = $portalCategoryModel->where('id', $cateid) ->where('delete_time', 0)->find();


        $categorylist = $portalCategoryModel->where('parent_id', $cateid) ->where('delete_time', 0)->select();

        $activecate = $portalCategoryModel->where('id', $id) ->where('delete_time', 0)->find();
        if($cateItem['name']==='会员名录'){
            $pageSize=8;
        }
       
        
        $catecount =  $portalCategoryModel
        ->order('id') ->where('parent_id',$id)
        ->where('delete_time', 0)
        ->select();


        // foreach($categorylist as $key=>$val) {
        //     print($val['id']);
        //     }
            
     
        $list;
        $allList;
        if($mid===-1){
             // 联表查询
             $allList = Db::table('cmf_portal_category_post')
             ->alias('a')
             ->join('cmf_portal_post w','a.post_id = w.id')
             ->join('cmf_portal_category c','a.category_id = c.id')
             ->where('w.delete_time',0)
             ->where('w.post_status',1)
             ->where('category_id', $id)
             ->order('w.published_time desc')
             ->select();
            $list = Db::table('cmf_portal_category_post')
            ->alias('a')
            ->join('cmf_portal_post w','a.post_id = w.id')
            ->join('cmf_portal_category c','a.category_id = c.id')
            ->where('w.delete_time',0)
            ->where('w.post_status',1)
            ->where('category_id', $id)
            ->limit(($page-1)* $pageSize , $pageSize)
            ->order('w.published_time desc')
            ->select();
        }else{
            $allList = Db::table('cmf_portal_category_post')
            ->alias('a')
            ->join('cmf_portal_post w','a.post_id = w.id')
            ->join('cmf_portal_category c','a.category_id = c.id')
            ->where('w.delete_time',0)
            ->where('w.post_status',1)
            ->where('category_id', $mid)
            ->order('w.published_time desc')
            ->select();
            $list = Db::table('cmf_portal_category_post')
            ->alias('a')
            ->join('cmf_portal_post w','a.post_id = w.id')
            ->join('cmf_portal_category c','a.category_id = c.id')
            ->where('w.delete_time',0)
            ->where('w.post_status',1)
            ->where('category_id', $mid)
            ->order('w.published_time desc')
            ->limit(($page-1)* $pageSize , $pageSize)
            ->select();
        }

       

        $this->assign('categorylist', $categorylist);
        $this->assign('categoryId',$cateid);
        $this->assign('activecate',$activecate);
        $this->assign('mid',$mid);
        $this->assign('list',$list);
        $this->assign('cateItem',$cateItem);
        $this->assign('count',$cateItem['name']==='会员名录' ? $catecount->count() :$allList->count());
        $this->assign('categorylistCount',$categorylist->count());
        $this->assign('page',$page);
        $this->assign('pageSize',$pageSize);


        if( $cateItem['name']==='协会概况' || $cateItem['name']==='入会申请' || $cateItem['name']==='会费标准' ||  $cateItem['name']==='会长信箱'){
            // 协会概况、入会申请、会费标准、会长信箱
            return $this->fetch('/membership');
        }elseif($cateItem['name']==='党建之窗' ||  $cateItem['name']==='行业动态' ||   $cateItem['name']==='下载中心'){
            // 党建之窗、保安风采、通知公告、行业动态、企业文化、业务咨询、学习园地、下载中心
            return $this->fetch('/list2');
        }elseif($cateItem['name']==='会员名录'){
            $catelist = $portalCategoryModel->where('parent_id', $id) ->where('delete_time', 0)
            ->limit(($page-1)* $pageSize , $pageSize)
            ->select();
            // foreach($catelist as $k=>$v){
            //     var_dump($v);
            // }

            $this->assign('catelist',$catelist);
            // 会员风貌
            return $this->fetch('/memberStyle');
        }elseif($cateItem['name']==='体会交流'){
            $this->assign('catelist',[]);
             // 体会交流
             return $this->fetch('/memberStyle');
        }elseif($cateItem['name'] === '先进单位'){

             // 先进单位
             $advancedTemp =  Db::table('cmf_portal_category')
             ->where('parent_id',$id)
             ->where('delete_time',0)
             ->select();
              $advancedUnit=[];
              foreach($advancedTemp as $key=>$val) {
                $advanceCount = Db::table('cmf_portal_category')
                ->where('parent_id',$val['id'])
                ->where('delete_time',0)
                ->select();
                  $val['list']=$advanceCount;
                  array_push($advancedUnit,$val);
              }

            $this->assign('advancedUnit',$advancedUnit);
            // var_dump($advancedUnit);
            return $this->fetch('/advancedUnit');
        }
       
        $listTpl = empty($categorylist['list_tpl']) ? 'list' : $categorylist['list_tpl'];


        return $this->fetch('/' . $listTpl);
    }

}
