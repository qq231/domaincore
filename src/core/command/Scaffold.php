<?php
namespace FTumiwan\DomainCore\Command;

/**
* 		
*/
class Scaffold
{
	protected $pathschema;
  	protected $domainname;
  	protected $pathdomain;
  	protected $pathTpl;

	function __construct($domainname){
		$this->domainname = $domainname;
		$this->pathdomain = base_path('app/Domain/'.$domainname);
		$this->pathTpl = base_path('packages/ftumiwan/domaincore/src/tpl');
		$this->pathschema = base_path('packages/ftumiwan/domaincore/schema/'.$domainname);
	}

	public function new() {
		//checking folder if exists
		$this->checkingFolder();
		$this->buildEntity();
		$this->buildContainer();
		$this->makeFactorySignal();
		$this->makeContext();
		$this->checkingBridgeFile();
	}

	public function update() {

	}

	function checkingBridgeFile() {
		if (!file_exists(base_path("app/Domain/Bridge.php"))) {
			$bridge = file_get_contents($this->pathTpl."/Bridge.tpl");
	    	file_put_contents(base_path("app/Domain/Bridge.php"),$bridge);
	    }
	}

	function checkingFolder() {
		if (!file_exists($this->pathdomain)) {
	        mkdir($this->pathdomain,0777,true);
	    }
	    if (!file_exists($this->pathdomain.'/Entities')) {
	        mkdir($this->pathdomain.'/Entities',0777,true);
	    }
	    if (!file_exists($this->pathdomain.'/Events')) {
	        mkdir($this->pathdomain.'/Events',0777,true);
	    }
	    if (!file_exists($this->pathdomain.'/Logics')) {
	        mkdir($this->pathdomain.'/Logics',0777,true);
	    }
	    if (!file_exists($this->pathdomain.'/Containers')) {
	        mkdir($this->pathdomain.'/Containers',0777,true);
	    }
	}

	function getEntitiesSchema() {
		$json = file_get_contents($this->pathschema."/entities.json");
		return json_decode($json)->schema;
	}

	function getContainersSchema() {
		$json = file_get_contents($this->pathschema."/containers.json");
		return json_decode($json)->schema;
	}

	function buildEntity() {
		foreach ($this->getEntitiesSchema() as $key => $value) {			
			$this->makeEntity($value);
		}
	}

	function buildContainer() {
		foreach ($this->getContainersSchema() as $key => $value) {
			$this->makeContainer($value);
		}
	}

	function makeContext() {
		$ctx = file_get_contents($this->pathTpl."/Context.tpl");
		$ctx_ = str_replace("%domainname%",$this->domainname,$ctx);
		file_put_contents($this->pathdomain."/Context.php",$ctx_);
	}

	function makeFactorySignal() {
		$fs = file_get_contents($this->pathTpl."/FactorySignal.tpl");
		$fs_ = str_replace("%domainname%",$this->domainname,$fs);
		file_put_contents($this->pathdomain."/Events/FactorySignal.php",$fs_);
	}

	function makeContainer($name) {
		$container = file_get_contents($this->pathTpl."/Container.tpl");
		$container_ = str_replace("%domainname%",$this->domainname,$container);
		$container_2 = str_replace("%containername%",$name->title,$container_);
		$container_3 = str_replace("%containerrelation%",$this->makeRelationContainer($name),$container_2);
		file_put_contents($this->pathdomain."/Containers/".$name->title.".php",$container_3);
	}

	function makeRelationContainer($schema) {
		$fill = "[
			'single'=>[%single%],
			'list'=>[%list%]
		]";
		$_single = "";
		foreach ($schema->single as $value) {
			$_single .= "['".$value[0]."',".$value[1]."],";
		}
		$_single = rtrim($_single,",");
		$_list = "";
		foreach ($schema->list as $value) {
			$_list .= "['".$value[0]."',".$value[1]."],";
		}
		$_list = rtrim($_list,",");
		$fill_ = str_replace("%single%",$_single,$fill);
		$fill_2 = str_replace("%list%",$_list,$fill_);
		return $fill_2;
	}

	function makeEntity($name) {
		$entity = file_get_contents($this->pathTpl."/Entity.tpl");
		$entity_ = str_replace("%domainname%",$this->domainname,$entity);
		$entity_2 = str_replace("%entityname%",$name->title,$entity_);
		$entity_3 = str_replace("%fillable%",$this->makeFillEntity($name->properties),$entity_2);
		$entity_4 = str_replace("%relationship%",$this->makeRelationEntity($name->relationship),$entity_3);
		file_put_contents($this->pathdomain."/Entities/".$name->title.".php",$entity_4);
	}

	function makeFillEntity($schema) {
		$fill = '[';
        foreach ($schema as $k => $v) {
          $fill .= '"'.$k.'",';
        }
        $_fill = rtrim($fill,",");
        $fill = $_fill.']';
        return $fill;
	}

	function makeRelationEntity($relationship) {
		$tpl = 
		"public function %rlname%() {
		return \$this->%rltype%('%rlentity%');
	}\n";
		$relation = "";
		foreach ($relationship as $value) {
			$k = explode(".",$value);
			$rlname = lcfirst($k[1]);
			$pkgname = 'App\\Domain\\'.$this->domainname.'\\Entities\\'.$k[1];
			$tpl_ = $tpl;
			$tpl_2 = str_replace("%rlname%",$rlname,$tpl_);
			$tpl_3 = str_replace("%rltype%",$k[0],$tpl_2);
			$tpl_4 = str_replace("%rlentity%",$pkgname,$tpl_3);
			$relation .= $tpl_4;
		}
		return $relation;
	}



	


}