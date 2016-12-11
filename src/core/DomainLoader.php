<?php

namespace FTumiwan\DomainCore;

use FTumiwan\DomainCore\Factory;

class DomainLoader 
{
	public $factory;
	public $bridge;
    public $domainName;

    function __construct($domainName) {
        $this->loading($domainName);
    }

	public function loading($domainName) {
		$this->domainName = $domainName;
    	$this->factory = new Factory($domainName);    	    	
    	$this->bridge = new \App\Domain\Bridge();

    	//loading class containers 
    	$_nspace = '\\App\\Domain\\'.$this->domainName.'\\Containers';
    	$_dir = base_path('app/Domain/'.$this->domainName.'/Containers');
    	foreach (new \DirectoryIterator($_dir) as $fl) {    		
    		if ($fl->getExtension()=='php') {
    			$getfile = explode('.',$fl->getFilename());
    			$_prop = lcfirst($getfile[0]);    			
    			$_conname = $_nspace."\\".$getfile[0];
    			$this->{$_prop} = new $_conname();
    		}
    	}
	}

	public function bridgeCall($methodInject,$parameter) {
    	return $this->bridge->call($this->domainName,$methodInject,$parameter);
    }
}
