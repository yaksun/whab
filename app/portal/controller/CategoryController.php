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

class CategoryController extends HomeBaseController
{
    /***
     * 文章分类
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {   
        $page=1;
        $pageSize=10;
        // var_dump($_GET['page']);
       try{
         if($_GET['page'] && is_numeric($_GET['page'])){
            $page = $_GET['page'];
         }
        }catch(\Exception $e){

        }
        // 顶部导航数据
        $portalCategoryModel = new PortalCategoryModel();
        $categorys =  $portalCategoryModel
        ->order('id') ->where('parent_id',0)
        ->where('delete_time', 0)->select();
         $this->assign('categorys', $categorys);

        // 左侧导航数据和当前父分类
        $id= $this->request->param('cid', 0, 'intval');
        $categorylist = $portalCategoryModel->where('parent_id', $id) ->where('delete_time', 0)->select();

    //   $categorylist->isEmpty()
        

        $activecate = $portalCategoryModel->where('parent_id', $id) ->where('delete_time', 0)->find();
        $cateItem = $portalCategoryModel->where('id', $id) ->where('delete_time', 0)->find();
       
        if($cateItem['name']==='会员名录'){
            $pageSize=8;
        }
       

         $allList = Db::table('cmf_portal_category_post')
        ->alias('a')
        ->join('cmf_portal_post w','a.post_id = w.id')
        ->join('cmf_portal_category c','a.category_id = c.id')
        ->where('category_id',$activecate['id'])
        ->where('w.delete_time', 0)
        ->where('w.post_status',1)
        ->order('w.published_time desc')
        ->select();

        $list = Db::table('cmf_portal_category_post')
        ->alias('a')
        ->join('cmf_portal_post w','a.post_id = w.id')
        ->join('cmf_portal_category c','a.category_id = c.id')
        ->where('category_id',$activecate['id'])
        ->where('w.delete_time', 0)
        ->where('w.post_status',1)
        ->order('w.published_time desc')
        ->limit(($page-1)* $pageSize , $pageSize)
        ->select();

        // var_dump($list);
        // var_dump($count);
        //  foreach($list as $key=>$val) {
        //     print($val['parent_id']);
        //     }

        // if(empty($list)){
        //     return $this->fetch('/404');
        // }
     
        $catecount;
        $listTpl;
        if($cateItem['name']==='先进单位'){
            // 先进单位
              $listTpl='advancedUnit';
               $advancedTemp =  Db::table('cmf_portal_category')
               ->where('parent_id',$activecate['id'])
               ->where('delete_time',0)
               ->select();

               
                $advancedUnit=[];
                foreach($advancedTemp as $key=>$val) {
                    $advanceCount = Db::table('cmf_portal_category')
                    ->where('parent_id',$val['id'])
                    ->where('delete_time',0)
                    ->select();
                    // var_dump($advanceCount);
                    $val['list']=$advanceCount;
                    array_push($advancedUnit,$val);
                }

            $this->assign('advancedUnit',$advancedUnit);

        }elseif($cateItem['name']==='会员名录' ){
            $tempCate =  $portalCategoryModel
            ->order('id') ->where('parent_id',$id)
            ->where('delete_time', 0)->find();

            $catelist =  $portalCategoryModel
            ->order('id') ->where('parent_id',$tempCate['id'])
            ->limit(($page-1)* $pageSize , $pageSize)
            ->where('delete_time', 0)->select();

            $catecount =  $portalCategoryModel
            ->order('id') ->where('parent_id',$tempCate['id'])
            ->where('delete_time', 0)
            ->select();

            // foreach($catelist as $k=>$v){
            //     var_dump( $v);
            // }

            $this->assign('catelist',$catelist);
            $listTpl='memberStyle';

        }elseif( $cateItem['name']==='体会交流'){
            $this->assign('catelist',[]);
           
            // 体会交流 
            $listTpl='memberStyle';

        }elseif ( $cateItem['name']==='协会概况' || $cateItem['name']==='入会申请' || $cateItem['name']==='会费标准' || $cateItem['name']==='会长信箱') {
            // 协会概况、入会流程、走进协会、会费标准
            $listTpl='membership';
        }elseif(  $cateItem['name']==='党建之窗' ||  $cateItem['name']==='行业动态' ||   $cateItem['name']==='下载中心'){
            // 党建之窗、行业动态、下载中心
            $listTpl='notice';
        }elseif($cateItem['name']==='能力评价'){
            // 能力评价
            $listTpl='ability';
        }elseif($cateItem['name']==='教育培训'){
            // 教育培训
            $listTpl='education';

            $education = Db::table('cmf_portal_category_post')
            ->alias('a')
            ->join('cmf_portal_post w','a.post_id = w.id')
            ->join('cmf_portal_category c','a.category_id = c.id')
            ->where('category_id',$id)
            ->where('w.delete_time', 0)
            ->where('w.post_status',1)
            ->order('w.published_time desc')
            ->find();
           
            $this->assign('education',$education);
       
        } else{
          $listTpl = empty($categorylist['list_tpl']) ? 'category' : $categorylist['list_tpl'];
        }

        $this->assign('categorylist', $categorylist);
        $this->assign('categorylistCount', $categorylist->count());
        $this->assign('categoryId',$id);
        $this->assign('cateItem',$cateItem);
        $this->assign('activecate',$activecate);
        $this->assign('list',$list);
        $this->assign('count',$cateItem['name']==='会员名录' ? $catecount->count() : $allList->count());
        $this->assign('page',$page);
        $this->assign('pageSize',$pageSize);

        return $this->fetch('/' . $listTpl);
       
       
       
    }

}
