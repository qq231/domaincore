<?php
namespace FTumiwan\DomainCore;
/**
 *
 */
class Gate
{	
	protected $domainContext;

	function __construct($context) {
		$this->domainContext = $context;
	}

	public function httpComing($request) {
    	//**contoh format request-value
		//{
		// 	"obj":"container.MasterBarang.queryAnd",
		// 	"param":{
		// 		"kode":"12323"
		// 	}
		//}    	
    	$req = $request->all();    	
    	$obj = explode('.',$req['obj']);
    	$param = $req['param'];    	
    	    	  	
    	switch ($obj[0]) {
    		case 'container':
    			$_method = $obj[2];  
    			$fpath = 'App\\Domain\\'.$this->domainContext->domainName.'\\Containers\\'.$obj[1];
    			return (new $fpath())->$_method($param);
    			break;
    		case 'entity':    			
    			$entity = $obj[1];
    			$_method_entity = $obj[2];
    			return $this->domainContext->factory->execute($_method_entity,$entity,$param);
    			break;       					
    		default:
    			$_ctx_method = $obj[0];
    			return $this->domainContext->$_ctx_method($param);
    			break;    
    	}
    }


}