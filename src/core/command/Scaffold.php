<?php
namespace FTumiwan\DomainCore\Command;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use DB;

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
		$this->pathTpl = base_path('vendor/ftumiwan/domaincore/src/tpl');
		$this->pathschema = base_path('packages/ftumiwan/schema/'.$domainname);
	}

	public function build() {
		//checking folder if exists
		$this->checkingFolder();
		$this->buildEntity();
		$this->buildContainer();
		$this->makeFactorySignal();
		$this->makeContext();
		$this->checkingBridgeFile();
		$this->makeRoute();
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
			$this->makeMigration($value);
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
		if (!file_exists($this->pathdomain."/Context.php")) {
			file_put_contents($this->pathdomain."/Context.php",$ctx_);
		}		
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

	function makeMigration($objEntity) {
		$table_name = strtolower(str_plural(snake_case($objEntity->title)));
		if (!Schema::hasTable($table_name)) {
			Schema::create($table_name,function(Blueprint $table) use ($objEntity){			
				foreach ($objEntity->properties as $key => $value) {	
					$dvalue = null;
					if ($value->type=='integer' or $value->type=='decimal' or $value->type=='float' or $value->type=='tinyInteger' 
						or $value->type=='double' or $value->type=='increments') {
						$dvalue = 0;
					}
					if ($value->type=='string' or $value->type=='char') {
						$dvalue = '';
					}
					if (!is_null($dvalue)) {
						if ($value->type!=='increments') {
							$table->{$value->type}($key)->default($dvalue);	
						} else {
							$table->{$value->type}($key);
						}						
					} else {
						$table->{$value->type}($key)->nullable();
					}					
				}
				$table->timestamp('created_at')->nullable();	
				$table->timestamp('updated_at')->nullable();	
				if (!empty($objEntity->index)) {
					foreach ($objEntity->index as $key) {
						$table->index($key,$key.'idx');	
					}						
				}					
			});
		} else {
			foreach ($objEntity->properties as $key => $value) {		
				if (Schema::hasColumn($table_name,$key)) {
				} else {
					Schema::table($table_name,function($table) use ($value,$key){
						$dvalue = null;
						if ($value->type=='integer' or $value->type=='decimal' or $value->type=='float' or $value->type=='tinyInteger' 
							or $value->type=='double' or $value->type=='increments') {
							$dvalue = 0;
						}
						if ($value->type=='string' or $value->type=='char') {
							$dvalue = '';
						}
						if (!is_null($dvalue)) {
							if ($value->type!=='increments') {
								$table->{$value->type}($key)->default($dvalue);		
							} else {
								$table->{$value->type}($key);
							}							
						} else {
							$table->{$value->type}($key)->nullable();
						}						
					});					
				}							
			}
			if (!empty($objEntity->index)) {
				Schema::table($table_name,function(Blueprint $table) use($objEntity,$table_name){
					foreach ($objEntity->index as $key) {
						//--check key if exists
						$ptt = 'SHOW KEYS
						        FROM '.$table_name.' 
						        WHERE Key_name= "'.$key.'idx"';
						$keyExists = DB::select(
						    DB::raw(
						        $ptt
						    )
						);
						//--
						if (!empty($keyExists)) {
							$table->dropIndex($key.'idx');	
						}						
						$table->index($key,$key.'idx');	
					}					
				});
			}
		}
		
		return true;
	}

	

  public function contentManipulation($text_file,$data,$flag) { //text_file: text file, data: content inserted, flag: identify begin to end
		$m = "";
		$_content = explode("\n",$text_file);
		$_domainFlagOpen = "//*--- begin ".$flag;
		$_domainFlagClose = "//*--- end ".$flag;
		$new = true;
		$remove = false;
		$content = "";
		while (list($var,$val) = each($_content)) {
			++$var;
			$val = trim($val);		
			if ($val==$_domainFlagOpen) {				
				$remove = true; 
			} else {
				if ($val==$_domainFlagClose) {
					$remove = false;
					continue;
				}
			}
			if ($remove==false) {
				$content .= $val."\n";
			}
		}
		$content .= $_domainFlagOpen."\n".$data."\n".$_domainFlagClose;		
		return $content;
	}


	function makeRoute() {
	    $_route = "
	    Route::post('api/%routename%', function (Request \$request){
			return (new Gate((new App\\Domain\\%domainname%\\Context())))->httpComing(\$request);
		});
	    ";
	    $_domain = str_replace('_','-',snake_case($this->domainname));
	    $_path = base_path('vendor/ftumiwan/domaincore/src');
	    $webroute = file_get_contents($_path."/Routes.php");
	    $r_data = str_replace("%routename%",$_domain,$_route);
	    $r_data_ = str_replace("%domainname%",$this->domainname,$r_data);
	    $routeContent = $this->contentManipulation($webroute,$r_data_,$this->domainname);	   
	    file_put_contents($_path."/Routes.php",$routeContent);
  	}

  	
}