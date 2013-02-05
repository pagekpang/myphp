<?php
session_start();

Class GIFEncoder {
	var $GIF = "GIF89a";                /* GIF header 6 bytes        */
	var $VER = "GIFEncoder V2.06";        /* Encoder version                */

	var $BUF = Array ( );
	var $LOP =  0;
	var $DIS =  2;
	var $COL = -1;
	var $IMG = -1;

	var $ERR = Array (
			'ERR00' =>"Does not supported function for only one image!",
			'ERR01' =>"Source is not a GIF image!",
			'ERR02' =>"Unintelligible flag ",
			'ERR03' =>"Could not make animation from animated GIF source",
	);

	/*
	 :::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::        GIFEncoder...
	::
	*/
	function GIFEncoder        (
			$GIF_src, $GIF_dly, $GIF_lop, $GIF_dis,
			$GIF_red, $GIF_grn, $GIF_blu, $GIF_mod
	) {
		if ( ! is_array ( $GIF_src ) && ! is_array ( $GIF_tim ) ) {
			printf        ( "%s: %s", $this->VER, $this->ERR [ 'ERR00' ] );
			exit        ( 0 );
		}
		$this->LOP = ( $GIF_lop > -1 ) ? $GIF_lop : 0;
		$this->DIS = ( $GIF_dis > -1 ) ? ( ( $GIF_dis < 3 ) ? $GIF_dis : 3 ) : 2;
		$this->COL = ( $GIF_red > -1 && $GIF_grn > -1 && $GIF_blu > -1 ) ?
		( $GIF_red | ( $GIF_grn << 8 ) | ( $GIF_blu << 16 ) ) : -1;

		for ( $i = 0; $i < count ( $GIF_src ); $i++ ) {
			if ( strToLower ( $GIF_mod ) == "url" ) {
				$this->BUF [ ] = fread ( fopen ( $GIF_src [ $i ], "rb" ), filesize ( $GIF_src [ $i ] ) );
			}
			else if ( strToLower ( $GIF_mod ) == "bin" ) {
				$this->BUF [ ] = $GIF_src [ $i ];
			}
			else {
				printf        ( "%s: %s ( %s )!", $this->VER, $this->ERR [ 'ERR02' ], $GIF_mod );
				exit        ( 0 );
			}
			if ( substr ( $this->BUF [ $i ], 0, 6 ) != "GIF87a" && substr ( $this->BUF [ $i ], 0, 6 ) != "GIF89a" ) {
				printf        ( "%s: %d %s", $this->VER, $i, $this->ERR [ 'ERR01' ] );
				exit        ( 0 );
			}
			for ( $j = ( 13 + 3 * ( 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 ) ) ), $k = TRUE; $k; $j++ ) {
				switch ( $this->BUF [ $i ] { $j } ) {
					case "!":
						if ( ( substr ( $this->BUF [ $i ], ( $j + 3 ), 8 ) ) == "NETSCAPE" ) {
							printf        ( "%s: %s ( %s source )!", $this->VER, $this->ERR [ 'ERR03' ], ( $i + 1 ) );
							exit        ( 0 );
						}
						break;
					case ";":
						$k = FALSE;
						break;
				}
			}
		}
		GIFEncoder::GIFAddHeader ( );
		for ( $i = 0; $i < count ( $this->BUF ); $i++ ) {
			GIFEncoder::GIFAddFrames ( $i, $GIF_dly [ $i ] );
		}
		GIFEncoder::GIFAddFooter ( );
	}
	/*
	 :::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::        GIFAddHeader...
	::
	*/
	function GIFAddHeader ( ) {
		$cmap = 0;

		if ( ord ( $this->BUF [ 0 ] { 10 } ) & 0x80 ) {
			$cmap = 3 * ( 2 << ( ord ( $this->BUF [ 0 ] { 10 } ) & 0x07 ) );

			$this->GIF .= substr ( $this->BUF [ 0 ], 6, 7                );
			$this->GIF .= substr ( $this->BUF [ 0 ], 13, $cmap        );
			$this->GIF .= "!\377\13NETSCAPE2.0\3\1" . GIFEncoder::GIFWord ( $this->LOP ) . "\0";
		}
	}
	/*
	 :::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::        GIFAddFrames...
	::
	*/
	function GIFAddFrames ( $i, $d ) {

		$Locals_str = 13 + 3 * ( 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 ) );

		$Locals_end = strlen ( $this->BUF [ $i ] ) - $Locals_str - 1;
		$Locals_tmp = substr ( $this->BUF [ $i ], $Locals_str, $Locals_end );

		$Global_len = 2 << ( ord ( $this->BUF [ 0  ] { 10 } ) & 0x07 );
		$Locals_len = 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 );

		$Global_rgb = substr ( $this->BUF [ 0  ], 13,
				3 * ( 2 << ( ord ( $this->BUF [ 0  ] { 10 } ) & 0x07 ) ) );
		$Locals_rgb = substr ( $this->BUF [ $i ], 13,
				3 * ( 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 ) ) );

		$Locals_ext = "!\xF9\x04" . chr ( ( $this->DIS << 2 ) + 0 ) .
		chr ( ( $d >> 0 ) & 0xFF ) . chr ( ( $d >> 8 ) & 0xFF ) . "\x0\x0";

		if ( $this->COL > -1 && ord ( $this->BUF [ $i ] { 10 } ) & 0x80 ) {
			for ( $j = 0; $j < ( 2 << ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 ) ); $j++ ) {
				if        (
						ord ( $Locals_rgb { 3 * $j + 0 } ) == ( $this->COL >>  0 ) & 0xFF &&
						ord ( $Locals_rgb { 3 * $j + 1 } ) == ( $this->COL >>  8 ) & 0xFF &&
						ord ( $Locals_rgb { 3 * $j + 2 } ) == ( $this->COL >> 16 ) & 0xFF
				) {
					$Locals_ext = "!\xF9\x04" . chr ( ( $this->DIS << 2 ) + 1 ) .
					chr ( ( $d >> 0 ) & 0xFF ) . chr ( ( $d >> 8 ) & 0xFF ) . chr ( $j ) . "\x0";
					break;
				}
			}
		}
		switch ( $Locals_tmp { 0 } ) {
			case "!":
				$Locals_img = substr ( $Locals_tmp, 8, 10 );
				$Locals_tmp = substr ( $Locals_tmp, 18, strlen ( $Locals_tmp ) - 18 );
				break;
			case ",":
				$Locals_img = substr ( $Locals_tmp, 0, 10 );
				$Locals_tmp = substr ( $Locals_tmp, 10, strlen ( $Locals_tmp ) - 10 );
				break;
		}
		if ( ord ( $this->BUF [ $i ] { 10 } ) & 0x80 && $this->IMG > -1 ) {
			if ( $Global_len == $Locals_len ) {
				if ( GIFEncoder::GIFBlockCompare ( $Global_rgb, $Locals_rgb, $Global_len ) ) {
					$this->GIF .= ( $Locals_ext . $Locals_img . $Locals_tmp );
				}
				else {
					$byte  = ord ( $Locals_img { 9 } );
					$byte |= 0x80;
					$byte &= 0xF8;
					$byte |= ( ord ( $this->BUF [ 0 ] { 10 } ) & 0x07 );
					$Locals_img { 9 } = chr ( $byte );
					$this->GIF .= ( $Locals_ext . $Locals_img . $Locals_rgb . $Locals_tmp );
				}
			}
			else {
				$byte  = ord ( $Locals_img { 9 } );
				$byte |= 0x80;
				$byte &= 0xF8;
				$byte |= ( ord ( $this->BUF [ $i ] { 10 } ) & 0x07 );
				$Locals_img { 9 } = chr ( $byte );
				$this->GIF .= ( $Locals_ext . $Locals_img . $Locals_rgb . $Locals_tmp );
			}
		}
		else {
			$this->GIF .= ( $Locals_ext . $Locals_img . $Locals_tmp );
		}
		$this->IMG  = 1;
	}
	/*
	 :::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::        GIFAddFooter...
	::
	*/
	function GIFAddFooter ( ) {
		$this->GIF .= ";";
	}
	/*
	 :::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::        GIFBlockCompare...
	::
	*/
	function GIFBlockCompare ( $GlobalBlock, $LocalBlock, $Len ) {

		for ( $i = 0; $i < $Len; $i++ ) {
			if        (
					$GlobalBlock { 3 * $i + 0 } != $LocalBlock { 3 * $i + 0 } ||
					$GlobalBlock { 3 * $i + 1 } != $LocalBlock { 3 * $i + 1 } ||
					$GlobalBlock { 3 * $i + 2 } != $LocalBlock { 3 * $i + 2 }
			) {
				return ( 0 );
			}
		}

		return ( 1 );
	}
	/*
	 :::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::        GIFWord...
	::
	*/
	function GIFWord ( $int ) {

		return ( chr ( $int & 0xFF ) . chr ( ( $int >> 8 ) & 0xFF ) );
	}
	/*
	 :::::::::::::::::::::::::::::::::::::::::::::::::::
	::
	::        GetAnimation...
	::
	*/
	function GetAnimation ( ) {
		return ( $this->GIF );
	}
}

