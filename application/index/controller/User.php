<?php
namespace app\index\controller;

use think\Controller;
use think\Validate;
use app\index\model\Member;
use think\Request;

class User extends Controller
{
	public function indexAction(){
		return view('index',['name'=>'aaa','age'=>18]);
	}

	public function registerAction(){
			if(request()->isPost()){
				$data = input('post.');
				
				$result = $this->validate($data,'Verify.register',[],true);
				if(true !== $result){
					return view('register',['errors'=>$result]);
					// 验证失败 输出错误信息
					// $this->error($result);
				}
				//过滤数据
				$data = request()->except(['agree','repassword']);
				$data['password'] = input('post.password','','md5');
				
				$res = Member::create($data);
				if($res){
					$this->success('注册成功，请激活','/index');
				}
			}else{
				return view('register');
			}
	
	}
	public function ajax(){
		$email = input('post.email');

		$member = Member::get(['email'=>$email]);
		
	}
	
	public function loginAction(){
		if(request()->isPost()){
			$captcha = input('post.captcha');
			$email = input('post.email');
			$telephone = input('post.email');
			$password= input('post.password','','md5');

			if(captcha_check($captcha)){
				// $res = Member::getByEmail($email);$res['password']
				$res = Member::where('email',$email)->whereOr('telephone',$telephone)->select();	
				if(!$res){
					$this->error('用户名不存在');
				}
				
				if($res[0]->password!=$password){
					$this->error('密码不正确');
				}
				session('name',$res[0]->name);
				$this->redirect('/index');
			}else{
				$this->error('验证码不正确，请重新输入');
			}

		}else{
			return view();
		}
		
	}
}