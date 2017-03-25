<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;
use think\Validate;
use app\admin\model\Setting;


class Settings extends Controller
{
	/**
	 * 展示品牌
	 * @return [type] [description]
	 */
	public function indexAction(){
		//获取分组
		
		$group_rows = db('setting_group')->select();
		$this -> assign('group_rows',$group_rows);

		//获取配置项
		
		$setting_rows = model('Setting')
						->alias('s')
						->join('__SETTING_TYPE__ st', 'st.setting_type_id = s.setting_type_id','left')
						//->join('__SETTING_OPTION__ so', 'so.setting_id = s.setting_id', 'left')							
						->select();
		
		//遍历所有的配置项，分组管理
		$group_setting = [];
		foreach($setting_rows as $settings) {
			$setting = $settings->getdata();
			// dump($setting);die;
			// dump($settings->title);//即可得到title数据
			// dump($settings->getdata());//将对象数据转化为数组
			// dump($settings->options);//关联模型里面的所有数据
			// foreach($settings->options as $option) {
				// dump($option->option_title);die;//里面为对象，可直接访问
			// }
			$setting['option'] = $settings->options;
			
			//判断是否为多选类型，如果是，拆分为value为数组
			if ($setting['type_title'] == 'select-multi') {
				$setting['value_list'] = explode(',' , $setting['value']);
			}
			//当前分组ID
			$group_id = $setting['setting_group_id'];
			//将配置项，存储在以组ID为下标的数组
			$group_setting[$group_id][] = $setting;

		}
		//[1=>[配置项1，配置项2]]
		
		// dump($group_setting);
		
		return view('index',['group_setting'=>$group_setting]);
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
				//批量验证
				$result = $validate->batch()->check($data);
				if(true !== $result){
					// 验证失败 输出错误信息
					$errors = $validate->getError();
					$this->error($errors);
				}
		
				// $res = Setting::create($data);
				$res = db('setting')->save($data);	
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
		$setting_id =input('param.setting_id','','trim');
		
		$this -> assign('row', db('setting')->find($setting_id));
		// $this -> assign('row', setting::get($setting_id));
		return view();
	}
	/**
	 * 更新操作
	 */
	public function updateAction()
	{
		//获取所有的配置项
		$setting = input('post.setting/a');
		// dump($setting);die;
		$m_setting = model('Setting');
		//保证多选配置项，存在合理的数据
		//获得所有的多选配置项ID
		$cond['type_title'] = 'select-multi';
		$multi_setting = $m_setting
						->alias('s')
						->join('__SETTING_TYPE__ st', 's.setting_type_id = st.setting_type_id' ,'LEFT')
						->where($cond)
						->column('setting_id');
		// dump($multi_setting);die;
		// 判断多选类型的配置项是否出现在用户提交的post数据中
		foreach($multi_setting as $m_setting_id) {
			if (! isset($setting[$m_setting_id])) {
				//用户没有选择任何多选选项.为空数组
				$setting[$m_setting_id] = '';
			}
		}
		//遍历配置项，更新配置项
		foreach($setting as $setting_id=>$value) {
			//如果是数组，多选类型，则将多选值逗号链接起来
			if(is_array($value)) {
				$value = implode(',',$value);
			}
			$result = $m_setting->update(['setting_id'=>$setting_id, 'value'=>$value]);
			if(!$result){
				$this -> error('数据更新失败:');
			}
		}

		//清空所有的配置项缓存
		//获取所有的配置项key，key与缓存项的key是对应
		// S(['type'=>'File']);
		// foreach($m_setting->getField('key', true) as $key) {
		// 	S('setting_' .$key, null);
		// }
		
		
		
		$this -> redirect('index');
	}
	/**
	 * 删除动作
	 */
	public function deleteAction()
	{
		
	}
	/**
	 * ajax验证
	 */
	public function changeAction()
	{
		$data = input('post.');

		$setting_id = $data['setting_id'];
		$value = $data['value'];
		$key = $data['key'];
		if(is_array($value)) {
				$value = implode(',',$value);
			}
		
		//更新一条数据，第二个为更新条件
		$res = model('Setting')->save(['value'=>$value],['setting_id'=>$setting_id] );

		if($res){
            $data=[
                'status' => 1,
                'msg'=>'更新成功',
            ];
            //获取所有的配置项key，key与缓存项的key是对应
			// S(['type'=>'File']);
			// S('setting_' .$key, null);
        }else{
            $data=[
                'status' => 0,
                'msg'=>'更新失败，请稍后再试',
            ];
        }
		return $data;
		
	}
}