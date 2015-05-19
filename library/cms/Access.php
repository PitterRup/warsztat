<?php
abstract class Access {
	private static $permission;

	public function isProtectedDir() {
		$protected = $this->_config->protected->dirs;
		if(array_key_exists($this->_request->directoryPath(), $protected)) {
			$a = $this->_request->directoryPath();
			self::$permission = $protected->$a;
			return true;
		}
		else return false;
	}

	public function isProtectedController() {
		$protected = $this->_config->protected->controllers;
		if(array_key_exists($this->_request->directoryPath(), $protected)) {
			$a = $this->_request->directoryPath();
			if(array_key_exists($this->_request->controllerName(), $protected->$a)) {
				$b = $this->_request->controllerName();
				self::$permission = $protected->$a->$b;
				return true;
			}
			else return false;
		}
		else return false;
	}

	public function isProtectedAction() {
		$protected = $this->_config->protected->actions;
		if(array_key_exists($this->_request->directoryPath(), $protected)) {
			$a = $this->_request->directoryPath();
			if(array_key_exists($this->_request->controllerName(), $protected->$a)) {
				$b = $this->_request->controllerName();
				if(array_key_exists($this->_request->actionName(), $protected->$a->$b)) {
					$c = $this->_request->actionName();
					self::$permission = $protected->$a->$b->$c;
					return true;
				}
				else return false;
			}
			else return false;
		}
		else return false;
	}

	public function getPermission() {
		return self::$permission;
	}
}
?>