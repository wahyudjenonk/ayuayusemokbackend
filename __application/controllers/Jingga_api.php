<?php
require(APPPATH.'/libraries/REST_Controller.php');
 
class Jingga_api extends REST_Controller
{
     
    function jingga_post(){
		//$this->set_response(array('data'=>$this->post('modul')), REST_Controller::HTTP_OK);
		$method=$this->post('method');
		$modul=$this->post('modul');
		$sub_modul=$this->post('submodul');
		//$this->set_response($modul, REST_Controller::HTTP_OK);
		//$msg=array('data'=>$_POST);
		//$this->set_response($msg, REST_Controller::HTTP_OK);
		
		switch($method){
			case "create":$editstatus="add";$this->simpandata($modul,$sub_modul,$editstatus);
			break;
			case "read":
				if($modul!='login')$this->getdata($modul,$sub_modul);
				else $this->login();
			break;
			case "update":$editstatus="edit";$this->simpandata($modul,$sub_modul,$editstatus);break;
			case "delete":$editstatus="delete";$this->simpandata($modul,$sub_modul,$editstatus);break;
		}
    }
    function getdata($p1,$p2=""){
		//$this->set_response($p2, REST_Controller::HTTP_OK);
		$msg=$this->mjingga_api->get_data($p1,$p2);
		$this->set_response($msg, REST_Controller::HTTP_OK);
	}
	function simpandata($p1="",$p2="",$editstatus){
		//$this->set_response('XXxxx', REST_Controller::HTTP_OK);
		
		$post = array();
        foreach($_POST as $k=>$v){
			if($this->input->post($k)!=""){
				$post[$k] = $this->db->escape_str($this->input->post($k));
			}else{
				$post[$k] = null;
			}
		}

		//$msg=array('data'=>$_POST);
		//$this->set_response($msg, REST_Controller::HTTP_OK);exit;

		unset($post['method']);unset($post['modul']);unset($post['submodul']);
		$msg=$this->mjingga_api->simpandata($p1, $post, $editstatus);
		$this->set_response($msg, REST_Controller::HTTP_OK);
	}
	public function login(){
		
		$this->load->library(array('encrypt'));
		$user = $this->db->escape_str($this->input->post('member_user'));
		$email = $this->db->escape_str($this->input->post('email_address'));
		$pass = $this->db->escape_str($this->input->post('pwd'));
		
		
		if($user==''){$user=$email;}
		$resp=array();
		
		
		
		if($user && $pass){
			$this->set_response(array('data'=>$_POST), REST_Controller::HTTP_OK);
			$cek_user = $this->mjingga_api->get_data('data_login', 'row_array', $user);
			
			//$this->set_response(array('data'=>$cek_user), REST_Controller::HTTP_OK);
			if(count($cek_user)>0){
				if(isset($cek_user['flag']) && $cek_user['flag']==1){
					if($pass == $this->encrypt->decode($cek_user['pwd'])){
						//$this->session->set_userdata('44mpp3R4', base64_encode(serialize($cek_user)));
						$resp['msg']='sukses';
						$resp['data']=$cek_user;
						$resp['pesan']='';
					}else{
						$resp['msg']='gagal';
						$resp['data']='';
						$resp['pesan']='Incorrect Password';
					}
				}else{
					$resp['msg']='gagal';
					$resp['data']='';
					$resp['pesan']='User Not Active';
				}
			}else{
				$resp['msg']='gagal';
				$resp['data']='';
				$resp['pesan']='User Not Registered';
			}
		}else{
			$resp['msg']='gagal';
			$resp['data']='';
			$resp['pesan']='Fill Username/Email & Password';
		}
		$this->set_response($resp, REST_Controller::HTTP_OK);
		
	}
	
	
	
	
}