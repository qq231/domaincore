<?php

namespace App\Domain\%domainname%\Containers;

use FTumiwan\DomainCore\BusinessModel;
use FTumiwan\DomainCore\Factory;

class %containername% extends BusinessModel
{	
	function __construct() {
		parent::__construct('%domainname%');
		$this->container = %containerrelation%;
	}
}
