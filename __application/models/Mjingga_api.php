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
				$balikan="row_array";
				$sql="SELECT * FROM tbl_member where member_user='".$p1."' OR email_address='".$p1."'";
				$data= $this->db->query($sql)->row_array();
				return $data;
			break;
			case "forgot_pwd":
				$balikan="row_array";
				$sql="SELECT * FROM tbl_member where email_address='".$this->input->post('email_address')."'";
				$data= $this->db->query($sql)->row_array();
				return $msg=array('msg'=>'sukses','data'=>$data);
				//return $sql;
			break;
			case "property":
				//return $msg=array('msg'=>'sukses','data'=>'Xxx');
				//$this->set_response('XXxxx', REST_Controller::HTTP_OK);
				$sql="
					SELECT A.*, B.photo_unit 
					FROM tbl_unit_member A
					LEFT JOIN (SELECT * FROM tbl_unit_photo LIMIT 1)B ON B.tbl_unit_member_id = A.id
				";
				if($balikan=='detil'){
					$data=array();
					$sql .=" WHERE A.id=".$this->input->post('id');
					$data['properti']=$this->db->query($sql)->row_array();
					$sql="SELECT * FROM tbl_unit_facility_member WHERE tbl_unit_member_id=".$this->input->post('id');
					$data['facility']=$this->db->query($sql)->result_array();
					$sql="SELECT * FROM tbl_unit_compulsary_periodic_payment WHERE tbl_unit_member_id=".$this->input->post('id');
					$data['compulsary']=$this->db->query($sql)->result_array();
					$sql="SELECT * FROM tbl_unit_room_type_member WHERE tbl_unit_member_id=".$this->input->post('id');
					$data['room_type']=$this->db->query($sql)->result_array();
					$sql="SELECT * FROM tbl_unit_photo WHERE tbl_unit_member_id=".$this->input->post('id');
					$data['photo']=$this->db->query($sql)->result_array();
					return $msg=array('msg'=>'sukses','data'=>$data);
				}else{
					return $msg=array('msg'=>'sukses','data'=>$this->db->query($sql)->result_array());
				}
			break;
			case "combo_all":
				$sql="SELECT * FROM ".$balikan;
				$data= $this->db->query($sql)->result_array();
				return $msg=array('msg'=>'sukses','data'=>$data);
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
			case "property":
				$table='tbl_unit_member';
				$cl_compulsary_id=array();
				$cl_compulsary_periodic_payment_id=array();
				$cl_facility_unit_id=array();
				$cl_facility_id=array();
				$cl_room_type_id=array();
				$cl_room_id=array();
				$photo_unit=array();
				$photo_unit_var=array();
				if(isset($data['cl_compulsary_periodic_payment_id'])){
					$cl_compulsary_id=$data['cl_compulsary_periodic_payment_id'];
					unset($data['cl_compulsary_periodic_payment_id']);
				}
				if(isset($data['cl_facility_unit_id'])){
					$cl_facility_id=$data['cl_facility_unit_id'];
					$qty=$data['qty'];
					unset($data['cl_facility_unit_id']);
					unset($data['qty']);
				}
				if(isset($data['cl_room_type_id'])){
					$cl_room_id=$data['cl_room_type_id'];
					unset($data['cl_room_type_id']);
				}
				if(isset($data['photo_unit'])){
					$photo_unit_var=$data['photo_unit'];
					unset($data['photo_unit']);
				}
				//return $msg['msg'] =$_POST;
			break;
		}
		
		switch ($sts_crud){
			case "add":
				if($table!='tbl_registration'){
					$data['create_date'] = date('Y-m-d H:i:s');
					//$data['create_by'] = $this->auth['nama_lengkap'];
				}
				if($table=='tbl_unit_member'){
					$this->db->insert($table,$data);//INSERT UNIT;
					$id_unit=$this->db->insert_id();
					if(count($cl_compulsary_id)>0){
						for($i=0;$i<count($cl_compulsary_id);$i++){
							$cl_compulsary_periodic_payment_id[]=array(	
										'tbl_unit_member_id'=>$id_unit,
										'cl_compulsary_periodic_payment_id'=>$cl_compulsary_id[$i],
										'create_date'=>date('Y-m-d H:i:s')
							);
						}
						 $this->db->insert_batch('tbl_unit_compulsary_periodic_payment', $cl_compulsary_periodic_payment_id);
					}
					if(count($cl_facility_id)>0){
						for($i=0;$i<count($cl_facility_id);$i++){
							$cl_facility_unit_id[]=array(
									'tbl_unit_member_id'=>$id_unit,
									'cl_facility_unit_id'=>$cl_facility_id[$i],
									'qty'=>$qty[$i],
									'create_date'=>date('Y-m-d H:i:s')
							);
						}
						 $this->db->insert_batch('tbl_unit_facility_member', $cl_facility_unit_id);
					}
					if(count($cl_room_id)>0){
						for($i=0;$i<count($cl_room_id);$i++){
							$cl_room_type_id[]=array(
									'tbl_unit_member_id'=>$id_unit,
									'cl_room_type_id'=>$cl_room_id[$i],
									'create_date'=>date('Y-m-d H:i:s')
							);
						}
						 $this->db->insert_batch('tbl_unit_room_type_member', $cl_room_type_id);
					}
					if(count($photo_unit_var)>0){
						for($i=0;$i<count($photo_unit_var);$i++){
							$photo_unit[]=array(
									'tbl_unit_member_id'=>$id_unit,
									'photo_unit'=>$photo_unit_var[$i],
									'create_date'=>date('Y-m-d H:i:s')
							);
						}
						 $this->db->insert_batch('tbl_unit_photo', $photo_unit);
					}
					
				}else{
					$this->db->insert($table,$data);
				}
			break;
			case "edit":
				if($table=='tbl_unit_member'){
					$this->db->update($table, $data, array('id' => $id) );
					if(count($cl_compulsary_id)>0){
						$this->db->delete('tbl_unit_compulsary_periodic_payment',array('tbl_unit_member_id'=>$id));
						for($i=0;$i<count($cl_compulsary_id);$i++){
							$cl_compulsary_periodic_payment_id[]=array(	
										'tbl_unit_member_id'=>$id,
										'cl_compulsary_periodic_payment_id'=>$cl_compulsary_id[$i],
										'create_date'=>date('Y-m-d H:i:s')
							);
						}
						 $this->db->insert_batch('tbl_unit_compulsary_periodic_payment', $cl_compulsary_periodic_payment_id);
					}
					if(count($cl_facility_id)>0){
						$this->db->delete('tbl_unit_facility_member',array('tbl_unit_member_id'=>$id));
						for($i=0;$i<count($cl_facility_id);$i++){
							$cl_facility_unit_id[]=array(
									'tbl_unit_member_id'=>$id,
									'cl_facility_unit_id'=>$cl_facility_id[$i],
									'qty'=>$qty[$i],
									'create_date'=>date('Y-m-d H:i:s')
							);
						}
						 $this->db->insert_batch('tbl_unit_facility_member', $cl_facility_unit_id);
					}
					if(count($cl_room_id)>0){
						$this->db->delete('tbl_unit_room_type_member',array('tbl_unit_member_id'=>$id));
						for($i=0;$i<count($cl_room_id);$i++){
							$cl_room_type_id[]=array(
									'tbl_unit_member_id'=>$id,
									'cl_room_type_id'=>$cl_room_id[$i],
									'create_date'=>date('Y-m-d H:i:s')
							);
						}
						 $this->db->insert_batch('tbl_unit_room_type_member', $cl_room_type_id);
					}
					if(count($photo_unit_var)>0){
						$this->db->delete('tbl_unit_photo',array('tbl_unit_member_id'=>$id));
						for($i=0;$i<count($photo_unit_var);$i++){
							$photo_unit[]=array(
									'tbl_unit_member_id'=>$id,
									'photo_unit'=>$photo_unit_var[$i],
									'create_date'=>date('Y-m-d H:i:s')
							);
						}
						 $this->db->insert_batch('tbl_unit_photo', $photo_unit);
					}
				}else{
					$this->db->update($table, $data, array('id' => $id) );
				}
			break;
			case "delete":
				if($table=='tbl_unit_member'){
					$this->db->delete('tbl_unit_compulsary_periodic_payment',array('tbl_unit_member_id'=>$id));
					$this->db->delete('tbl_unit_facility_member',array('tbl_unit_member_id'=>$id));
					$this->db->delete('tbl_unit_room_type_member',array('tbl_unit_member_id'=>$id));
					$this->db->delete('tbl_unit_photo',array('tbl_unit_member_id'=>$id));
					$this->db->delete($table, array('id' => $id));
					
				}else{
					$this->db->delete($table, array('id' => $id));
				}
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
