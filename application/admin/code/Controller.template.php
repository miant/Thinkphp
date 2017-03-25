<?php
namespace app\__MODULE__\controller;

use think\Controller;
use think\Request;
use think\Validate;
// use app\__MODULE__\model\__MODEL__;

class __CONTROLLER__ extends Controller
{
	/**
	 * 展示品牌
	 * @return [type] [description]
	 */
	public function indexAction(){
		// $cond查询条件
		$cond = $filter = [];
		// $filter['filter_title'] = input('param.filter_title', '', 'trim');
		
		// if($filter['filter_title'] != ''){
		// 	$cond['title'] = ['like' , '%'.$filter['filter_title'].'%'];//适当考虑索引问题
		// }
		//分配筛选数据到模板 
		$this -> assign('filter',$filter);
		// 排序(考虑用户字段和排序方式)可考虑索引
		$order['field'] = input('param.field','__PK_FIELD__','trim');
		$order['type'] = input('param.type','asc','trim');

		$sort = [$order['field'] => $order['type']];
		
		// 分配排序数据
		$this -> assign('order',$order);
		
		$list = db('__MODEL__')
					->where($cond)
					->order($sort)
					->paginate(10);
		return view('index',['data'=>$list]);
	}
	/**
	 * 添加操作
	 */
	public function addAction(){
		if(request()->isPost()){
				$data = input('post.','','trim');
				// dump($data);die;
				$rules = [
					'title' => 'require',
					'sort_number' => 'number'	
				];
				$msg = [
					'title.require' => '名称必须填写',
					// 'title.unique' => '名称不能重复',
					'sort_number.number' => '排序必须为整数',
				];
				//调用静态方法实例化对象
				$validate =Validate::make($rules, $msg);
				$result = $validate->batch()->check($data);
				if(true !== $result){
					// 验证失败 输出错误信息
					$errors = $validate->getError();
					$this->error($errors);
				}
		
				// $res = __MODEL__::create($data);
				$res = db('__MODEL__')->save($data);	
				if($res){
					$this->redirect('index');
					
				}
				
		}else{
			return view();
		}
	}
	/**
	 * 编辑动作
	 */
	public function editAction()
	{
		$__PK_FIELD__ =input('param.__PK_FIELD__','','trim');
		
		$this -> assign('row', db('__MODEL__')->find($__PK_FIELD__));
		// $this -> assign('row', __MODEL__::get($__PK_FIELD__));
		return view();
	}
	/**
	 * 更新操作
	 */
	public function updateAction()
	{
		$data = input('post.','','trim');
		// $result = __MODEL__::update($data);
		$result = db('__MODEL__')->update($data);
		if(!$result){
			$this -> error('数据更新失败:');
		}
		$this -> redirect('index');
	}
	/**
	 * 删除动作
	 */
	public function deleteAction()
	{
		//确定动作
		$operate = input('post.operate','delete','trim');
		//确定ID列表
		$selected = input('post.selected/a');
		 // 如果为空数组, 表示没有选择, 则立即跳转回列表页.
        if (empty($selected)) {
            $this->redirect('list', [], 0);
            return ;
        }
		switch($operate){
			case 'delete':
				// __MODEL__::destroy($selected);
				db('__MODEL__')->where('__PK_FIELD__','in',$selected)->delete();
				$this -> redirect('index');
				break;
			default:
				break;

		}
	}
	/**
	 * ajax验证
	 */
	public function ajaxAction(){
		 $operate = input('param.operate', null, 'trim');	
		if (is_null($operate)) {
			return;
		}
		switch($operate){
			//验证品牌唯一性名称操作
			case 'check__MODEL__Unique':
			//获取填写的品牌名称 
				$title = input('request.title','');

				$cond['title'] = $title;
				// 判断是否传递了__PK_FIELD__
				$__PK_FIELD__ = input('request.__PK_FIELD__',null);
				if(!is_null($__PK_FIELD__)) {
					//存在则匹配与当前记录不相同的ID
					$cond['__PK_FIELD__'] = ['neq',$__PK_FIELD__];
				}
				//获取模型后利用条件来查询匹配数
				$count = db('__MODEL__')->where($cond)->count();
				//如果记录大于零，则为真，说明存在记录，重复，验证未通过。响应false
				return $count ? false :true;
				break;
		}
	}
}