<?php
session_start();
class dz_authnum {
	//图片对象、宽度、高度、验证码长度
	private $im;
	private $im_width;
	private $im_height;
	private $len;
	//随机字符串、y轴坐标值、随机颜色
	private $randnum;
	private $randcolor;
	private $randandgle;
	private $randsize;
	//背景色的红绿蓝，默认是浅灰色
	public $red=255;
	public $green=255;
	public $blue=255;
	//字体文件
	private $fontfile = "data/ttf/comic_sans_ms.ttf";
	
	/**
	 * 可选设置：验证码类型、干扰点、干扰线、Y轴随机
	 * 设为 false 表示不启用
	 **/
	//默认是大小写数字混合型，1 2 3 分别表示 小写、大写、数字型
	public $ext_num_type='';
	public $ext_pixel = false; //干扰点
	public $ext_line = false; //干扰线
	public $ext_rand_y= true; //Y轴随机
	function __construct ($len=4,$im_width='',$im_height=25) 
	{
		// 验证码长度、图片宽度、高度是实例化类时必需的数据
		$this->len = $len; //$im_width = $len * 15;
		$this->im_width = $im_width;
		$this->im_height= $im_height;
		$this->im = imagecreate($im_width,$im_height);
		
		//创建随机
		for($i = 0; $i < $this->len; $i++)
		{
			$this->randandgle[$i] = rand(0,50) - 25;
			$this->randsize[$i] = rand(18,23);
		}
	}
	// 设置图片背景颜色，带方块会椭圆
	function set_bgcolor () {
		imagecolorallocate($this->im,$this->red,$this->green,$this->blue);
			
		for($i=2;$i;$i--)
		{
			if(rand(0,1))
			{
				$aplfx = rand($this->im_width/3,$this->im_width*2/3);
				$aplfy = rand($this->im_height/3,$this->im_height*2/3);
				$aplfw = rand(($this->im_width)*2/3,$this->im_width);
				$aplfh = rand(($this->im_height)*2/3,$this->im_height);
				$col = imagecolorallocatealpha($this->im, rand(150,250), rand(150,250), rand(150,250), rand(30,80));
				imagefilledellipse($this->im, $aplfx, $aplfy, $aplfw, $aplfh, $col);
			}else{
				$aplfx = rand(0,$this->im_width/2);
				$aplfy = rand(0,$this->im_height/2);
				$aplfw = rand(($this->im_width)*2/3,$this->im_width);
				$aplfh = rand(($this->im_height)*2/3,$this->im_height);
				$col = imagecolorallocatealpha($this->im, rand(150,250), rand(150,250), rand(150,250), rand(30,80));
				imagefilledrectangle($this->im,$aplfx,$aplfy,$aplfw,$aplfh,$col);
			}
		}
		
	}
	// 获得任意位数的随机码
	function get_randnum () {
		$an1 = 'abcdefghijklmnopqrstuvwxyz';
		$an2 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$an3 = '0123456789';
		$randnum = "";
		if ($this->ext_num_type == '') $str = $an1.$an2.$an3;
		if ($this->ext_num_type == 1) $str = $an1;
		if ($this->ext_num_type == 2) $str = $an2;
		if ($this->ext_num_type == 3) $str = $an3;
		for ($i = 0; $i < $this->len; $i++) {
			$start = rand(1,strlen($str) - 1);
			$randnum .= substr($str,$start,1);
		}
		$this->randnum = $randnum;
		$_SESSION['an'] = strtolower($this->randnum);
	}

	function print_text()
	{
		for($i = 0; $i < $this->len; $i++)
		{
			$this->randcolor[$i] = imagecolorallocatealpha($this->im, rand(0,100), rand(0,100), rand(0,100), rand(5,20));
			imagettftext($this->im, $this->randsize[$i], $this->randandgle[$i], 20 + $i * 20, 40, $this->randcolor[$i], $this->fontfile, substr($this->randnum, $i ,1));
		}
	}
	
	function print_randalp()
	{
		$xbs = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-=';
		for($i = rand(30,40); $i>0; $i--)
		{
			$col = imagecolorallocatealpha($this->im, rand(0,100), rand(0,100), rand(0,100), rand(50,100));
			imagettftext($this->im, rand(5,8), rand(0,100)-50, rand(0,$this->im_width), rand(0,$this->im_height), $col, $this->fontfile, substr($xbs,rand(1,strlen($xbs) - 1),1));
		}		
	}

	function create () {
		$this->set_bgcolor();
		$this->get_randnum ();

		$this->print_text();
		
		$this->print_randalp();

		header("content-type:image/png");
		imagepng($this->im);
		imagedestroy($this->im); //释放图像资源
	}
}//end class

$an = new dz_authnum(6,180,50);
$an->ext_num_type='';
$an->ext_pixel = true; //干扰点
$an->ext_line = true; //干扰线
$an->ext_rand_y= true; //Y轴随机

$an->create();
