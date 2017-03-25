<?php
namespace app\admin\controller;
use think\Controller;


class Members extends Controller
{
	public function indexAction(){
		$list = db('member')->paginate(5);
		return view('index',['data'=>$list]);
	}
}