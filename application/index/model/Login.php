<?php
namespace app\index\model;
use \think\Model;

class Login extends Model
{
	protected $table = 'user';
	protected $autoWriteTimestamp="timestamp";
}
