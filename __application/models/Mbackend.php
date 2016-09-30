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
			case "package_header":
				if($balikan=='get'){$where .=" AND A.id=".$this->input->post('id');}
				else {$where .=" AND A.tbl_services_id=".$this->input->post('id');}
				$sql="SELECT A.*,B.services_name
						FROM tbl_package_header A 
						LEFT JOIN tbl_services B ON A.tbl_services_id=B.id ".$where;
				if($balikan=='get')return $this->db->query($sql)->row_array();
			break;
			case "package_item":
				if($balikan=='get'){$where .=" AND A.id=".$this->input->post('id');}
				else {$where .=" AND A.tbl_package_header_id=".$this->input->post('id');}
				$sql="SELECT 
						CASE WHEN E.id IS NULL THEN '-' 
						ELSE E.services_name 
						END AS header,
						D.services_name as header2,
						C.services_name,B.package_name,C.flag_sum,A.*
						FROM tbl_package_detil A
						LEFT JOIN tbl_package_header B ON A.tbl_package_header_id=B.id
						LEFT JOIN tbl_services C ON A.tbl_services_id=C.id
						LEFT JOIN tbl_services D ON C.pid=D.id
						LEFT JOIN tbl_services E ON D.pid=E.id 
						".$where;
				if($balikan=='get')return $this->db->query($sql)->row_array();
			break;
			case "package_services":
				$id_ser=array();
				if($this->input->post('editstatus')=='add'){
					$sql="SELECT tbl_services_id FROM tbl_package_detil WHERE tbl_package_header_id=".$this->input->post("id_header");
					$ex=$this->db->query($sql)->result_array();
					foreach($ex as $v){
						$id_ser[]=$v['tbl_services_id'];
					}
					if(count($ex)>0){$where .=" AND A.tbl_services_id not in (".join(',',$id_ser).")";}
				}
				$sql="SELECT A.tbl_services_id, 
						CASE WHEN E.id IS NULL THEN '-' 
						ELSE E.services_name 
						END AS header,
						D.services_name as header2,
						C.services_name
						FROM tbl_pricing_services A
						LEFT JOIN tbl_services C ON A.tbl_services_id=C.id
						LEFT JOIN tbl_services D ON C.pid=D.id
						LEFT JOIN tbl_services E ON D.pid=E.id
						".$where."
						GROUP BY A.tbl_services_id 
						ORDER BY A.tbl_services_id ASC ";
			break;
			case "planning":
				if($balikan!="get"){
					$data=array();
					$sql="SELECT A.*,CASE WHEN A.flag='P' THEN 'Planned' ELSE 'Finish' END as flag_desc 
						FROM tbl_execution_transaction A
						WHERE A.tbl_detail_transaction_id=".$this->input->post('id_detil_trans');
					$res=$this->db->query($sql)->result_array();
					$data['data']=$res;
					$data['jml_data']=count($res);
					return $data;
				}else{
					$balikan="row_array";
					$sql="SELECT A.*,CASE WHEN FLAG='P' THEN 'Planned' ELSE 'Finish' END as flag_desc
						FROM tbl_execution_transaction A
						WHERE id=".$this->input->post('id');
					
				}
			break;
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
				$data=array();
				$sts=$this->input->post('editstatus');
				if($sts=='add'){
					//if($this->input->post('flag_tree')!='H'){
						$sql="SELECT * FROM tbl_services WHERE id=".$this->input->post('pid');
						$data['parent']=$this->db->query($sql)->row_array();
					//}
				}else if($sts=='edit'){
					if($this->input->post('flag_tree')!='H'){
						$sql="SELECT * FROM tbl_services WHERE id=".$this->input->post('pid');
						$data['parent']=$this->db->query($sql)->row_array();
					}
					$sql="SELECT * FROM tbl_services WHERE id=".$this->input->post('id');
					$data['child']=$this->db->query($sql)->row_array();
				}
				return $data;
				/*$mod=$this->input->post('mod');
				switch($mod){
					case "housekeeping":$pid=1;break;
					case "linen":$pid=2;break;
					case "check":$pid=3;break;
					case "hosting":$pid=4;break;
					case "full_host":$pid=5;break;
				}
				$sql="SELECT * FROM tbl_services WHERE pid=".$pid;
				*/
				//return $this->lib->json_grid($sql);
			break;
			case "services_all":
				$sql="SELECT * FROM tbl_services WHERE pid IS NULL";
				$data=array();
				$serv=$this->db->query($sql)->result_array();
				$aa=0;
				foreach($serv as $x=>$y){
					$data[$aa]=array(
						'id'=>$y['id'],
						'name'=>'&nbsp;&nbsp;'.$y['code'].'. '.$y['services_name'],
						'services_name'=>$y['services_name'],
						'code'=>$y['code'],
						'desc_services_eng'=>$y['desc_services_eng'],			
						'iconCls'=>'icon-tree-h',	
						'flag_tree'=>'H'
									
					);
					$sql="SELECT * FROM tbl_services WHERE pid = ".$y['id'];
					$ch=$this->db->query($sql)->result_array();
					if(count($ch)>0){
						$data[$aa]['state']='closed';
						$data[$aa]['children']=array();
						foreach($ch as $a=>$b){
							$det=array();
							$det['id']=$b['id'];
							$det['name']='&nbsp;&nbsp;'.$b['code'].'. '.$b['services_name'];
							$det['services_name']=$b['services_name'];
							$det['code']=$b['code'];
							$det['desc_services_eng']=$b['desc_services_eng'];
							$det['iconCls']='icon-tree-d1';
							$det['flag_tree']='D';
							$det['pid']=$y['id'];
							
							$sql="SELECT * FROM tbl_services WHERE pid = ".$b['id'];
							$ch2=$this->db->query($sql)->result_array();
							if(count($ch2)>0){
								$det['state']='closed';
								$det['children']=array();
								foreach($ch2 as $c=>$d){
									$det2=array();
									$det2['id']=$d['id'];
									$det2['name']='&nbsp;&nbsp;'.$d['code'].'. '.$d['services_name'];
									$det2['services_name']=$d['services_name'];
									$det2['code']=$d['code'];
									$det2['desc_services_eng']=$d['desc_services_eng'];
									$det2['flag_tree']='C';
									$det2['pid']=$b['id'];
									$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark 
									FROM tbl_pricing_services WHERE tbl_services_id=".$d['id'];
								
									$price=$this->db->query($sql_pr)->row_array();
									if(isset($price['id_price']))$det2=array_merge($det2,$price);
									
									
									array_push($det['children'],$det2);
								}
								
							}else{
								$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark 
									FROM tbl_pricing_services WHERE tbl_services_id=".$b['id'];
								
								$price=$this->db->query($sql_pr)->row_array();
								if(isset($price['id_price']))$det=array_merge($det,$price);
							}
							array_push($data[$aa]['children'],$det);
							
						}
						
					}
					$aa++;
				}
				//echo "<pre>";
				//print_r($data);exit;
				return json_encode($data);
			break;
			case "services_master":
				if($balikan!='get'){
					if($p1=='package')$where .=' AND type_services=2 ';
					else $where .=' AND type_services=1';
				}else{
					$where .=' AND type_services=2 AND id='.$this->input->post('services_id');
				}
				$sql="SELECT * FROM tbl_services ".$where." AND pid IS NULL ";
				if($balikan=='get'){return $this->db->query($sql)->row_array();}
			break;
			case "services_detil":
				$pid=$this->input->post('id');
				$sql="SELECT * FROM tbl_services WHERE pid =".$pid;
				$res=$this->db->query($sql)->result_array();
				$data=array();
				if(count($res)>0){
					foreach($res as $a=>$b){
						$det=array();
						$det['id']=$b['id'];
						$det['name']='&nbsp;&nbsp;'.$b['code'].'. '.$b['services_name'];
						$det['services_name']=$b['services_name'];
						
						$sql="SELECT * FROM tbl_services WHERE pid =".$b['id'];
						$res_ch=$this->db->query($sql)->result_array();
						if(count($res_ch)>0){
							$det['children']=array();
							foreach($res_ch as $c=>$d){
								$det2=array();
								$det2['id']=$d['id'];
								$det2['name']='&nbsp;&nbsp;'.$d['code'].'. '.$d['services_name'];
								$det2['services_name']=$d['services_name'];
								
								
								$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark 
										FROM tbl_pricing_services WHERE tbl_services_id=".$d['id'];
								$price=$this->db->query($sql_pr)->result_array();
								if(count($price)>0){
									$det2['price']=$price;
									//array_push($det['price'],$price);
								}
								array_push($det['children'],$det2);
							}
						}
						else{
							$sql_pr="SELECT id as id_price,tbl_services_id,of_unit,of_area_item,percen,rate,type,remark 
									FROM tbl_pricing_services WHERE tbl_services_id=".$b['id'];
							$price=$this->db->query($sql_pr)->result_array();
							if(count($price)>0){
								$det['price']=$price;
								//array_push($det['price'],$price);
							}
						}
						array_push($data,$det);
					}
				}
				//echo "<pre>";print_r($data);
				return $data;
			break;
			case "pricing":
				$sts=$this->input->post('editstatus');
				$data=array();
				
				if($sts=='edit'){
					$sql="SELECT A.*,B.services_name 
					FROM tbl_pricing_services A 
					LEFT JOIN tbl_services B ON A.tbl_services_id=B.id 
					WHERE A.id=".$this->input->post('id_price');
					$data['price']=$this->db->query($sql)->row_array();
				}else{
					$sql="SELECT * FROM tbl_services WHERE id=".$this->input->post('id_parent');
					$data['price']=$this->db->query($sql)->row_array();
				}
				return $data;
			break;
			case "invoice":
				if($balikan=='get'){
					$data=array();
					$sql="SELECT A.*,CONCAT(C.title,' ',C.owner_name_first,' ',C.owner_name_last)as nama,
							E.apartment_name,D.method_payment 
							FROM tbl_header_transaction A
							LEFT JOIN tbl_member B ON A.tbl_member_user=B.member_user
							LEFT JOIN tbl_registration C ON B.tbl_registration_id=C.id
							LEFT JOIN cl_method_payment D ON A.cl_method_payment_id=D.id
							LEFT JOIN tbl_unit_member E ON A.tbl_unit_member_id=E.id 
							WHERE A.id=".$this->input->post('id');
					$data["header"]=$this->db->query($sql)->row_array();
					if(isset($data["header"]['id'])){
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
						$data["detil"]=$this->db->query($sql)->result_array();
					}
					return $data;
				}else{
					$sql="SELECT A.*,CONCAT(D.title,' ',D.owner_name_first,' ',D.owner_name_last)as name,B.method_payment
						FROM tbl_header_transaction A
						LEFT JOIN cl_method_payment B ON A.cl_method_payment_id=B.id
						LEFT JOIN tbl_member C ON A.tbl_member_user=C.member_user
						LEFT JOIN tbl_registration D ON C.tbl_registration_id=D.id ".$where." ORDER BY A.date_invoice DESC";
				}
			break;
			case "invoice_package":
				if($balikan=='get'){
					$data=array();
					$sql="SELECT A.*,CONCAT(D.title,' ',D.owner_name_first,' ',D.owner_name_last)as name,
							B.method_payment,F.package_name,F.package_desc,
							E.apartment_name,E.apartment_address
							FROM tbl_transaction_package A
							LEFT JOIN cl_method_payment B ON A.cl_method_payment_id=B.id
							LEFT JOIN tbl_member C ON A.tbl_member_user=C.member_user
							LEFT JOIN tbl_registration D ON C.tbl_registration_id=D.id 
							LEFT JOIN tbl_unit_member E ON A.tbl_unit_member_id=E.id
							LEFT JOIN tbl_package_header F ON A.tbl_package_header_id=F.id
							WHERE A.id=".$this->input->post('id');
					$data["header"]=$this->db->query($sql)->row_array();
					if(isset($data["header"]['id'])){
						$sql="SELECT 
								CASE WHEN E.id IS NULL THEN '-' 
								ELSE E.services_name 
								END AS header,
								D.services_name as header2,
								C.services_name,B.package_name,C.flag_sum,A.*
								FROM tbl_package_detil A
								LEFT JOIN tbl_package_header B ON A.tbl_package_header_id=B.id
								LEFT JOIN tbl_services C ON A.tbl_services_id=C.id
								LEFT JOIN tbl_services D ON C.pid=D.id
								LEFT JOIN tbl_services E ON D.pid=E.id
							WHERE A.tbl_package_header_id=".$data["header"]['tbl_package_header_id'];
						$res=$this->db->query($sql)->result_array();
						$data["detil"]=$res;
						
					}
					return $data;
				}else{
					$sql="SELECT A.*,CONCAT(D.title,' ',D.owner_name_first,' ',D.owner_name_last)as name,
						B.method_payment,F.package_name,F.package_desc,
						E.apartment_name,E.apartment_address
						FROM tbl_transaction_package A
						LEFT JOIN cl_method_payment B ON A.cl_method_payment_id=B.id
						LEFT JOIN tbl_member C ON A.tbl_member_user=C.member_user
						LEFT JOIN tbl_registration D ON C.tbl_registration_id=D.id 
						LEFT JOIN tbl_unit_member E ON A.tbl_unit_member_id=E.id
						LEFT JOIN tbl_package_header F ON A.tbl_package_header_id=F.id
						".$where." ORDER BY A.date_invoice DESC";
				}
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
	function get_child($id){
		$rs = $this->db->get_where("tbl_services",array('pid'=>$id))->result_array();
		if(count($rs) > 0){
			return $rs;
		}else{
			return 'closed';
		}
		//return count($rs) > 0 ? true : false;
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
			case "planning":
				$table='tbl_execution_transaction';
				if($sts_crud=='edit'){
					if(isset($data['flag'])){$data['finish_date']=date('Y-m-d H:i:s');}
				}
				if($sts_crud=='add'){$data['flag']='P';}
				//echo $sts_crud;exit;
				//print_r($_POST);exit;
			break;
			case "package":
				$table='tbl_package_header';
				if($sts_crud=='delete'){
					$this->db->delete('tbl_package_detil',array('tbl_package_header_id'=>$id));
				}
			break;
			case "package_item":
				$table='tbl_package_detil';
			break;
			
			case "services":
				$table='tbl_services';
				if($sts_crud=='add_new')$sts_crud='add';$data['flag']='F';
				if($sts_crud=='delete'){
					$sql="SELECT * FROM tbl_services where pid=".$id;
					$ex=$this->db->query($sql)->result_array();
					if(count($ex)>0){
						foreach($ex as $v){
							$this->db->delete('tbl_services',array('pid'=>$v['id']));
						}
					}
					$this->db->delete('tbl_services',array('pid'=>$id));
				}
				//print_r($data);exit;
			break;
			case "pricing":
				$table='tbl_pricing_services';
			break;
		}
		
		switch ($sts_crud){
			case "add":
				if($table!='tbl_registration'){
					$data['create_date'] = date('Y-m-d H:i:s');
					$data['create_by'] = $this->auth['nama_user'];
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
			 return $this->db->trans_commit();
		}
	}
	
}
