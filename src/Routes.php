<?php
use Illuminate\Http\Request;
use FTumiwan\DomainCore\Gate;

Route::get('api/dateserver',function(){
	return date('Ymd');
});

//*--- begin FakturPenjualan

	    Route::post('api/faktur-penjualan', function (Request $request){
			return (new Gate((new App\Domain\FakturPenjualan\Context())))->httpComing($request);
		});
	    
//*--- end FakturPenjualan