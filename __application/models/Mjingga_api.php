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
			case "invoice":
				$sql="SELECT A.*,CONCAT(D.title,' ',D.owner_name_first,' ',D.owner_name_last)as name,B.method_payment,
						E.apartment_name,E.apartment_address
						FROM tbl_header_transaction A
						LEFT JOIN cl_method_payment B ON A.cl_method_payment_id=B.id
						LEFT JOIN tbl_member C ON A.tbl_member_user=C.member_user
						LEFT JOIN tbl_registration D ON C.tbl_registration_id=D.id 
						LEFT JOIN tbl_unit_member E ON A.tbl_unit_member_id=E.id
						";
				if($balikan=='detil'){
					$data=array();
					$sql .=" WHERE A.id=".$this->input->post('id');
					$sql .=" ORDER BY A.date_invoice DESC";
					//return $msg=array('msg'=>'sukses','data'=>$sql);
					$data['header']=$this->db->query($sql)->row_array();
					$sql="SELECT A.*,C.services_name,B.of_unit,B.of_area_item,B.percen,B.rate,
								CASE 
								WHEN A.flag_transaction='H' THEN 'Hourly'
								WHEN A.flag_transaction='M' THEN 'Weekly'
								ELSE 'Mothly'
								END as type_serv
								FROM tbl_detail_transaction A
								LEFT JOIN tbl_pricing_services B ON A.tbl_pricing_services_id=B.id
								LEFT JOIN tbl_services C ON B.tbl_services_id=C.id
								WHERE A.tbl_header_transaction_id=".$data["header"]['id'];
					$data['detil']=$this->db->query($sql)->result_array();
					
					return $msg=array('msg'=>'sukses','data'=>$data);
				}else{
					$sql .=" ORDER BY A.date_invoice DESC";
					return $msg=array('msg'=>'sukses','data'=>$this->db->query($sql)->result_array());
				}		
						
				
			break;
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
					LEFT JOIN (SELECT tbl_unit_member_id, photo_unit FROM tbl_unit_photo GROUP BY tbl_unit_member_id)B ON B.tbl_unit_member_id = A.id
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
			case "services_header":
				$sql="SELECT * FROM tbl_services WHERE pid IS NULL";
				$data=array();
				$serv=$this->db->query($sql)->result_array();
				return $serv;
			break;
			case "services_detil":
				$sql="SELECT * FROM tbl_services WHERE pid =".$this->input->post('id');
				$data=array();
				$serv=$this->db->query($sql)->result_array();
				foreach($serv as $x=>$y){
					$data[$y['id']]=array(
						'id'=>$y['id'],
						'services_name'=>$y['services_name'],
						'code'=>$y['code'],
						'desc_services_eng'=>$y['desc_services_eng']			
					);
					$sql="SELECT * FROM tbl_services WHERE pid = ".$y['id'];
					$ch=$this->db->query($sql)->result_array();
					if(count($ch)>0){
						$data[$y['id']]['child']=array();
						foreach($ch as $a=>$b){
							$det=array();
							$det['id']=$b['id'];
							$det['services_name']=$b['services_name'];
							$det['code']=$b['code'];
							$det['desc_services_eng']=$b['desc_services_eng'];
							
							$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark 
									FROM tbl_pricing_services WHERE tbl_services_id=".$b['id'];
								
							$price=$this->db->query($sql_pr)->result_array();
							if(count($price)>0){$det['price']=$price;}
							/*$sql="SELECT * FROM tbl_services WHERE pid = ".$b['id'];
							$ch2=$this->db->query($sql)->result_array();
							if(count($ch2)>0){
								$det['child']=array();
								foreach($ch2 as $c=>$d){
									$det2=array();
									$det2['id']=$d['id'];
									$det2['services_name']=$d['services_name'];
									$det2['code']=$d['code'];
									$det2['desc_services_eng']=$d['desc_services_eng'];
									
									$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark 
									FROM tbl_pricing_services WHERE tbl_services_id=".$d['id'];
								
									$price=$this->db->query($sql_pr)->result_array();
									if(count($price)>0){$det2['price']=$price;}
									array_push($det['child'],$det2);
								}
								
							}else{
								$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark 
									FROM tbl_pricing_services WHERE tbl_services_id=".$b['id'];
								
								$price=$this->db->query($sql_pr)->result_array();
								if(count($price)>0){$det['price']=$price;}
							}*/
							array_push($data[$y['id']]['child'],$det);
							
						}
						
					}else{
						$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark 
									FROM tbl_pricing_services WHERE tbl_services_id=".$y['id'];
								
						$price=$this->db->query($sql_pr)->result_array();
						if(count($price)>0){$data[$y['id']]['price']=$price;}
					}
				}
				return $msg=array('msg'=>'sukses','data'=>$data);
			break;
			case "services":
				$sql="SELECT * FROM tbl_services WHERE pid IS NULL AND type_services='".$this->input->post('type_services')."'";
				$data=array();
				$serv=$this->db->query($sql)->result_array();
				foreach($serv as $x=>$y){
					$data[$y['id']]=array(
						'id'=>$y['id'],
						'services_name'=>$y['services_name'],
						'code'=>$y['code'],
						'desc_services_eng'=>$y['desc_services_eng']			
					);
					$sql="SELECT * FROM tbl_services WHERE pid = ".$y['id'];
					$ch=$this->db->query($sql)->result_array();
					if(count($ch)>0){
						$data[$y['id']]['child']=array();
						foreach($ch as $a=>$b){
							$det=array();
							$det['id']=$b['id'];
							$det['services_name']=$b['services_name'];
							$det['code']=$b['code'];
							$det['desc_services_eng']=$b['desc_services_eng'];
							$sql="SELECT * FROM tbl_services WHERE pid = ".$b['id'];
							$ch2=$this->db->query($sql)->result_array();
							if(count($ch2)>0){
								$det['child']=array();
								foreach($ch2 as $c=>$d){
									$det2=array();
									$det2['id']=$d['id'];
									$det2['services_name']=$d['services_name'];
									$det2['code']=$d['code'];
									$det2['desc_services_eng']=$d['desc_services_eng'];
									
									$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark,type_choice,uom 
									FROM tbl_pricing_services WHERE tbl_services_id=".$d['id'];
								
									$price=$this->db->query($sql_pr)->result_array();
									if(count($price)>0){$det2['price']=$price;}
									array_push($det['child'],$det2);
								}
								
							}else{
								$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark,type_choice,uom 
									FROM tbl_pricing_services WHERE tbl_services_id=".$b['id'];
								
								$price=$this->db->query($sql_pr)->result_array();
								if(count($price)>0){$det['price']=$price;}
							}
							array_push($data[$y['id']]['child'],$det);
							
						}
						
					}
				}
				return $msg=array('msg'=>'sukses','data'=>$data);
			break;
			
			case "pricing_pilih":
				$pilih=$this->input->post('tbl_services_id');
				if($pilih){
					$sql="SELECT A.*,B.services_name 
							FROM tbl_pricing_services A 
							LEFT JOIN tbl_services B ON A.tbl_services_id=B.id
							WHERE A.id IN (".join(',',$pilih).") ";
					$msg=array('msg'=>'sukses','data'=>$this->db->query($sql)->result_array());		
					return $msg;
				}
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
			case "transaction":
				//return $msg['msg'] =$_POST;
				$table="tbl_header_transaction";
				$tbl_services_id=array();
				$services_id=array();
				$ex=$this->db->get_where('tbl_header_transaction',array('tbl_member_user'=>$data['tbl_member_user']))->row_array();
				if(isset($ex['id'])){
					$sql="SELECT MAX(SUBSTRING(no_invoice FROM 12 FOR 5)) + 1 as no_baru FROM tbl_header_transaction WHERE tbl_member_user='".$data['tbl_member_user']."'";
					$id=$this->db->query($sql)->row('no_baru');
				}else{
					$id=1;
				}
				if($id<10){$id_baru='0000'.$id;}
				if($id<100 && $id >=10){$id_baru='000'.$id;}
				if($id<1000 && $id >=100){$id_baru='00'.$id;}
				if($id<10000 && $id >=1000){$id_baru='0'.$id;}
				$no_inv='INV-'.$data['tbl_member_user'].'-'.$id_baru;
				$data['no_invoice']=$no_inv;
				$data['date_invoice']=date('Y-m-d H:i:s');
				
				if(isset($data['tbl_pricing_services_id'])){
					$tbl_services_id=$data['tbl_pricing_services_id'];unset($data['tbl_pricing_services_id']);
					$qty=$data['qty'];unset($data['qty']);
					$total=$data['total'];unset($data['total']);
					$flag_transaction=$data['flag_transaction'];unset($data['flag_transaction']);
				}
				
				
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
					
				}else if($table=='tbl_header_transaction'){
					$this->db->insert($table,$data);//INSERT HEADER TRANSACTION;
					$id_header=$this->db->insert_id();
					if(count($tbl_services_id)>0){
						for($i=0;$i<count($tbl_services_id);$i++){
							$services_id[]=array(
									'tbl_header_transaction_id'=>$id_header,
									'tbl_pricing_services_id'=>$tbl_services_id[$i],
									'qty'=>$qty[$i],
									'total'=>$total[$i],
									'flag_transaction'=>$flag_transaction[$i],
									
							);
						}
						 $this->db->insert_batch('tbl_detail_transaction', $services_id);
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
