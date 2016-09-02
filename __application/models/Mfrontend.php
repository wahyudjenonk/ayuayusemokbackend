<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/*
	Models Buatan Wahyu Jinggomen
*/

class Mfrontend extends CI_Model{
	function __construct(){
		parent::__construct();
		$this->auth = unserialize(base64_decode($this->session->userdata('4mp31244444')));
	}
	
	function getdata($type="", $balikan="", $p1="", $p2="",$p3="",$p4=""){
		$where = " WHERE 1=1 ";
		$join = "";
		$select = "";
		$order = "";
		
		switch($type){
			case "fotoslider":
				$sql = "
					SELECT judul_slider, gambar_slider
					FROM tbl_slider
				";
			break;
			case "pelayankebaktian":
				$sql = "
					SELECT A.*, B.jadwal_kebaktian
					FROM tbl_pelayanan_minggu A
					LEFT JOIN cl_jadwal_kebaktian B ON B.id = A.cl_jadwal_kebaktian_id
					WHERE A.tgl_kebaktian >= CURDATE()
					ORDER BY tgl_kebaktian ASC
					LIMIT 0,2
				";
			break;
			case "renunganwarta":
				$sql = "
					SELECT *
					FROM tbl_renungan_warta
					WHERE tgl_renungan >= CURDATE()
					ORDER BY tgl_renungan ASC
					LIMIT 0,1
				";
			break;
			case "wartakomisi":
			case "wartakombas":
			case "wartakemajelisan":
				$sql = "
					SELECT * 
					FROM tbl_warta
					WHERE tipe_warta = '".$p1."'
				";
			break;
			case "renunganberanda":
				$sql = "
					SELECT *
					FROM tbl_renungan_harian
					ORDER BY tgl_renungan DESC
					LIMIT 0,3
				";
			break;
			case "artikelberanda":
				$sql = "
					SELECT *
					FROM tbl_artikel_rohani
					ORDER BY id DESC
					LIMIT 0,3
				";
			break;
			
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
			case "cl_komisi_id":
				$sql = "
					SELECT id, nama_komisi as txt
					FROM cl_komisi
				";
			break;
			case "cl_jadwal_kebaktian_id":
				$sql = "
					SELECT id, jadwal_kebaktian as txt
					FROM cl_jadwal_kebaktian
				";
			break;
		}
		
		return $this->db->query($sql)->result_array();
	}
	
	function simpansavedata($table,$data,$sts_crud){  //$sts_crud --> STATUS NYEE INSERT, UPDATE, DELETE
		$this->db->trans_begin();
		if(isset($data['id'])){
			$id = $data['id'];
			unset($data['id']);
		}
		
		switch($table){
			case "asu":
				
			break;
		}
		
		switch ($sts_crud){
			case "add":
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
		} else{
			return $this->db->trans_commit();
		}
	
	}
	
}
