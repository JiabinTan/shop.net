<?php
namespace app\index\controller;
use \think\Controller;
use \think\Session;
use \think\Cookie;
use \app\index\model\User as UserModel;
use \app\index\model\Goods as GoodsModel;
class Userinfo extends Controller
{
    public function index()
    {
        return $this->fetch("userinfo");
    }
	public function getInfo(){
		$user=new UserModel();
		$goods=new GoodsModel();
		$session_id=Session::get('id','personinfo');
		$session_username=Session::get('username','personinfo');
		if(!($session_id&&$session_username))
			return json(["status"=>"fail","messege"=>"您的登录存在问题","id"=>$session_id,"user"=>$session_username]);
		$userinfo=$user->field("username,email,contact,school,headshot,ip,likes,purchased")->find($session_id);
		if(!$userinfo)
			return json(["status"=>"fail","messege"=>"您的登录存在问题,无法获得对应信息"]);
		$likes=explode(";",$userinfo->likes);
		$purchase=explode(";",$userinfo->purchased);

		
		$likes_count=count($likes);
		$purchase_count=count($purchase);
		for($i=1;$i<$likes_count;$i++)
		{
			$likesinfo[$i-1]=$goods->field("name,owner,price")->find($likes[$i]);
		}
		for($i=1;$i<$purchase_count;$i++)
		{
			$purchaseinfo[$i-1]=$goods->field("name,owner,price")->find($purchase[$i]);
		}
		
		$goodinfo=$goods->where("owner_id",$session_id)->field("name,expire,price")->select();
		$data=["userinfo"=>$userinfo->toJson(),"goodsinfo"=>json_encode($goodinfo),"likesinfo"=>json_encode($likesinfo),"purchaseinfo"=>json_encode($purchaseinfo)];
		return json(['status'=>"ok","data"=>$data]);
	}

}
