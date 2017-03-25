<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Codes extends Controller
{
	public function generateAction()
	{
		if (request() -> isPost()) {
			//利用用户的输入信息生成相应的CRUD操作
			//收集用户的数据
			$module = input('post.module','admin','trim');
			$table = input('post.table','','trim');//brand(Brand),event_member(EventMember);

			//转换成代码需要的格式结构
			$module_name = $module;
// dump($module_name);die;
			//先使用_分割，针对每个元素的首字母大写，链接成一个字符串
			$model_name = implode('',array_map('ucfirst',explode('_',$table)));//模型名
			$controller_name = $model_name . 's';//控制器名
			// dump($controller_name);die;
			//处理当前表名标题
			$table_title = input('post.table_title',$model_name,'trim');//brand(品牌),event_member(会员事件)

			//处理模型对应的字段，知道哪个字段是主键
			$model = Db::name($model_name);
			$fields = $model -> getTableFields(true);//获取全部字段
			// $fields = $model -> field(true);
			$pk = $model->getPK();//获取主键
			// dump($fields);
			// dump($pk);
			
			//二、替换控制器代码模板
			//得到模板内容
			$controller_template_file = APP_PATH . 'admin/code/Controller.template.php';
			$controller_template = file_get_contents($controller_template_file);

			//进行替换
			$search = ['__MODULE__','__CONTROLLER__','__MODEL__','__PK_FIELD__'];
			$replace = [$module_name,$controller_name,$table,$pk];
			$controller_content = str_replace($search,$replace,$controller_template);

			//生成真正的控制器文件
			$controller_path = APP_PATH . $module_name . '/controller/';
			$controller_file = $controller_path . $controller_name . '.php';

			//保证目录正确
			if(! is_dir($controller_path)) {
				mkdir($controller_path,0775,true);
			}
			//写入
			$length = file_put_contents($controller_file,$controller_content);
			if ($length) {
				echo $controller_file,'生成完成','<br>';
			}

			//三、生成列表模板
			//生成局部的字段头，字段主体
			//头列表，主体列表
			$table_head_template_file = APP_PATH . 'admin/code/field_table_head.template.html';
			$table_body_template_file = APP_PATH . 'admin/code/field_table_body.template.html';
			//遍历所有的字段
			$table_head_list = $table_body_list = '';
			foreach($fields as $field) {
				$search = ['__FIELD_NAME__'];
				$replace = [$field];
				$table_head_list .= str_replace($search, $replace, file_get_contents($table_head_template_file));
				$table_body_list .= str_replace($search, $replace, file_get_contents($table_body_template_file));
			}
			//替换index整体的模板内容
			$search = ['__TABLE_TITLE__', '__MODEL__', '__TABLE_HEAD_LIST__', '__TABLE_BODY_LIST__', '__PK_FIELD__'];
			$replace = [$table_title, $model_name, $table_head_list, $table_body_list, $pk];
			$index_template_content = str_replace($search, $replace, file_get_contents(APP_PATH . 'admin/code/index.template.html'));
			//生成index整体模板内容
			$index_template_path = APP_PATH . $module_name . '/view/' . $controller_name;
			$index_template_file = $index_template_path . '/index.html';
			if(! is_dir($index_template_path)) {
				mkdir($index_template_path, 0775,true);
			}
			$length = file_put_contents($index_template_file, $index_template_content);
			if($length) {
				echo $index_template_file,'生成成功','<br>';
			}

			//四、生成添加模板
			//替换字段局部
			$form_field_list = '';
			foreach($fields as $field) {
				//添加时，不用处理主键字段
				if($field == $pk) continue;
				$search = ['__FIELD_NAME__'];
				$replace = [$field];
				$form_field_list .= str_replace($search,$replace,file_get_contents(APP_PATH.'admin/code/add_field.template.html'));
			}

			//替换整体
			$search = ['__MODEL__', '__TABLE_TITLE__', '__FORM_FIELD_LIST__'];
			$replace = [$model_name, $table_title,$form_field_list];
			$add_template_content = str_replace($search,$replace,file_get_contents(APP_PATH . 'admin/code/add.template.html'));

			//生成模板文件
			$add_template_path = APP_PATH . $module_name . '/view/' .$controller_name;
			$add_template_file = $add_template_path .'/add.html';
			if(! is_dir ($add_template_path)) {
				mkdir($add_template_path, 0775, true);
			}
			$length = file_put_contents($add_template_file,$add_template_content);
			if($length) {
				echo $add_template_file,'生成成功','<br>';
			}

			//五、生成编辑模板
			//替换局部字段
			$form_field_list = '';
			foreach($fields as $field) {
				//添加时，不用处理主键字段
				if($field == $pk) continue;
				//非主键字段，进行处理
				$search = ['__FIELD_NAME__'];
				$replace = [$field];
				$form_field_list .= str_replace($search, $replace,file_get_contents(APP_PATH.'admin/code/edit_field.template.html'));
			}
			//替换整体
			$search = ['__MODEL__','__TABLE_TITLE__','__FORM_FIELD_LIST__','__PK_FIELD__'];
			$replace = [$model_name,$table_title,$form_field_list,$pk];
			$edit_template_content = str_replace($search,$replace,file_get_contents(APP_PATH .'admin/code/edit.template.html'));

			//生成模板文件
			$edit_template_path = APP_PATH . $module_name .'/view/' .$controller_name;
			$edit_template_file = $edit_template_path . '/edit.html';
			if(! is_dir($edit_template_path)) {
				mkdir($edit_template_path,0775,true);
			}
			$length = file_put_contents($edit_template_file,$edit_template_content);
			if ($length) {
				echo $edit_template_file,'生成成功','<br>';
			}


		} else {
			//展示用户相关配置
			return view();
		}
	}
}