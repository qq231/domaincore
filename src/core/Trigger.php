<?php
namespace FTumiwan\DomainCore;

/**
 *
 */
class Trigger
{
	protected $domain;
	function __construct($domain) {
		$this->domain = $domain;
	}
	public function run($command,$entity,$resultAction,$value) {
		$file = "app/Domain/".$this->domain."/Events/FactorySignal.php";		
		if (file_exists($file)) {
			$cls = "App\\Domain\\".$this->domain."\\Events\\FactorySignal";
			$factorySignal = new $cls();
			$methodName = "on".$entity.ucfirst($command);
			if (method_exists($factorySignal,$methodName)) {
				$factorySignal->$methodName($resultAction,$value);
			}
		}
	}
}