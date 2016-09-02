<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Frontend extends JINGGA_Controller {
	
	function __construct(){
		parent::__construct();
	}
	
	function index(){				
		$this->nsmarty->assign('konten', 'beranda');		
		$this->nsmarty->display( 'frontend/main-index.html');		
	}
	
	function getdisplay($type="", $p1="", $p2="", $p3=""){
		switch($type){
			case "main_page":								
				$this->nsmarty->display( 'frontend/main-index.html');	
			break;
			case "loading_page":
				switch($p1){
					case "beranda":
						$temp = "frontend/modul/beranda-page.html";
						$dataslider 		= $this->mfrontend->getdata('fotoslider', 'result_array');
						$pelayanankebaktian = $this->mfrontend->getdata('pelayankebaktian', 'result_array');
						$renunganwarta 		= $this->mfrontend->getdata('renunganwarta', 'row_array');
						$wartakemajelisan 	= $this->mfrontend->getdata('wartakemajelisan', 'result_array', 1);
						$wartakomisi 		= $this->mfrontend->getdata('wartakomisi', 'result_array', 2);
						$wartakombas 		= $this->mfrontend->getdata('wartakombas', 'result_array', 3);
						$renunganberanda 	= $this->mfrontend->getdata('renunganberanda', 'result_array');
						$artikelberanda 	= $this->mfrontend->getdata('artikelberanda', 'result_array');
						
						foreach($pelayanankebaktian as $z => $y){
							$expt =  explode("-", $y['tgl_kebaktian']);
							$konvers = $this->lib->konversi_bulan((int)$expt[1],'fullbulan');
							$pelayanankebaktian[$z]['tgl_kebaktian'] = $expt[2]." ".$konvers." ".$expt[0];
						}
						
						foreach($renunganberanda as $k => $v){
							$exp =  explode("-", $v['tgl_renungan']);
							$renunganberanda[$k]['bulan'] = $this->lib->konversi_bulan((int)$exp[1]);
							$renunganberanda[$k]['isi_renungan_harian'] = $this->lib->cutstring($v['isi_renungan_harian'], 400);
						}
						
						foreach($artikelberanda as $b => $n){
							$artikelberanda[$b]['isi_artikel'] = $this->lib->cutstring($n['isi_artikel'], 400);
						}
						
						$exptgl = explode("-", $renunganwarta['tgl_renungan']);
						$konversibulan = $this->lib->konversi_bulan((int)$exptgl[1],'fullbulan');
						$renunganwarta['tgl_renungan'] = $exptgl[2]." ".$konversibulan." ".$exptgl[0];
						
						$this->nsmarty->assign('dataslider', $dataslider);		
						$this->nsmarty->assign('pelayanankebaktian', $pelayanankebaktian);		
						$this->nsmarty->assign('renunganwarta', $renunganwarta);		
						$this->nsmarty->assign('wartakemajelisan', $wartakemajelisan);		
						$this->nsmarty->assign('wartakomisi', $wartakomisi);		
						$this->nsmarty->assign('wartakombas', $wartakombas);		
						$this->nsmarty->assign('renunganberanda', $renunganberanda);		
						$this->nsmarty->assign('artikelberanda', $artikelberanda);
					break;
				}		
				
				if(isset($temp)){
					$template = $this->nsmarty->fetch($temp);
				}else{
					$template = $this->nsmarty->fetch("konstruksi.html");
				}
				
				$array_page = array(
					'loadbalancedt' => md5('Ymd'),
					'loadbalancetm' => md5('H:i:s'),
					'loadtmr' => md5('YmdHis'),
					'page' => $template 
				);
				
				echo json_encode($array_page);
			break;
		}
	}
	
	function generatepdf($type){
		$this->load->library('mlpdf');	
		switch($type){
			case "bastnya":
				$inv = $this->input->post('invo');
				if(!$inv){
					echo "tutup tab browser ini, dan generate kembali melalui tombol di web www.aldeaz.id";
					exit;
				}
				$data_invoice = $this->mfrontend->getdata('header_pesanan', 'row_array', $inv);
				if($data_invoice){
					$no_bast = $data_invoice['no_order']."/ASP/BAST/".date('Y');
					$datacust = $this->mfrontend->getdata('datacustomer', 'row_array', $data_invoice['tbl_registrasi_id'], '', 'cetak_bast');
					$datakonfirmasi = $this->db->get_where('tbl_konfirmasi', array('tbl_h_pemesanan_id'=>$data_invoice['id']) )->row_array();
					$datadetailpesanan = $this->mfrontend->getdata('detail_pesanan', 'result_array', $data_invoice['id']);
					$totqty = 0;
					$tottotal = 0;
					foreach($datadetailpesanan as $k => $v){
						$totqty += $v['qty'];
						$tottotal += $v['subtotal'];
						
						$datadetailpesanan[$k]['harga'] = number_format($v['harga'],0,",",".");
						$datadetailpesanan[$k]['subtotal'] = number_format($v['subtotal'],0,",",".");
						$datadetailpesanan[$k]['nama_group'] = strtoupper(substr($v['nama_group'], 0,1));
					}
					
					$cekdatabast = $this->db->get_where('tbl_bast', array('tbl_konfirmasi_id'=>$datakonfirmasi['id']) )->row_array();
					if(!$cekdatabast){
						$array_insert_bast = array(
							'tbl_konfirmasi_id' => $datakonfirmasi['id'],
							'no_bast' => $no_bast,
							'create_date' => date('Y-m-d H:i:s')
						);
						$this->db->insert('tbl_bast', $array_insert_bast);
					}
					
					$tgl = $this->lib->konversi_tgl(date('Y-m-d'));
					
					$this->nsmarty->assign('datainvoice', $data_invoice);
					$this->nsmarty->assign('datakonfirmasi', $datakonfirmasi);
					$this->nsmarty->assign('datacust', $datacust);
					$this->nsmarty->assign('datadetailpesanan', $datadetailpesanan);
					$this->nsmarty->assign('totqty', $totqty);
					$this->nsmarty->assign('tgl', $tgl);
					$this->nsmarty->assign('no_bast', $no_bast);
					$this->nsmarty->assign('tottotal', number_format($tottotal,0,",","."));
				}
				
				$filename = "DOCBAST-";
				$htmlcontent = $this->nsmarty->fetch('frontend/modul/bast_pdf.html');
				
				$pdf = $this->mlpdf->load();
				$spdf = new mPDF('', 'A4', 0, '', 12.7, 12.7, 15, 20, 5, 2, 'P');
				$spdf->ignore_invalid_utf8 = true;
				$spdf->allow_charset_conversion = true;     // which is already true by default
				$spdf->charset_in = 'iso-8859-2';  // set content encoding to iso
				$spdf->SetDisplayMode('fullpage');		
				$spdf->SetProtection(array('print'));				
				$spdf->WriteHTML($htmlcontent); // write the HTML into the PDF
				//$spdf->Output($general_path.$subgroup."/".$io_number."/"."PARTIAL-".$partial_no."/LOA/".$filename.'.pdf', 'F'); // save to file because we can
				$spdf->Output($filename.'.pdf', 'I'); // view file
			break;
		}
	}
		
	function cruddata($p1="", $p2=""){
		$post = array();
        foreach($_POST as $k=>$v){
			if($this->input->post($k)!=""){
				$post[$k] = $this->db->escape_str($this->input->post($k));
			}
		}
		
		if(isset($post['editstatus'])){$editstatus = $post['editstatus'];unset($post['editstatus']);}
		else $editstatus = null;
		
		echo $this->mfrontend->simpansavedata($p1, $post, $editstatus);
	}
	
	function test(){			
		echo "test";
	}
	
}