class dz_authnum {

	public $width = 120;
	public $height = 50;

	public $ansize = 4;
	public $antype = '';
	public $ansesstion = 'an';
	
	public $ananglemax = 10;
	
	
	private $randset;
	private $randnum;
	private $randcolor;
	private $randangle;
	private $randsize;
	
	private $fontfile = "data/ttf/comic_sans_ms.ttf";
	
	private function pre_create(){
		if($this->width<0) $this->width = 120;
		if($this->height<0) $this->height = 50;
		
		if($this->antype<0) $this->$antype = 4;
		
		$an1 = 'abcdefghijklmnopqrstuvwxyz';
		$an2 = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$an3 = '0123456789';
		$this->randset = "";
		if ($this->antype == '') $this->randset = $an1.$an2.$an3;
		if ($this->antype == 1) $this->randset = $an1;
		if ($this->antype == 2) $this->randset = $an2;
		if ($this->antype == 3) $this->randset = $an3;
		
		$this->get_randnum();
		
		//for font color
		for($i = 0; $i < $this->ansize; $i++)
		{
			$this->randcolor[$i]['r'] = rand(0, 100);
			$this->randcolor[$i]['g'] = rand(0, 100);
			$this->randcolor[$i]['b'] = rand(0, 100);
			$this->randcolor[$i]['a'] = rand(50, 100);
			$this->randangle[$i] = rand(0,20) ;
			$this->randsize[$i] = rand(20,25);
		}
	}
	
