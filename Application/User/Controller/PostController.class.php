<?php
namespace User\Controller;
use Think\Controller;

/**
 * @author Zhou Yuyang <1009465756@qq.com> 2015-07-27
 * @copyright ©2105-2018 SRCMS
 * @homepage http://www.src.pw
 * @version 1.0
 */
 
class PostController extends Controller
{
    /**
     * 漏洞报告列表
     * @return [type] [description]
     */
    public function index($key="")
    {
        if($key == ""){
            $model = D('PostView'); 
        }else{
            $where['post.title'] = array('like',"%$key%");
            $where['member.username'] = array('like',"%$key%");
            $where['category.title'] = array('like',"%$key%");
            $where['_logic'] = 'or';
            $model = D('PostView')->where($where); 
        } 
        
		$id = session('userId');
        $count  = $model->where($where)->where('user_id='.$id)->count();// 查询满足要求的总记录数
        $Page = new \Extend\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show = $Page->show();// 分页显示输出
        $post = $model->limit($Page->firstRow.','.$Page->listRows)->where($where)->order('post.id DESC')->where('user_id='.$id)->select();
        $this->assign('model', $post);
        $this->assign('page',$show);
        $this->display();     
    }
    /**
     * 添加漏洞报告
     */
    public function add()
    {
        //默认显示添加表单
        if (!IS_POST) {
        	$this->assign("category",getSortedCategory(M('category')->select()));
            $this->display();
        }
        if (IS_POST) {
            //如果用户提交数据
            $model = D("Post");
            $model->time = time();
            $model->user_id = 1;
            if (!$model->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                $this->error($model->getError());
                exit();
            } else {
                if ($model->add()) {
                    $this->success("添加成功", U('post/index'));
                } else {
                    $this->error("添加失败");
                }
            }
        }
    }
}
