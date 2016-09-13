<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

class Backend extends JINGGA_Controller {
	
	function __construct(){
		parent::__construct();
		if(!$this->auth){
			$this->nsmarty->display('backend/main-login.html');
			exit;
		}
		$this->nsmarty->assign('acak', md5(date('H:i:s')) );
		$this->temp="backend/";
		$this->load->model('mbackend');
		$this->load->library(array('encrypt','lib'));
	}
	
	function index(){
		if($this->auth){
			$this->nsmarty->display( 'backend/main-backend.html');
		}else{
			$this->nsmarty->display( 'backend/main-login.html');
		}
	}
	
	function modul($p1,$p2){
		if($this->auth){
			switch($p1){
				case "beranda":
					
				break;
			}
			
			$this->nsmarty->assign("main", $p1);
			$this->nsmarty->assign("mod", $p2);
			$temp = 'backend/modul/'.$p1.'/'.$p2.'.html';
			if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
			else{$this->nsmarty->display($temp);}	
		}
	}	
	
	function get_grid($mod){
		$temp = 'backend/modul/grid_config.html';
		$filter=$this->combo_option($mod);
		$this->nsmarty->assign('data_select',$filter);
		$this->nsmarty->assign('mod',$mod);
		if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
		else{$this->nsmarty->display($temp);}
	}
	
	
	function getdisplay($type="", $p1="", $p2=""){
		$display = false;
		switch($type){
			case "get-form":
				$sts_crud = $this->input->post('editstatus');
				$this->nsmarty->assign("acak", md5(date('YmdHis').'ind') );				
				
				$table = $this->input->post('ts');
				if($sts_crud == 'edit'){	
					$id = $this->input->post('id');
					if($table == 'warkom'){
						$tabel = 'tbl_warta';
					}elseif($table == 'kokom'){
						$tabel = 'cl_komisi';
					}
					$data = $this->db->get_where($tabel, array('id'=>$id) )->row_array();
					$this->nsmarty->assign('data', $data);
				}
				
				switch($table){
					case "warkom":
						$field = $this->db->list_fields("tbl_warta");
						$this->nsmarty->assign('legend', "WARTA");
					break;
				}
				
				$arrayform = array();
				$i = 0;
				foreach($field as $k => $v){							
					if($v == 'create_date' || $v == 'create_by'){
						continue;
					}
					
					$label = str_replace('_', ' ', $v);
					$label = strtoupper($label);
					
					if($v == 'id'){
						$arrayform[$k]['tipe'] = "hidden";
					}else{	
						if(strpos($v, 'cl_') !== false){
							$label = str_replace("CL ", "", $label);
							$label = str_replace(" ID", "", $label);
							
							$arrayform[$k]['tipe'] = "combo";
							$arrayform[$k]['ukuran_class'] = "span4";
							$arrayform[$k]['isi_combo'] =  $this->lib->fillcombo($v, 'return', ($sts_crud == 'edit' ? $data[$v] : "") );
						}elseif(strpos($v, 'tipe_') !== false){
							$arrayform[$k]['tipe'] = "combo";
							$arrayform[$k]['ukuran_class'] = "span4";
							$arrayform[$k]['isi_combo'] =  $this->lib->fillcombo($v, 'return', ($sts_crud == 'edit' ? $data[$v] : "") );
						}elseif(strpos($v, 'tgl_') !== false){
							$label = str_replace("TGL", "TANGGAL", $label);
							
							$arrayform[$k]['tipe'] = "text";
							$arrayform[$k]['ukuran_class'] = "span2";
						}elseif(strpos($v, 'isi_') !== false){
							$arrayform[$k]['tipe'] = "textarea";
							$arrayform[$k]['ukuran_class'] = "span8";
						}elseif(strpos($v, 'gambar_') !== false){
							$arrayform[$k]['tipe'] = "file";
							$arrayform[$k]['ukuran_class'] = "span8";	
						}else{
							$arrayform[$k]['tipe'] = "text";
							$arrayform[$k]['ukuran_class'] = "span8";
						}
					}
												
					$arrayform[$k]['name'] = $v;
					$arrayform[$k]['label'] = $label;
					$i++;
				}
				//echo "<pre>"; print_r($arrayform); exit;
				
				$this->nsmarty->assign('arrayform', $arrayform);
						
				$this->nsmarty->assign("main", $p1);
				$this->nsmarty->assign("acak_form", md5(date('H:i:s')) );
				$this->nsmarty->assign("sts_crud", $sts_crud);
				$this->nsmarty->assign("table", $table);
				$temp = 'backend/modul/form_config.html';
				$display = true;
				if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
			break;
		}
		
		if($display == true){
			$this->nsmarty->display($temp);
		}
	}	
	function get_konten(){
		$mod=$this->input->post('mod');
		if($this->input->post('table'))$mod=$this->input->post('table');
		//echo $mod;
		$this->nsmarty->assign('mod',$mod);
		$temp="backend/modul/".$mod.".html";
		switch($mod){
			case "registration":
				$data=$this->mbackend->getdata('registration','get');
				$this->nsmarty->assign('data',$data);
			break;
			case "member":
				$data=$this->mbackend->getdata('member','get');
				$this->nsmarty->assign('data',$data);
			break;
			case "property":
				$data=$this->mbackend->getdata('property','get');
				$this->nsmarty->assign('data',$data);
			break;
		}
		$this->nsmarty->assign('temp',$temp);
		if(!file_exists($this->config->item('appl').APPPATH.'views/'.$temp)){$this->nsmarty->display('konstruksi.html');}
		else{$this->nsmarty->display($temp);}
	}
	function getdata($p1,$p2="",$p3=""){
		echo $this->mbackend->getdata($p1,'json',$p3);
	}
	
	function simpandata($p1="",$p2=""){
		if($this->input->post('mod'))$p1=$this->input->post('mod');
		$post = array();
        foreach($_POST as $k=>$v){
			if($this->input->post($k)!=""){
				//$post[$k] = $this->db->escape_str($this->input->post($k));
				$post[$k] = $this->input->post($k);
			}
			
		}
		if(isset($post['editstatus'])){$editstatus = $post['editstatus'];unset($post['editstatus']);}
		else $editstatus = $p2;
		
		echo $this->mbackend->simpandata($p1, $post, $editstatus);
	}
	
	function test(){
		$a = 'cl_komisi_id';
		if (strpos($a, 'tipe') !== false) {
			echo 'true';
		}
	}
	function combo_option($mod){
		$opt="";
		switch($mod){
			case "registration":
				$opt .="<option value='A.email'>Email</option>";
				$opt .="<option value='A.owner_name_last'>Last Name</option>";
				$opt .="<option value='A.owner_name_first'>First Name</option>";
				$opt .="<option value='A.id_number'>ID Number</option>";
				$opt .="<option value='A.company_name'>Company Name</option>";
			break;
			case "registration":
				$opt .="<option value='A.email_address'>Email</option>";
				$opt .="<option value='B.owner_name_last'>Last Name</option>";
				$opt .="<option value='B.owner_name_first'>First Name</option>";
				$opt .="<option value='B.id_number'>ID Number</option>";
				$opt .="<option value='B.company_name'>Company Name</option>";
			break;
			case "property":
				$opt .="<option value='C.owner_name_first'>First Name</option>";
				$opt .="<option value='C.owner_name_last'>Last Name</option>";
				$opt .="<option value='A.apartment_name'>Apartment Name</option>";
			break;
			case "housekeeping":
			case "check":
			case "hosting":
			case "linen":
			case "full_host":
				$opt .="<option value='services_name'>Services Name</option>";
			break;
		}
		return $opt;
	}
}