	private function get_randnum () {
		$randnum = '';
		for ($i = 0; $i < $this->ansize; $i++) {
			$start = rand(1,strlen($this->randset) - 1);
			$randnum .= substr($this->randset,$start,1);
		}
		$this->randnum = $randnum;
		$_SESSION[$this->ansesstion] = strtolower($this->randnum);
	}
	
	private function print_text($im,$angle)
	{
		for($i = 0; $i < $this->ansize; $i++)
		{
			$acolor = imagecolorallocatealpha($im, $this->randcolor[$i]['r'],$this->randcolor[$i]['g'], $this->randcolor[$i]['b'],$this->randcolor[$i]['a']);
			imagettftext($im, $this->randsize[$i], $this->randangle[$i] + $angle, 5 + $i * 30, $this->height/2 + 5, $acolor, $this->fontfile, substr($this->randnum, $i ,1));
		}
	}
	
	function create(){
		$imagedata = array();
		$iamgedelay = array();
		$this->pre_create();
		$allcount = 80;
		for($i = -($allcount/2); $i < ($allcount/2); $i+=3)
		{
			$iamgedelay[$i] = 1;
			$image = imagecreate($this->width,$this->height);
			imagecolorallocate($image, 0,0,0); // bg
			if($i>0)
			{
				$this->print_text($image, - $i);
			}else{
				$this->print_text($image,$i);
			}
			
			imagegif($image);
			imagedestroy($image);
			$imagedata[] = ob_get_contents();
			ob_clean();
		}
		
		Header ('Content-type:image/gif');
		$git = new GIFEncoder($imagedata, 100, 0, 2, 0, 0, 0, 'bin');
		echo $git->GetAnimation();
		
	}
	
	
	
}//end class

$an = new dz_authnum(4,120,50);

$an->create();
//echo $_SESSION['an'];
