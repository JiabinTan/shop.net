<?php
namespace app\index\model;
use \think\Model;

class Goods extends Model
{
	protected $table = 'goods';
	protected $autoWriteTimestamp='timestamp';
	/*public function user()
	{
		$this->belongsTo('User','id');
	}*/
}
