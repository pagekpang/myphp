<?php
// 4 size
define ( 'DZ_MIBAO_PREFIX', '1078' );
// cardid:DZ_MIBAO_PREFIX RAND
class dz_mibao_util {
	static function zuhe($num, $get) {
		$total = 1;
		$c = 1;
		for($n = $num; $n > $num - $get; $n --) {
			$total *= $n;
		}
		for($m = $get; $m >= 1; $m --) {
			$c *= $m;
		}
		return $total / $c;
	}
	static function rand_row($num = 8) {
		$basealp = 64;
		if ($num > 26)
			$num = 8;
		$step_len = 26 / $num;
		$ret = '';
		for($i = 0; $i < $num; $i ++) {
			$basealp += rand ( 1, $step_len );
			$ret .= chr ( $basealp );
		}
		return $ret;
	}
	static function rand_cardid() {
		$ret = DZ_MIBAO_PREFIX;
		
		$ret .= rand ( 0, 9 ) . rand ( 0, 9 ) . rand ( 0, 9 ) . rand ( 0, 9 );
		
		$ret .= rand ( 0, 9 ) . rand ( 0, 9 ) . rand ( 0, 9 ) . rand ( 0, 9 );
		
		$ret .= rand ( 0, 9 ) . rand ( 0, 9 ) . rand ( 0, 9 ) . rand ( 0, 9 );
		
		return $ret;
	}
	static function rand_code($row) {
		$code = array ();
		for($i = 0; $i < 8; $i ++) {
			for($j = 0; $j < 8; $j ++) {
				$code [$i] [substr ( $row, $j, 1 )] = rand ( 0, 9 ) . rand ( 0, 9 ) . rand ( 0, 9 );
			}
		}
		return $code;
	}
	static function show($cardid, $row, $code) {
		$height = 332;
		$width = 626;
		$fontfile = "../data/ttf/fs.ttf";
		$textfont = "../data/ttf/comic_sans_ms.ttf";
		$logo = 'pagekpang网';
		$im = imagecreatetruecolor ( $width, $height );
		
		$linecolor = imagecolorallocate ( $im, 229, 229, 229 );
		
		$fontcolor = imagecolorallocate ( $im, 0, 0, 0 );
		
		$top_rectangle_color = imagecolorallocate ( $im, 241, 254, 237 );
		
		$top_letter_color = imagecolorallocate ( $im, 54, 126, 76 );
		
		$left_rectangle_color = imagecolorallocate ( $im, 243, 247, 255 );
		
		$left_num_color = imagecolorallocate ( $im, 4, 68, 192 );
		
		$logo_str_color = imagecolorallocate ( $im, 60, 150, 80 );
		
		imagefill ( $im, 0, 0, imagecolorallocate ( $im, 255, 255, 255 ) );
		
		// logo
		// imagettftext($im,15,0,100,100,$fontcolor,$fontfile,strlen($logo));
		imagettftext ( $im, 12, 0, $width - 12 * strlen ( $logo ), 25, $logo_str_color, $fontfile, $logo );
		
		$x = 20;
		$y = 40; // 序列号位置
		imagettftext ( $im, 15, 0, $x, $y, $fontcolor, $fontfile, '序列号' );
		
		$p = '';
		for($i = 0; $i < 4; $i ++) {
			
			$p .= substr ( $cardid, 3 * $i, 4 ) . ' ';
		}
		imagettftext ( $im, 15, 0, $x + 80, $y, $fontcolor, $fontfile, $p );
		
		imagefilledrectangle ( $im, 10, 80, $width - 10, 50, $top_rectangle_color );
		imagefilledrectangle ( $im, 10, 80, 65, $height - 10, $left_rectangle_color );
		
		// 横线
		imageline ( $im, 10, 50, $width - 10, 50, $linecolor );
		for($i = 0; $i < 8; $i ++) {
			$y = 80 + 30 * $i;
			imageline ( $im, 10, $y, $width - 10, $y, $linecolor );
			imagettftext ( $im, 15, 0, 30, $y + 23, $left_num_color, $textfont, $i + 1 );
		}
		
		for($i = 0; $i < 8; $i ++) {
			$x = 65 + 70 * $i;
			imageline ( $im, $x, 50, $x, $height - 10, $linecolor );
			imagettftext ( $im, 15, 0, $x + 28, 73, $top_letter_color, $textfont, substr ( $row, $i, 1 ) );
		}
		
		for($i = 0; $i < 8; $i ++) {
			for($j = 0; $j < 8; $j ++) {
				$x = 65 + 70 * $i;
				$y = 80 + 30 * $j;
				imagettftext ( $im, 12, 0, $x + 18, $y + 23, $fontcolor, $textfont, $code [$i] [substr ( $row, $j, 1 )] );
			}
		}
		
		imageline ( $im, 10, 10, $width - 10, 10, $linecolor );
		
		imageline ( $im, 10, $height - 10, $width - 10, $height - 10, $linecolor );
		
		imageline ( $im, 10, 10, 10, $height - 10, $linecolor );
		
		imageline ( $im, $width - 10, 10, $width - 10, $height - 10, $linecolor );
		
		ob_clean ();
		header ( "Content-type: image/jpeg" );
		imagejpeg ( $im, null, 100 );
		imagedestroy ( $im );
	}
}