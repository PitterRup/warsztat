<?php
abstract class Errors {
	private static $_errors = array();
	public static $showLayout = true;

	public function page($code) {
        $content = file_get_contents(PATH.'/library/cms/errors/'.$code.'.html');
        echo $content;
        exit;
    }

    public function add($e) {
    	$deep = 0;
    	$msg = Errors::extract($e->getMessage(),$deep);
    	$arr = $e->getTrace();
		self::$_errors[] = '<strong>Przechwycony wyjątek:</strong> '.$msg.' (<i>'.$e->getFile().'</i> w lini <b>'.$e->getLine().'</b>) przewidywana lokalizacja błędu: <i>'.$arr[$deep]['file'].'</i> w lini <b>'.$arr[$deep]['line'].'</b>';
    }

    public function get() {
		foreach(self::$_errors as $error) echo '<p>'.$error.'</p>';
	}	

	public function count() {
		return count(self::$_errors);
	}

	private function extract($text,&$deep) {
		if(preg_match('/^deep\[[0-9]+\]/', $text, $arr)) {
			$msg = preg_replace('/^deep\[[0-9]+\]:/',"",$text);
			preg_match('/[0-9]+/', $arr[0],$arr);
			$deep = $arr[0];
		}
		else $msg = $text;
		return $msg;
	}
}
?>