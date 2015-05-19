<?php 
abstract class Debug {
	public static $on = false;
	private static $deep = 0;

	public static function checkPoint($text,$parents=0) {
		if(self::$on && (self::$deep>=$parents || self::$deep==-1)) {
			$ml = $parents * 20;
			echo '<p style="font-size: 14px; margin-left:'.$ml.'px">'.$text.'</p>';
		}
	}

	public static function on($on=true, $deep=0) {
		self::$on = $on;
		self::$deep = $deep;
	}
}
?>