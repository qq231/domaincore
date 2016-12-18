<?php
use Illuminate\Http\Request;
use FTumiwan\DomainCore\Gate;

//*--- begin FakturPenjualan

	    Route::post('api/faktur-penjualan', function (Request $request){
			return (new Gate((new App\Domain\FakturPenjualan\Context())))->httpComing($request);
		});
	    
//*--- end FakturPenjualan