<?php
use FTumiwan\DomainCore\Factory;
use App\Domain\FakturPenjualan\Containers\NotaPenjualan;
use App\Domain\FakturPenjualan\Context;

/**
* 	
*/
class DomainCore extends TestCase
{
	public function testFactory() {
		(new Factory('FakturPenjualan'))->execute('store','TipePenjualan'
			,['ket'=>'Penjualan Kredit']);
		(new Factory('FakturPenjualan'))->execute('store','TipePenjualan'
			,['ket'=>'Penjualan Konsinyasi']);
		// $id = (new Factory('FakturPenjualan'))->execute('store','Penjualan',['tgl'=>'2016-10-11','pelanggan_id'=>1,'pelanggan'=>'CV. Sinar Surya Sentosa'
		// 	,'brutto'=>120000,'discount'=>80000,'netto'=>87000,'stat_posting'=>0]);
		// (new Factory('FakturPenjualan'))->execute('store','PenjualanDetail',['tgl'=>'2016-10-12'
		// 	,'penjualan_id'=>12,'barang_id'=>10,'barang'=>'Aqua Botol 180ml','qty'=>2,'satuan'=>'pcs'
		// 	,'isi'=>10,'harga_jual'=>180000,'harga_pokok'=>200000,'brutto'=>2888100
		// 	,'discount_config'=>'10','discount_rp'=>100000,'netto'=>250000]);
	}

	public function testBusinessModel() {
		$mb = new NotaPenjualan();
		echo $mb->store(['Penjualan'=>['tgl'=>'2016-10-11','pelanggan_id'=>20
							,'pelanggan'=>'CV. Duo Maju Bersama'
							,'brutto'=>120000,'discount'=>80000,'netto'=>87000
							,'stat_posting'=>0,'tipe_penjualan_id'=>1],
						'PenjualanDetail'=>[
								['tgl'=>'2016-10-12','barang_id'=>1,'barang'=>'Le Mineral 500ml','qty'=>2
								,'satuan'=>'pcs','isi'=>10,'harga_jual'=>180000,'harga_pokok'=>200000
								,'brutto'=>2888100,'discount_config'=>'10','discount_rp'=>100000
								,'netto'=>250000]
								,['tgl'=>'2016-10-12','barang_id'=>1,'barang'=>'Coca Cola Can','qty'=>2
								,'satuan'=>'pcs','isi'=>10,'harga_jual'=>180000,'harga_pokok'=>200000
								,'brutto'=>2888100,'discount_config'=>'10','discount_rp'=>100000
								,'netto'=>250000]
							]
						]);

	}

	public function testFindBusinessModel() {
		$mb = new NotaPenjualan();
		$x = $mb->find(1);
		echo $x->pelanggan;
	}

	public function testLoadAllBusinessModel() {
		$mb = new NotaPenjualan();
		$x = $mb->loadAll(['limit'=>3]);
		//print_r($x);
	}

	public function testQueryAndBusinessModel() {
		$mb = new NotaPenjualan();
		$x = $mb->queryAnd(['pelanggan_id'=>'20']);
		echo $x[0]->pelanggan." -- ".$x[0]->penjualanDetail[0]->barang." -- ".$x[0]->tipePenjualan->ket;
	}

	public function testUpdateBusinessModel() {
		$mb = new NotaPenjualan();
		$data = ['Penjualan'=>['data'=>
								['tgl'=>'2016-10-11','pelanggan_id'=>20
									,'pelanggan'=>'CV Pengganti'
									,'brutto'=>120000,'discount'=>80000,'netto'=>87000
									,'stat_posting'=>0,'tipe_penjualan'=>null,'tipe_penjualan_id'=>1],
								'id'=>1
					],
					'PenjualanDetail'=>[
						['data'=>
							['tgl'=>'2016-10-12','barang_id'=>1,'barang'=>'Le Mineral 500ml di ubah','qty'=>2
								,'satuan'=>'pcs','isi'=>10,'harga_jual'=>180000,'harga_pokok'=>200000
								,'brutto'=>2888100,'discount_config'=>'10','discount_rp'=>100000
								,'netto'=>250000,'penjualan_id'=>1],
							'id'=>1
						]
						,['data'=>
							['tgl'=>'2016-10-12','barang_id'=>1,'barang'=>'Coca Cola Can di ubah','qty'=>2
								,'satuan'=>'pcs','isi'=>10,'harga_jual'=>180000,'harga_pokok'=>200000
								,'brutto'=>2888100,'discount_config'=>'10','discount_rp'=>100000
								,'netto'=>250000,'penjualan_id'=>1],
							'id'=>2
						]
						,['data'=>
							['tgl'=>'2016-10-12','barang_id'=>1,'barang'=>'Coca Cola Can di ubah sekali lagi','qty'=>2
								,'satuan'=>'pcs','isi'=>10,'harga_jual'=>180000,'harga_pokok'=>200000
								,'brutto'=>2888100,'discount_config'=>'10','discount_rp'=>100000
								,'netto'=>250000,'penjualan_id'=>0],
							'id'=>0
						]
					]
				];
		$x = $mb->update($data);
	}

	public function testDeleteBusinessModel() {
		// $mb = new NotaPenjualan();
		// $mb->delete(3);
	}

	// public function testBridge() {
	// 	$x = new ContextCoba();
	// 	$x->cobaBridge();
	// }

	// public function testGetSchemaEntity() {
	// 	$x = new ContextCoba();
	// 	print_r($x->factory->execute('getSchema','Barang',''));
	// }
}