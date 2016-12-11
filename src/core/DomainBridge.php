<?php

namespace FTumiwan\DomainCore;

class DomainBridge 
{
	public $register = [];
	
	public function call($domainName,$methodInject,$parameter) {
		foreach ($this->register as $key => $value) {
			$mainMethod = explode("::",$key);
			if ($domainName==$mainMethod[0]) {
				foreach ($value as $_v) {
					$_mi = explode("::",$_v);
					if ($_mi[1]==$methodInject) {
						$cls = 'App\\Domain\\'.$_mi[0].'\\Context';
						return (new $cls())->$methodInject($parameter);
					}
				}
			}
		}
		return false;
	}
}
