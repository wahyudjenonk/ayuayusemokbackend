<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mbackend extends CI_Model{
	function __construct(){
		parent::__construct();
		//$this->auth = unserialize(base64_decode($this->session->userdata('44mpp3R4')));
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
		/*$this->db->trans_begin();
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
			case "registrasi":
				$table='tbl_registration';
			break;
		}
		
		switch ($sts_crud){
			case "add":
				if($table!='tbl_registration'){
					$data['create_date'] = date('Y-m-d H:i:s');
					$data['create_by'] = $this->auth['nama_lengkap'];
				}
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
			return 'gagal';
		}else{
			 $this->db->trans_commit();
			 return 'sukses';
		}*/
		
		return "sukses";
	
	}
	
}
