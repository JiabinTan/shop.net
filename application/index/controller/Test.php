<?php
namespace app\index\controller;
use \app\index\model\Goods as GoodsModel;
use \app\index\model\User as UserModel;
class Test
{

/**
     * 将unicode转换成字符
     * @param int $unicode
     * @return string UTF-8字符
     **/
    function unicode2Char($unicode){
        if($unicode < 128)     return chr($unicode);
        if($unicode < 2048)    return chr(($unicode >> 6) + 192) .
                                      chr(($unicode & 63) + 128);
        if($unicode < 65536)   return chr(($unicode >> 12) + 224) .
                                      chr((($unicode >> 6) & 63) + 128) .
                                      chr(($unicode & 63) + 128);
        if($unicode < 2097152) return chr(($unicode >> 18) + 240) .
                                      chr((($unicode >> 12) & 63) + 128) .
                                      chr((($unicode >> 6) & 63) + 128) .
                                      chr(($unicode & 63) + 128);
        return false;
    }
 
    /**
     * 将字符转换成unicode
     * @param string $char 必须是UTF-8字符
     * @return int
     **/
    function char2Unicode($char){
        switch (strlen($char)){
            case 1 : return ord($char);
            case 2 : return (ord($char{1}) & 63) |
                            ((ord($char{0}) & 31) << 6);
            case 3 : return (ord($char{2}) & 63) |
                            ((ord($char{1}) & 63) << 6) |
                            ((ord($char{0}) & 15) << 12);
            case 4 : return (ord($char{3}) & 63) |
                            ((ord($char{2}) & 63) << 6) |
                            ((ord($char{1}) & 63) << 12) |
                            ((ord($char{0}) & 7)  << 18);
            default :
                trigger_error('Character is not UTF-8!', E_USER_WARNING);
                return false;
        }
    }
	function index()
	{
		/*return preg_replace_callback('/[\x{3000}\x{ff01}-\x{ff5f}]/',function($matches)
		{
			($unicode=char2Unicode($matches[0])) == 0x3000 ? " " : (($code=$unicode-0xfee0) > 256 ? unicode2Char($code) : chr($code));

		},$str);
		*/
		$str="asd；d十大；不啊好低啊";

		echo urlencode("年后");
		echo "<br/>";
		echo utf8_decode("\你");
		//preg_replace_callback('//');
	}
}
