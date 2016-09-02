<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mbackend extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('44mpp3R4')));
	}
	
	function getdata($type="", $balikan="", $p1="", $p2="",$p3="",$p4=""){
		$where = " WHERE 1=1 ";
		if($this->input->post('key')){
				$where .=" AND ".$this->input->post('kat')." like '%".$this->db->escape_str($this->input->post('key'))."%'";
		}
		switch($type){
			case "data_login":
				$sql = "
					SELECT *
					FROM tbl_user 
					WHERE nama_user = '".$p1."'
				";
			break;
			
			//Modul Kasir
			case "get_setting":
				$sql = "
					SELECT value
					FROM tbl_setting
					WHERE param = '".$p1."'
				";
			break;
			case "get_meja_perlantai":
				$sql = "
					SELECT *
					FROM tbl_meja
					WHERE lantai_id = '".$p1."'
				";
			break;

			case "list_pesanan_kasir":
				$id_meja = $this->input->post('id_meja');
				$sql = "
					SELECT A.*, B.nama_produk
					FROM tbl_transaksi_penjualan A
					LEFT JOIN tbl_produk B ON B.id = A.tbl_produk_id
					WHERE A.cl_meja_id = '".$id_meja."'
				";
			break;
			case "list_produk_kasir":
				$where = "";
				$kategori = $this->input->post('kategori');
				if($kategori){
					$where .= "
						AND A.cl_kategori_id = '".$kategori."'
					";
				}
				$nama_produk = $this->input->post('nama_produk');
				if($nama_produk){
					$where .= "
						AND A.nama_produk = '".$nama_produk."'
					";
				}
				
				$sql = "
					SELECT A.*, B.nama_kategori
					FROM tbl_produk A
					LEFT JOIN cl_kategori B ON B.id = A.cl_kategori_id
					WHERE 1=1 $where
				";
			break;
			case "total_pesanan":
				$id_meja = $this->input->post('id_meja');
				$sql = "
					SELECT SUM(qty) as tot_qty, SUM(total_harga) as tot_harga
					FROM tbl_transaksi_penjualan
					WHERE cl_meja_id = '".$id_meja."'
				";
			break;			
			//End Modul Kasir
		}
		
		if($balikan == 'json'){
			return $this->lib->json_grid($sql);
		}elseif($balikan == 'row_array'){
			return $this->db->query($sql)->row_array();
		}elseif($balikan == 'result'){
			return $this->db->query($sql)->result();
		}elseif($balikan == 'result_array'){
			return $this->db->query($sql)->result_array();
		}
		
	}
	
	function get_combo($type="", $p1="", $p2=""){
		switch($type){
			case "cl_kategori":
				$sql = "
					SELECT id, nama_kategori as txt
					FROM cl_kategori
				";
			break;
		}
		
		return $this->db->query($sql)->result_array();
	}
	
	function simpandata($table,$data,$sts_crud){ //$sts_crud --> STATUS NYEE INSERT, UPDATE, DELETE
		$this->db->trans_begin();
		if(isset($data['id'])){
			$id = $data['id'];
			unset($data['id']);
		}
		
		switch($table){
			case "admin":
				//print_r($data);exit;
				if($sts_crud=='add')$data['password']=$this->encrypt->encode($data['password']);
				if(!isset($data['status'])){$data['status']=0;}
			break;
			case "transaksi_penjualan":
				$table = "tbl_transaksi_penjualan";
				$id_meja = $data['cl_meja_id'];
				$tbl_produk_id = $data['tbl_produk_id'];
				unset($data['cl_meja_id']);
				unset($data['tbl_produk_id']);
				
				$cek_data_produk = $this->db->get_where('tbl_produk', array('id'=>$tbl_produk_id) )->row_array();
				$cek_data = $this->db->get_where('tbl_transaksi_penjualan', array('cl_meja_id'=>$id_meja, 'tbl_produk_id'=>$tbl_produk_id) )->row_array();
				
				if($cek_data){
					$sts_crud = 'edit';
					$qty_update = ($cek_data['qty']+1);
					
					$id = $cek_data['id'];
					$data['qty'] = $qty_update;
					$data['total_harga'] = ($qty_update*$cek_data_produk['harga_jual']);
				}else{
					$sts_crud = 'add';
					
					$data['cl_meja_id'] = $id_meja;
					$data['tbl_produk_id'] = $tbl_produk_id;
					$data['qty'] = 1;
					$data['harga_satuan'] = $cek_data_produk['harga_jual'];
					$data['total_harga'] = $cek_data_produk['harga_jual'];
				}
			break;
			case "hapus_item_kasir":
				$table = "tbl_transaksi_penjualan";
				$id_meja = $data['id_meja'];
				$tbl_produk_id = $data['tbl_produk_id'];
				unset($data['id_meja']);
				unset($data['tbl_produk_id']);				
				
				$cek_data = $this->db->get_where('tbl_transaksi_penjualan', array('id'=>$id, 'cl_meja_id'=>$id_meja, 'tbl_produk_id'=>$tbl_produk_id) )->row_array();
				if($cek_data['qty'] > 1){
					$sts_crud = 'edit';
					$qty = ($cek_data['qty']-1);
					$data['qty'] = $qty;
					$data['total_harga'] = ($qty * $cek_data['harga_satuan']);
				}else{
					$sts_crud = 'delete';
				}
			break;
			case "tutup_transaksi":
				$table = "tbl_h_penjualan_outlet";
				$get_data_transaksi_temp = $this->db->get_where('tbl_transaksi_penjualan', array('cl_meja_id'=>$data['tbl_meja_id']))->result_array();
				$array_batch_insert = array();
				foreach($get_data_transaksi_temp as $k => $v){
					$array_insert = array(
						'kode_transaksi' => $data['kode_transaksi'],
						'tbl_produk_id' => $v['tbl_produk_id'],
						'qty' => $v['qty'],
						'harga_satuan' => $v['harga_satuan'],
						'total_harga' => $v['total_harga'],
						'create_date' => date('Y-m-d H:i:s'),
						'create_by' => $this->auth['nama_lengkap'],
					);
					array_push($array_batch_insert, $array_insert);
				}
				
				$insert_rame = $this->db->insert_batch('tbl_d_penjualan_outlet', $array_batch_insert);
				if($insert_rame){
					$this->db->delete('tbl_transaksi_penjualan', array('cl_meja_id'=>$data['tbl_meja_id']));
					$this->db->update('tbl_meja', array('status_meja'=>'Y'), array('id'=>$data['tbl_meja_id']));
				}
				
				$data['jumlah_uang_dibayar'] = ($data['jumlah_bayar_pesanan'] + $data['pajak']);
				//$data['create_date'] = date('Y-m-d H:i:s');
				//$data['create_by'] = $this->auth['nama_lengkap'];
			break;
		}
		
		switch ($sts_crud){
			case "add":
				$data['create_date'] = date('Y-m-d H:i:s');
				$data['create_by'] = $this->auth['nama_lengkap'];
				$this->db->insert($table,$data);
			break;
			case "edit":
				$this->db->update($table, $data, array('id' => $id) );
			break;
			case "delete":
				$this->db->delete($table, array('id' => $id));
			break;
		}
		
		if($this->db->trans_status() == false){
			$this->db->trans_rollback();
			return 0;
		}else{
			return $this->db->trans_commit();
		}
	
	}
	
}
