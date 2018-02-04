<?php
namespace app\index\controller;
use \think\Controller;
use \app\index\model\Goods as GoodsModel;
use \app\index\model\User as UserModel;
use \app\index\model\Admin as AdminModel;
class Admin extends Controller
{
	function index($clientIP)
	{     
		$taobaoIP = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$clientIP;
        $IPinfo = json_decode(file_get_contents($taobaoIP));
		dump($IPinfo);
        $province = $IPinfo->data->region;
        $city = $IPinfo->data->city;
        $data = $province.$city;
    }
	function deleteExpire($name,$pass)
	{
		
		$admin_model=new AdminModel();
		$admin=$admin_model->get("name",$name);
		if($admin->password==MD5($pass))
		{
			$good_model=new GoodsModel();
			$state=$good_model->where("expire","<=",date("Y:m:d H:i:s"))->delete();
			if($state)
				return "删除成功！";
			else
				return "删除失败！";
		}
	}
	function createAdmin($name,$pass)
	{
		$para=request()->param();
		$newName=$para['newname'];
		$newPass=$para['newpass'];
		$admin_model=new AdminModel();
		$admin=$admin_model->get("name",$name);
		if($admin->password==MD5($pass))
		{
			$newAdmin=new AdminModel();
			$newAdmin->name=$newName;
			$newAdmin->password=MD5($newPass);
			if($newAdmin->save())
				return "创建成功！";
			else 
				return "创建失败！";
		}
	}
}
