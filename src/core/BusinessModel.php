<?php
namespace FTumiwan\DomainCore;
use FTumiwan\DomainCore\Factory;
use FTumiwan\DomainCore\Trigger;

class BusinessModel implements iBm
{
	public $factory;
	//** container is a entity configuration for business model presentation
	// example config:  ['single'=>[['Beli',0],['RekapBeli',0]],'list'=>[['BeliDetail',0]]]
	//         0:read/write, 1:read only
	public $container;
	protected $triggerBm;
	protected $implementObject;
	function __construct($domain) {
		$this->factory = new Factory($domain);
		$this->triggerBm = new Trigger($domain);
		$this->implementObject = $this->getImplement();
	}
	function getImplement() {
		$cls = explode("\\",get_class($this));
		return $cls[4];
	}
	public function store($value) {
		if (empty($this->container)) {
			return false;
		} else {
			$container = $this->getElementContainer();
			$base = $container[0]; //get first element for base			
			$parent_id = strtolower(snake_case($base))."_id";
			$_id = $this->factory->execute('store',$base,$value[$base]);
			foreach ($this->container['single'] as $v) {
				if ($v[0]!==$base) { //skip if is base element
					$entity = $v[0];
					if ($v[1]==0) { //can read/write
						$value[$entity][$parent_id] = $_id;
						$this->factory->execute('store',$entity,$value[$entity]);
					}
				}				
			}
			foreach ($this->container['list'] as $v) {
				$entity = $v[0];
				if ($v[1]==0) { //can read/write
					foreach ($value[$entity] as $key => $val) {
						$val[$parent_id] = $_id;
						$this->factory->execute('store',$entity,$val);
					}
				}
			}
			$this->triggerBm->run('store',$this->implementObject,true,$value);		
		}
	}
	public function find($value) {
		if (empty($this->container)) {
			return false;
		} else {
			$container = $this->getElementContainer();
			$base = $container[0];
			array_splice($container,0,1); //remove first element;
			for ($i=0; $i < count($container) ; $i++) { 
				$container[$i] = lcfirst($container[$i]);
			}			
			$hs = $this->factory->execute('findWith',$base,['id'=>$value,'with'=>$container])[0]; //get first element
			$this->triggerBm->run('find',$this->implementObject,$hs,$value);
			return $hs;
		}
	}
	public function loadAll($value) {
		if (empty($this->container)) {
			return false;
		} else {
			$container = $this->getElementContainer();
			$base = $container[0];
			array_splice($container,0,1); //remove first element;
			for ($i=0; $i < count($container) ; $i++) { 
				$container[$i] = lcfirst($container[$i]);
			}
			$value['with'] = $container;
			//$hs = $this->factory->execute('loadAll',$base,['pr'=>$value,'with'=>$container]);
			$hs = $this->factory->execute('loadAll',$base,$value);
			$this->triggerBm->run('loadAll',$this->implementObject,$hs,$value);
			return $hs;
		}
	}
	public function queryAnd($value) {
		if (empty($this->container)) {
			return false;
		} else {
			$container = $this->getElementContainer();
			$base = $container[0];
			array_splice($container,0,1); //remove first element;
			for ($i=0; $i < count($container) ; $i++) { 
				$container[$i] = lcfirst($container[$i]);
			}
			$hs = $this->factory->execute('queryAndContainer',$base,['pr'=>$value,'with'=>$container]);
			$this->triggerBm->run('queryAnd',$this->implementObject,$hs,$value);
			return $hs;
		}
	}
	public function search($value) {
		if (empty($this->container)) {
			return false;
		} else {
			$container = $this->getElementContainer();
			$base = $container[0];
			array_splice($container,0,1); //remove first element;
			for ($i=0; $i < count($container) ; $i++) { 
				$container[$i] = lcfirst($container[$i]);
			}
			$hs = $this->factory->execute('searchContainer',$base,['pr'=>$value,'with'=>$container]);
			$this->triggerBm->run('search',$this->implementObject,$hs,$value);
			return $hs;
		}
	}	

	function cleanFieldsRelation($entity) {
		foreach ($this->container['single'] as $value) {
			$sc = strtolower(snake_case($value[0]));
			if (array_key_exists($sc,$entity['data'])) {
				unset($entity['data'][$sc]);
			};			
		}
		foreach ($this->container['list'] as $value) {
			$sc = strtolower(snake_case($value[0]));
			if (array_key_exists($sc,$entity['data'])) {
				unset($entity['data'][$sc]);
			};
		}
		return $entity;
	}

	public function update($value) {
		if (empty($this->container)) {
			return false;
		} else {
			$container = $this->getElementContainer();
			$base = $container[0]; //get first element for base
			$parent_id = strtolower(snake_case($base))."_id";
			$_parent_id = $value[$base]['id'];
			$_val = $this->cleanFieldsRelation($value[$base]);						
			$this->factory->execute('update',$base,$_val);
			foreach ($this->container['single'] as $v) {
				if ($v[0]!==$base) { //skip if is base element
					$entity = $v[0];
					if ($v[1]==0) { //can read/write			
						$_val = $this->cleanFieldsRelation($value[$entity]);		
						$this->factory->execute('update',$entity,$_val);
					}
				}				
			}
			foreach ($this->container['list'] as $v) {
				$entity = $v[0];
				if ($v[1]==0) { //can read/write
					//-- khusus untuk detail, polanya data dihapus kemudian diinsert lagi
					//-- agar bisa tercover untuk yang menghapus maupun menginput row data baru
					$this->factory->execute('deleteWhere',$entity,[$parent_id=>$_parent_id]);		
					foreach ($value[$entity] as $key => $val) {
						$_val = $this->cleanFieldsRelation($val);
						$_val['data'][$parent_id] = $_parent_id;										
						$this->factory->execute('store',$entity,$_val['data']);
					}
				}
			}
			$this->triggerBm->run('update',$this->implementObject,true,$value);
		}
	}

	public function delete($value) {
		if (empty($this->container)) {
			return false;
		} else {
			$container = $this->getElementContainer();
			$base = $container[0]; //get first element for base				
			$parent_id = strtolower(snake_case($base))."_id";					
			foreach ($this->container['single'] as $v) {
				if ($v[0]!==$base) { //skip if is base element
					$entity = $v[0];
					if ($v[1]==0) { //can read/write						
						$this->factory->execute('deleteWhere',$entity,[$parent_id=>$value]);
					}
				}				
			}
			foreach ($this->container['list'] as $v) {
				$entity = $v[0];
				if ($v[1]==0) { //can read/write
					$this->factory->execute('deleteWhere',$entity,[$parent_id=>$value]);
				}
			}
			$this->factory->execute('delete',$base,$value);
			$this->triggerBm->run('delete',$this->implementObject,true,$value);
		}
	}

	public function getElementContainer() {
		$col = [];
		foreach ($this->container['single'] as $value) {
			$col[] = $value[0];
		}
		foreach ($this->container['list'] as $value) {
			$col[] = $value[0];
		}
		return $col;
	}
}

