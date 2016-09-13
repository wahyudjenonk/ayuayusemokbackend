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
			case "registration":
				if($balikan=='get'){$where .=" AND A.id=".$this->input->post('id');}
				$sql="SELECT A.*,CONCAT(A.title,' ',A.owner_name_first,' ',A.owner_name_last) as name
					  FROM tbl_registration A ".$where;
				if($balikan=='get')return $this->db->query($sql)->row_array();
			break;
			case "member":
				if($balikan=='get'){$where .=" AND A.member_user='".$this->input->post('id')."'";}
				$sql="SELECT A.*,A.flag as flag_member,CONCAT(B.title,' ',B.owner_name_first,' ',B.owner_name_last) as name,
					  B.*
					  FROM tbl_member A 
					  LEFT JOIN tbl_registration B ON A.tbl_registration_id=B.id ".$where;
				if($balikan=='get')return $this->db->query($sql)->row_array();
			break;
			case "property":
				if($balikan=='get'){$where .=" AND A.id='".$this->input->post('id')."'";}
				$sql="SELECT A.*,CONCAT(C.title,' ',C.owner_name_first,' ',C.owner_name_last) as nama,
						C.id_number,C.phone_home,C.phone_mobile,C.email
						FROM tbl_unit_member A
						LEFT JOIN tbl_member B ON A.tbl_member_user=B.member_user
						LEFT JOIN tbl_registration C ON B.tbl_registration_id=C.id ".$where;
				if($balikan=='get'){
					$data=array();
					$data['properti']=$this->db->query($sql)->row_array();
					$sql="SELECT A.*,B.facility_name,B.unit 
							FROM tbl_unit_facility_member A
							LEFT JOIN cl_facility_unit B ON A.cl_facility_unit_id=B.id 
							WHERE A.tbl_unit_member_id=".$this->input->post('id');
					$data['facility']=$this->db->query($sql)->result_array();
					$sql="SELECT A.*,B.compulsary_periodic_payment
							FROM tbl_unit_compulsary_periodic_payment  A
							LEFT JOIN cl_compulsary_periodic_payment B ON A.cl_compulsary_periodic_payment_id=B.id 
							WHERE A.tbl_unit_member_id=".$this->input->post('id');
					$data['compulsary']=$this->db->query($sql)->result_array();
					$sql="SELECT A.*,B.room_type 
							FROM tbl_unit_room_type_member A 
							LEFT JOIN cl_room_type B ON A.cl_room_type_id=B.id
							WHERE A.tbl_unit_member_id=".$this->input->post('id');
					$data['room_type']=$this->db->query($sql)->result_array();
					//print_r($data['room_type']);
					return $data;
				}
			break;
			case "services":
				$mod=$this->input->post('mod');
				switch($mod){
					case "housekeeping":$pid=1;break;
					case "linen":$pid=2;break;
					case "check":$pid=3;break;
					case "hosting":$pid=4;break;
					case "full_host":$pid=5;break;
				}
				$sql="SELECT * FROM tbl_services WHERE pid=".$pid;
				//return $this->lib->json_grid($sql);
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
		}
		
		return "sukses";
	
	}
	
}
