<?php
namespace app\index\validate;
use \think\Validate;
class Register extends Validate
{
    // 当前验证的规则
    protected $rule = [
	'username'=>'require|unique:user',
	
	
	];

	protected function Uphead($value)
	{
		if(preg_match('/^[a-zA-Z]/', $value))
		return true;
		else
		return false;
	}

}
