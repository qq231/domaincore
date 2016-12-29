<?php
namespace FTumiwan\DomainCore;

use FTumiwan\DomainCore\Repo;
use FTumiwan\DomainCore\Trigger;
/**
* 
*/
class Factory
{
	protected $domainName;
	protected $entities;
	protected $domain;
	protected $trigger;

	function __construct($domainName)
	{
		$this->domain = $domainName;
		$this->domainName = "Domain/".$domainName;
		$this->entities = "app/Domain/".$domainName."/Entities/";
		$this->trigger = new Trigger($domainName);
		$this->checkDomain();
	}
	public function execute($action,$entity,$value) {
		//action = store, update, delete, deleteWhere, find, queryAnd, loadAll
		//entity = is entity name
		//value = is value..... if value with parameter, use array... example: [['nama'=>'kiki','alamat'=>'manado'],['id'=>1]]		
		if ($this->checkEntityIsFound($entity)) {
			$repo = new Repo("App\\Domain\\".$this->domain."\\entities\\".$entity);
			switch ($action) {
				case 'getSchema':
					return $repo->getSchema($entity);
					break;
				case 'store':
					$hs = $repo->store($value);
					$this->trigger->run('store',$entity,$hs,$value);
					return $hs;
					break;
				case 'update':
					$hs = $repo->update($value['data'],$value['id']);
					$this->trigger->run('update',$entity,$hs,$value);
					return $hs;
					break;
				case 'delete':
					$hs = $repo->delete($value);
					$this->trigger->run('delete',$entity,$hs,$value);
					return $hs;
					break;
				case 'deleteWhere':
					$hs = $repo->deleteWhere($value);
					$this->trigger->run('deleteWhere',$entity,$hs,$value);
					return $hs;
					break;
				case 'search':
					$hs = $repo->search($value);
					return $hs;
					break;
				case 'find':
					$hs = $repo->find($value);
					$this->trigger->run('find',$entity,$hs,$value);
					return $hs;
					break;
				case 'findWith':
					$hs = $repo->findWith($value);
					$this->trigger->run('findWith',$entity,$hs,$value);
					return $hs;
					break;
				case 'queryAnd':
					$hs = $repo->queryAnd($value);
					$this->trigger->run('queryAnd',$entity,$hs,$value);
					return $hs;
					break;
				case 'queryAndContainer': //use in business domain
					$hs = $repo->queryAndContainer($value);
					$this->trigger->run('queryAndContainer',$entity,$hs,$value);
					return $hs;
					break;
				case 'loadAll':
					$hs = $repo->loadAll($value);
					$this->trigger->run('loadAll',$entity,$hs,$value);
					return $hs;
					break;
				default:
					throw new Exception("action execute at entity not found");					
					break;
			}
		} else {
			throw new Exception("entity is not found!....");
		}
	}
	function checkDomain() {
		if (is_dir(base_path('app/'.$this->domainName))) {			
		} else {
			throw new Exception("domain not found");			
		}		
	}
	function checkEntityIsFound($entity) {
		if (!file_exists(base_path($this->entities.$entity.".php"))) {
			echo "entity ".$this->entities.$entity." not found";
			return false;
		} else {
			return true;
		}
	}
}