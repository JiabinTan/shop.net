<?php
namespace app\index\model;
use \think\Model;
class Register extends Model
{
	protected $table = 'user';
	protected $autoWriteTimestamp='timestamp';
	protected $updateTime=false;
	public function setPasswordAttr($value)
	{
		return MD5($value);
	}
}
