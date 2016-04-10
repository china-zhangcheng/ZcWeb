<?php
namespace App\Action;
class SoftwareAction extends CommonAction {

	const TYPE_GUI = 1;
	const TYPE_GAME = 2;

	/*
	 * @brief 设置每一页标题
	 */
	public function _initialize() {
		parent::_initialize ();
		$action_name = strtolower ( ACTION_NAME );
		switch ($action_name) {
			case 'index' :
				$this->assign ( "website_title", "软件首页" );
				break;
			case 'create' :
				$this->assign ( "website_title", "发布软件" );
				break;
			case 'update' :
				$this->assign ( "website_title", "修改软件" );
				break;
		}
	}
	// 遍历列表 前20个软件
	public function index() {
		$page = I('get.page',1,'int');

		$limit = 10;
		$result = M('software')->page($page,$limit)->select();
		$count = M('software')->count();
		$page = new \Think\Page($count,$limit);
		$show = $page->show();
		$this->assign('page',$show);
		$this->assign('result',$result);
		$this->display ();
	}
	
	// 创建软件
	public function create() {
		if(IS_GET){
			$this->display();
		}else{
			$data = I('post.');

			$model =  M('software');
			if( $model->create($data) ){
				if($model->add()){
					$this->formSuccess('添加成功');
				}else{
					$this->formError('添加失败');
				}
			}else{
				$this->formError($model->getError());
			}
		}

	}
	// 更新软件
	public function update() {
		if(IS_GET){
			$this->display();
		}else{
			$data = I('post.');

			$model =  M('software');
			if( false === $model->where(array('id'=>$data['id']))->save($data) ){

				$this->formSuccess('更新成功');

			}else{
				$this->formError('更新失败');
			}
		}

	}
	
	// 删除软件
	public function del() {

		$id = I('get.id',0,'int');

		$res = M('software')->where(array('id'=>$id))->delete();

		if($res){
			$this->formSuccess('删除成功');

		}else{
			$this->formError('删除失败');
		}
	}
	

//	public function search() {
//	}


	public function gui()
	{
		$page = I('get.page',1,'int');

		$limit = 10;
		$result = M('software')->where(array('type'=>self::TYPE_GUI))->page($page,$limit)->select();
		$count = M('software')->where(array('type'=>self::TYPE_GUI))->count();
		$page = new \Think\Page($count,$limit);
		$show = $page->show();
		$this->assign('page',$show);
		$this->assign('result',$result);
		$this->assign('soft_type','gui');
		$this->display ();
	}


	public function game()
	{
		$page = I('get.page',1,'int');

		$limit = 10;
		$result = M('software')->where(array('type'=>self::TYPE_GAME))->page($page,$limit)->select();
		$count = M('software')->where(array('type'=>self::TYPE_GAME))->count();
		$page = new \Think\Page($count,$limit);
		$show = $page->show();
		$this->assign('page',$show);
		$this->assign('result',$result);
		$this->assign('soft_type','game');
		$this->display ();
	}


	public function guidetail()
	{

		$this->display();
	}

	public function gamedetail()
	{
		$this->display();
	}
}

