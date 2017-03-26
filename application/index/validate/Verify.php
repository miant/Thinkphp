<?php

namespace app\index\validate;

use think\Validate;

class Verify extends Validate
{
	protected $rule = [
					'name' =>'require|unique:member|chsDash|length:6,20',
					'email'=>'require|email|unique:member',
					'password' =>"require|length:6,20|alphaSpecial|strong:",
					'repassword'=>'require|confirm:password',
					'is_newsletter' =>'require|in:0,1',
					'agree' => 'accepted',
					'title' => 'require|unique:member',
					'sort_number' => 'number'
				];
	protected $message = [
					'name.require' =>'名字必须',
					'name.unique'  =>'名字已存在',
					'name.chsDash' =>'只能是汉字、字母、数字和下划线_及破折号-',
					'name.length'  =>'名字字符在6~20之间',

					'email.require'=>'邮箱必须',
					'email.unique' =>'邮箱已存在',
					'email'  =>'邮箱格式不正确',

					'password.require' => '密码必须填',
					'password.length'  => '密码必须字符在6~20之间',
					'password.alphaSpecial'=>'密码字符任意',
					'password.strong' => '密码必须要包含字母、数字特殊符号中两种或两种一种上',
					'repassword.require' => '确认密码必须填',
					'repassword.confirm' => '确认密码必须一致',

					'is_newsletter.require' => '订阅信息可选',
					'is_newsletter.in' => '订阅信息可选',
					'agree' => '请阅读同意隐私政策',

					'title.require' => '名称必须填写',
					'title.unique' => '名称不能重复',
					'sort_number.number' => '必须为整数',
				];
				//自定义规则.上面strong后面必须加冒号
	protected function strong($value,$rule='',$data=''){
					$strong = 0;
					if(preg_match('/\d/',$value)){
						++ $strong;
					}
					if(preg_match('/[a-z]/i',$value)){
						++ $strong;
					}
					if(preg_match('/[\-\_\!\@\#\$\%\^\&\*\(\)\+\=\;\:\?\<\>\.\/\`\~\'\"]/',$value)){
						++ $strong;
					}
					if($strong >=2 ){
						return true;
					}else{
						return false;
					}
				}
				//自定义场景
	protected $scene = [
					'register' => ['name','email','password','repassword','is_newsletter','agree'],
					'brand' => ['title','sort_number']
					];
}
//用法一：
//在需要进行Verify 验证的地方，添加如下代码即可：
//使用助手函数实例化验证器
//$validate = validate('Verify');
//如果需要批量验证，可以使用：
//$result = $validate->batch()->check($data);
//if(!$validate->check($data)){
//dump($validate->getError());
//}

//用法二：控制器调用
// 如果定义了验证器类的话，例如
//控制器中的验证代码可以简化为：
/**
 * @access protected
 * @param array        $data     数据
 * @param string|array $validate 验证器名或者验证规则数组,可带场景
 * @param array        $message  提示信息
 * @param bool         $batch    是否批量验证
 * @param mixed        $callback 回调方法（闭包）
 * @return array|string|true
 * @throws ValidateException
 */
// $result = $this->validate($data,'Verify.register',[],true);
// 				if(true !== $result){
// 					// 验证失败 输出错误信息
// 					$this->error($result);
// 				} 
			
// 用法三: 模型调用

// 如果需要调用的验证器类和当前的模型名称不一致，则可以使用：
// $User = new User;
// // 调用Member验证器类进行数据验证
// $result = $User->validate('Member')->save($data);
// if(false === $result){
// // 验证失败 输出错误信息
// dump($User->getError());
// }
// 同样也可以支持场景验证：
// $User = new User;
// // 调用Member验证器类进行数据验证
// $result = $User->validate('User.edit')->save($data);
// if(false === $result){
// // 验证失败 输出错误信息
// dump($User->getError());
// }


//如果在控制器中写则
//$data = input('post.');
				// 验证规则
				// $rules = [
				// 	'name' =>'require|chsDash|length:6,20',
				// 	'email'=>'require|email',
				// 	'password' =>'require|length:6,20|strong|alphaSpecial',
				// 	'repassword'=>'require|confirm:password',
				// 	'is_newsletter' =>'require|in:0,1',
				// 	'agree' => 'accepted',
				// ];
				// $msg = [
				// 	'name.require' =>'名字必须',
				// 	// 'name.unique'  =>'名字已存在',
				// 	'name.chsDash' =>'只能是汉字、字母、数字和下划线_及破折号-',
				// 	'name.length'  =>'名字字符在6~20之间',

				// 	'email.require'=>'邮箱必须',
				// 	// 'email.unique' =>'邮箱已存在',
				// 	'email'  =>'邮箱格式不正确',

				// 	'password.require' => '密码必须填',
				// 	'password.length'  => '密码必须字符在6~20之间',
				// 	// 'password.alphaDash'=>'密码必须啊啊啊',
				// 	'password.strong' => '密码必须要包含字母、数字特殊符号中两种或两种一种上',
				// 	'repassword.require' => '确认密码必须填',
				// 	'repassword.confirm' => '确认密码必须一致',

				// 	'is_newsletter.require' => '订阅信息可选',
				// 	'is_newsletter.in' => '订阅信息可选',
				// 	'agree' => '请阅读同意隐私政策',
				// ];
				// //调用静态方法实例化对象
				// $validate =Validate::make($rules, $msg);
				// //扩展注册验证规则
				// $validate ->extend('strong', function ($password) {
				// 	$strong = 0;
				// 	if(preg_match('/\d/',$password)){
				// 		++ $strong;
				// 	}
				// 	if(preg_match('/[a-z]/i',$password)){
				// 		++ $strong;
				// 	}
				// 	if(preg_match('/[\-\_\!\@\#\$\%\^\&\*\(\)\+\=\;\:\?\<\>\.\/\`\~\'\"]/',$password)){
				// 		++ $strong;
				// 	}
				// 	if($strong >=2 ){
				// 		return true;
				// 	}else{
				// 		return false;
				// 	}
				// });
				// 批量验证
				// $result = $validate->batch()->check($data);
				// if(!$result){
				// 		// $errors = implode("<br>",$validate->getError());
				// 		$errors = $validate->getError();
				// 		$this->error($errors);
				// 	}