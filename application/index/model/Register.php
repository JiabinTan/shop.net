<?php
namespace app\index\model;
use \think\Model;
class Register extends Model
{
	protected $table = 'user';
	protected $updateTime=false;
	public function setPasswordAttr($value)
	{
		return MD5($value);
	}
}
