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
}
