<?php
namespace app\index\controller;
use \app\index\model\Goods as GoodsModel;
use \app\index\model\User as UserModel;
class Test
{
	function index($tag)
	{
		$para=Request::instance()->param();
		$user=new GoodsModel();
		$good1=$user->where('expire','>=',date('Y-m-d h:i:s'))->select();
		echo $user->getLastSql();
		echo "<br/>";
		$good2=$user->whereOr(
		['tag1'=>$tag,
		'tag2'=>$tag,
		'tag3'=>$tag,
		])->select();
		echo "good2......";
		echo "<br/>";
		echo $user->getLastSql();
		echo "<br/>";
		$good3=$user->where('expire','>=',date('Y-m-d h:i:s'))->whereOr(
		['tag1'=>$tag,
		'tag2'=>$tag,
		'tag3'=>$tag,
		])->select();
		echo "good3.....";
		echo "<br/>";
		echo $user->getLastSql();
		echo "<br/>";
		$good4=$user->whereOr(
		['tag1'=>$tag,
		'tag2'=>$tag,
		'tag3'=>$tag,
		])->where('expire','>=',date('Y-m-d h:i:s'))->select();
		echo " good4.....";
		echo "<br/>";
		echo $user->getLastSql();
		echo "<br/>";
	}
}
