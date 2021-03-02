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
use app\portal\service\PostService;
use app\portal\model\PortalPostModel;
use think\Db;

class ArticleController extends HomeBaseController
{
    /**
     * 文章详情
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {

        $portalCategoryModel = new PortalCategoryModel();
        $postService         = new PostService();

        $articleId  = $this->request->param('id', 0, 'intval');
        $categoryId = $this->request->param('sid', 0, 'intval');
        $article; 
        $prevArticle;
        $nextArticle;


        $categorys =  $portalCategoryModel
        ->order('id')->where('parent_id',0)
        ->where('delete_time', 0)->select();
         $this->assign('categorys', $categorys);



        $flag=false; 

        $parentItem = Db::table('cmf_portal_category')
        ->where('id',$categoryId)
        ->find();

        $father =  Db::table('cmf_portal_category')
        ->where('id',$parentItem['parent_id'])
        ->find();


        if($father['name']==='体会交流'){
            $flag = true;
        }

        $this->assign('flag',$flag);


        // 如果主分类是会员名录
        if($father['name']==='会员名录'){
            $catelist =  $portalCategoryModel
            ->order('id') ->where('parent_id',$articleId)
            ->where('delete_time', 0)->select();

            $cateItem = $portalCategoryModel->where('id',$articleId) ->where('delete_time', 0)->find();
          
            foreach($catelist as $k=>$v){
                $temp = $portalCategoryModel
                ->order('id') ->where('parent_id',$v['id'])
                ->where('delete_time', 0)->select();
                // if($temp->count()){
                // var_dump($temp[0]['more']['thumbnail']);
                // }
                $allList=[];
                if(count($temp) && count($temp)<4 && $v['name']!='联系我们'){
                    $count =  floor(4/count($temp));
                    $mini = 4%count($temp); 
                    
                    for($i=0;$i<$count;$i++){
                        foreach($temp as $vi){
                            array_push($allList,$vi);
                        }
                    }
        
                    for($j=0;$j<$mini;$j++){
                        array_push($allList,$temp[$j]);
                    }

                    // foreach($allList as $k=>$m){
                    //     var_dump($m);
                    // }
        
                }else{
                    $allList = $temp;
                }
               

                $v['list'] = $allList; 

            }

           

           $this->assign('cateItem',$cateItem);
           $this->assign('parentItem',$parentItem);
           $this->assign('catelist',$catelist);
           $this->assign('categoryId',$father['id']);
           $this->assign('articleId', $articleId);
           $this->assign('father', $father);
           return $this->fetch("/leave1");

        }else{
            $article=$postService->publishedArticle($articleId, $categoryId);
            $prevArticle = $postService->publishedPrevArticle($articleId, $categoryId);
            $nextArticle = $postService->publishedNextArticle($articleId, $categoryId);
            $this->assign('prev_article', $prevArticle);
            $this->assign('next_article', $nextArticle);
        }



        if (empty($article)) {
            abort(404, '文章不存在!');
        }


       
        $tplName = 'article';

        if (empty($categoryId)) {
            $categories = $article['categories'];

            if (count($categories) > 0) {
                $this->assign('category', $categories[0]);
            } else {
                abort(404, '文章未指定分类!');
            }

        } else {
            $category = $portalCategoryModel->where('id', $categoryId)->where('status', 1)->find();

            if (empty($category)) {
                abort(404, '文章不存在!');
            }

            $this->assign('category', $category);

            $tplName = empty($category["one_tpl"]) ? $tplName : $category["one_tpl"];
        }

        Db::name('portal_post')->where('id', $articleId)->setInc('post_hits');


        hook('portal_before_assign_article', $article);

        // 导航 
      

         $categorylist = $portalCategoryModel->where('parent_id', $category['parent_id']) ->where('delete_time', 0)->select();
         $cateItem = $portalCategoryModel->where('id',$category['parent_id']) ->where('delete_time', 0)->find();
         $activecate = $portalCategoryModel->where('id',$categoryId) ->where('delete_time', 0)->find();


        $this->assign('article', $article);
      
        $this->assign('categorylist', $categorylist);
        $this->assign('categoryId', $category['parent_id']);
        $this->assign('cateItem', $cateItem);
        $this->assign('activecate', $activecate);


        // var_dump($category['parent_id']);
        $tplName = empty($article['more']['template']) ? $tplName : $article['more']['template'];

        return $this->fetch("/$tplName");
    }

    // 文章点赞
    public function doLike()
    {
        $this->checkUserLogin();
        $articleId = $this->request->param('id', 0, 'intval');


        $canLike = cmf_check_user_action("posts$articleId", 1);

        if ($canLike) {
            Db::name('portal_post')->where('id', $articleId)->setInc('post_like');

            $this->success("赞好啦！");
        } else {
            $this->error("您已赞过啦！");
        }
    }


    public function newdoLike()
    {
        $articleId = $this->request->param('id', 0, 'intval');

        Db::name('portal_post')->where('id', $articleId)->setInc('post_like');

        $this->success("赞好啦！");
     
    }

}
