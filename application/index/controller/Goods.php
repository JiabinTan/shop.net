<?php
namespace app\index\controller;
use \think\Controller;
use \think\Session;
use \think\Cookie;
use \think\Request;
use \app\index\model\Goods as GoodsModel;
class Goods extends Controller
{
    public function index()
    {
    	$para=Request::instance()->param();
    	$start=$para['items'];
    		
        $model=new GoodsModel();
        $good=$model->where('expire','>=',date('Y-m-d h:i:s'))->limit($start,50)->order('id','asc')->field('id,name,path,price,description,likes')->select();
        $count=count($good);
       for($i=0;$i < $count;$i++)
       {
       		$goods[$i]=$good[$i]->toJson();
       }
       
        return	json(['status'=>'ok','count'=>$count,'goods'=>$goods,'isAll'=>$count<50]);
    }
    public function detail()
    {
    		$para=Request::instance()->param();
    		$id=$para['id'];
    		
        $model=new GoodsModel();
        $detail=$model->find($id);
        if($detail->count()==0)
       		 return json(['status'=>'fail']);
       	else{
        	return	json(['status'=>'ok','goods'=>$detail->toJson()]);
      	}
    }
	public function search()
    {

		$para=Request::instance()->param();
    	$start=$para['items'];
		$tag=$para['tag'];
    	if(!$tag)
		{
			return json(["status"=>"fail"]);
		}
        $model=new GoodsModel();
        $good=$model->whereOr(
		['tag1'=>$tag,
		'tag2'=>$tag,
		'tag3'=>$tag,
		])->where('expire','>=',date('Y-m-d h:i:s'))->limit($start,50)->order('id','asc')->field('id,name,path,price,description,likes')->select();
        $count=count($good);
       for($i=0;$i < $count;$i++)
       {
       		$goods[$i]=$good[$i]->toJson();
       }
       
        return	json(['status'=>'ok','count'=>$count,'goods'=>$goods,'isAll'=>$count<50]);
	}
	public function sort()
    {

		$para=Request::instance()->param();
		$start=$para['count'];
    	$state=$para['status'];//1降序，2升序
		$tag=$para['tag'];//0代表热度，1代表价格
    	if($tag!='1'&&$tag!='0')
		{
			return json(["status"=>"fail"]);
		}
        $model=new GoodsModel();
		if(0==$tag&&1==$state)
			$good=$model->where('expire','>=',date('Y-m-d h:i:s'))->order('likes','asc')->limit($start,50)->field('id,name,path,price,description,likes')->select();
		else if('0'==$tag&&'2'==$state)
			$good=$model->where('expire','>=',date('Y-m-d h:i:s'))->order('likes','desc')->limit($start,50)->field('id,name,path,price,description,likes')->select();
		else if(1==$tag&&1==$state)
			$good=$model->where('expire','>=',date('Y-m-d h:i:s'))->order('price','asc')->limit($start,50)->field('id,name,path,price,description,likes')->select();
		else if(1==$tag&&2==$state)
			$good=$model->where('expire','>=',date('Y-m-d h:i:s'))->order('price','desc')->limit($start,50)->field('id,name,path,price,description,likes')->select();
        $count=count($good);
       for($i=0;$i < $count;$i++)
       {
       		$goods[$i]=$good[$i]->toJson();
       }
       
        return	json(['status'=>'ok','count'=>$count,'goods'=>$goods,'isAll'=>$count<50]);
	}
}
