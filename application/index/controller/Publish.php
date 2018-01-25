<?php
namespace app\index\controller;
use \think\Controller;
use \think\Session;
use \think\Cookie;
use \app\index\model\Goods as GoodsModel;
use \app\index\model\User as UserModel;
use \app\index\validate\Publish as InfoValidate;
class Publish extends Controller
{
	public function check(){
	$session_id=Session::get('id','personinfo');
	$session_username=Session::get('username','personinfo');
	if($session_id||$session_username)
		return json(['status'=>'ok']);
	else {
		 	return json(['status'=>'fail']);
		}
	}
	public function index()
	{
		return $this->fetch();
	}
	public function receive()
	{
		$id=Session::get('id','personinfo');
		$username=Session::get('username','personinfo');
		if(!$id||!$username)
		{
			return json(['status'=>'fail','messege'=>"登录存在问题，请重试",'url'=>"\\"]);
		}
		$good=new GoodsModel();
		$count=$good->where("owner_id",$id)->count();
		if(count($count)>=5)
			return json(['status'=>'fail','messege'=>"发布数量已达上限，无法再发布",'url'=>"\\"]);
		$request=request();
		$images = $request->file()['fileList'];
		$count=count($images);
		if($count>4)
			$count=4;
		$path="";
		for($i=0;$i<$count;$i++)
		{
			//部署时候需要改变位置
			$imgInfo=$images[$i]->move("D:\dev\shop.net\public\uploads");
			if(false==$imgInfo)
				return json(['status'=>'fail','messege'=>"商品图片存储存在问题，请重试",'url'=>"\\"]);
			$path=$path . ";" . "\\uploads\\" . $imgInfo->getSaveName();
		}
		
		
		
		//$image 格式如下
		/*["name"] =&gt; array(2) {
        [0] =&gt; string(48) "175320040617e4d27a06gy1fgr2fntuvfj20c7103gox.jpg"
        [1] =&gt; string(48) "175420040609e4d27a06gy1fgr2fpj8ruj20c812q0wg.jpg"
      }
      ["type"] =&gt; array(2) {
        [0] =&gt; string(10) "image/jpeg"
        [1] =&gt; string(10) "image/jpeg"
      }
      ["tmp_name"] =&gt; array(2) {
        [0] =&gt; string(27) "C:\Windows\Temp\phpBFA4.tmp"
        [1] =&gt; string(27) "C:\Windows\Temp\phpBFA5.tmp"
      }
      ["error"] =&gt; array(2) {
        [0] =&gt; int(0)
        [1] =&gt; int(0)
      }
      ["size"] =&gt; array(2) {
        [0] =&gt; int(59161)
        [1] =&gt; int(68900)
      }*/
		//dump($request);
		$info=json_decode($request->param()['goodInfo']);
		$cate=$info->cate;
		$cate=mb_eregi_replace('\；',";",$cate);
		$tags=explode(';',$cate,3);
		if(empty($tags))
		{
			
			$status='fail';
			$messege="商品标签必须";
			return json(['status'=>$status,'messege'=>$messege,'url'=>"\\"]);
			
		}
		
		$good->name=$info->name;
		if(!empty($tags[0]))
			$good->tag1=$tags[0];
		if(!empty($tags[1]))
			$good->tag2=$tags[1];
		if(!empty($tags[2]))
			$good->tag3=$tags[2];
		$good->expire=date('Y-m-d H:i:s',time()+14*24*3600);
		$good->likes=0;
		$good->owner=$username;
		$good->description=$info->desc;
		$good->price=$info->price;
		$good->owner_id=$id;
		$good->path=$path;
		$validate=new InfoValidate();
		$result=$validate->check($good);
		if(true!=$result)
		{
			$status='fail';
			$messege=$validate->getError();
		}
		else{
			$status='ok';
			$messege="上传完成";
			$url=url("index/publish/jump");
			header("Location:$url");
		}
		if(false==$good->save())
		{
			$status='fail';
			$messege="数据写入出错，请联系网站管理员";
		}
		return json(['status'=>$status,'messege'=>$messege ,"url"=>"\\"]);
		//$info = $image->move(ROOT_PATH . 'public/'  . 'uploads');
		//return json([request()->param()]);
		
	}
	public function jump()
	{
		return $this->fetch();
	}
}

