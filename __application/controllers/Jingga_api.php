<?php
require(APPPATH.'/libraries/REST_Controller.php');
 
class Jingga_api extends REST_Controller
{
     
    function user_post()
    {
		
		$method=$this->post('method');
		
		if($method=='update'){
			 $this->set_response(array("Method"=>$method,"msg"=>"ok"), REST_Controller::HTTP_OK);
		}else if($method=='delete'){
			 $this->set_response(array("Method"=>$method,"msg"=>"ok",'post'=>$_POST['method']), REST_Controller::HTTP_OK);
		}else{
			 $this->set_response(array("Method"=>$method,"msg"=>"ok"), REST_Controller::HTTP_OK);
		}
		
		/*
        $result = $this->user_model->update( $this->post('id'), array(
            'name' => $this->post('name'),
            'email' => $this->post('email')
        ));
         
        if($result === FALSE)
        {
            $this->response(array('status' => 'failed'));
        }
         
        else
        {
            $this->response(array('status' => 'success'));
        }
         */
    }
     
}