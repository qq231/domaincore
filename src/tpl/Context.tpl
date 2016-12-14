<?php
namespace App\Domain\%domainname%;

use FTumiwan\DomainCore\DomainLoader;
use FTumiwan\DomainCore\Factory;
use App\Domain\Bridge;

class Context extends DomainLoader
{	
    function __construct() {
        parent::__construct('%domainname%');
    }
    
  
    
}
