<?php
namespace app\admin\model;

use think\Model;
class Setting extends Model
{
	public function options()
	{

		return $this->hasMany('SettingOption');
	}


}