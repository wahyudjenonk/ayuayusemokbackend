<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Mjingga_api extends CI_Model{
	function __construct(){
		parent::__construct();
		//$this->auth = unserialize(base64_decode($this->session->userdata('44mpp3R4')));
		$this->load->library(array('encrypt','lib'));
	}
	
	function get_data($type="", $balikan="", $p1=""){
		$msg=array();
		switch($type){
			case "data_login":
				$sql="SELECT * FROM tbl_member where member_user='".$p1."' OR email_address='".$p1."'";
			break;
			case "forgot_pwd":
				$balikan="row_array";
				$sql="SELECT * FROM tbl_member where email_address='".$this->input->post('email_address')."'";
				//return $sql;
			break;
		}
		if($balikan == 'json'){
			$data= $this->lib->json_grid($sql);
		}elseif($balikan == 'row_array'){
			$data= $this->db->query($sql)->row_array();
		}elseif($balikan == 'result'){
			$data= $this->db->query($sql)->result();
		}elseif($balikan == 'result_array'){
			$data= $this->db->query($sql)->result_array();
		}
		return $msg=array('msg'=>'sukses','data'=>$data);
		
		
	}
	function simpandata($table,$data,$sts_crud){ //$sts_crud --> STATUS NYEE INSERT, UPDATE, DELETE
		$this->db->trans_begin();
		$msg=array();
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
				$data['flag']='P';
				if($sts_crud=='add'){
					$ex=$this->db->get_where('tbl_registration',array('email'=>$data['email']))->row_array();
					$msg['data']=array('member_user'=>$this->lib->uniq_id(),
									'pwd'=>$this->lib->uniq_id(),
									'email_address'=>$data['email']
					);
					if(isset($ex['email'])){
						$this->db->trans_rollback();
						return array('msg'=>'gagal','pesan'=>'Your email has already in system.');
					}
				}
				if($sts_crud=='edit'){
					$sql="SELECT * FROM tbl_registration WHERE email='".$data['email_address']."'";
					$reg=$this->db->query($sql)->row_array();
					if(isset($reg['email'])){
						$id=$reg['id'];
						$data['flag']='F';
						$ex=$this->db->get_where('tbl_member',array('email_address'=>$data['email_address']))->row_array();
						if(isset($ex['email_address'])){
							$this->db->trans_rollback();
							return array('msg'=>'gagal','pesan'=>'Your email has already in system.');
						}else{
							$data_member=array('member_user'=>$data['member_user'], //$this->lib->uniq_id(),
											   'email_address'=>$data['email_address'],
											   'tbl_registration_id'=>$reg['id'],
											   'pwd'=>$this->encrypt->encode($data['pwd']),
											   'create_date'=>date('Y-m-d H:i:s'),
											   'create_by'=>'SYS',
											   'flag'=>1
							);
							$this->db->insert('tbl_member',$data_member);
							unset($data['email_address']);
							unset($data['pwd']);
							unset($data['member_user']);
						}
					}else{
						$this->db->trans_rollback();
						return array('msg'=>'gagal','pesan'=>'Failed register.');
					}
				}
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
			$msg['msg']='gagal';
			$msg['pesan']='System Failure, Please Try Again Later.';
			//return 'gagal';
		}else{
			 $this->db->trans_commit();
			 $msg['msg'] = 'sukses';
			 $msg['pesan'] = '';
			// return 'sukses';
		}
		
		return $msg;
	}
	
}
