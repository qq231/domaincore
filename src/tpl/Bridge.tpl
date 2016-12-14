<?php

namespace App\Domain;

use FTumiwan\DomainCore\DomainBridge;

// this class provide for automatic method injection from separate problem domain
// with the class, communication still separate
class Bridge extends DomainBridge
{
	function __construct() {
		// -- example...
		// $this->register = [
		// 	'Pembelian::penerimaanBarang'=>[ //method as injected
		// 		'Gudang::statusBarang',
		// 		'Gudang::stockBertambah'
		// 	],
		// 	'Gudang::persediaanBarang'=>[ //method as injected
		// 		'Pembelian::historyPenerimaan'
		// 	]
		// ];
		
		$this->register = [

		]; 
		
	}	
	
}
