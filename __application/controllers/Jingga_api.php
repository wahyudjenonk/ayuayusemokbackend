<?php
require(APPPATH.'/libraries/REST_Controller.php');
 
class Jingga_api extends REST_Controller
{
    function user_get()
    {
        if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }
 
        $user = $this->user_model->get( $this->get('id') );
         
        if($user)
        {
           $this->set_response(array("user"=>$user,"msg"=>"ok"), REST_Controller::HTTP_OK);
        }
 
        else
        {
            $this->response(NULL, 404);
        }
    }
     
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
     
    function users_get()
    {
		// $this->response(array('status' => $this->get('aa')));
		//$met=$this->post('method');
		if(!$this->get('aa'))
        {
            $this->response(NULL, 400);
        }
 
        $users = $this->user_model->get_all();
         
        if(count($users)>0)
        {
             $this->set_response(array("user"=>$users,"msg"=>"ok"), REST_Controller::HTTP_OK);
        }
 
        else
        {
            $this->response(NULL, 404);
        }
    }
	function user_put()
    {       
		if(!$this->get('id'))
        {
            $this->response(NULL, 400);
        }
        $data = array('returned: '. $this->put('id'));
        $this->response($data);
    }
	 function user_delete()
    {
		 $id = (int) $this->get('xx');

        // Validate the id.
        /*if ($id <= 0)
        {
            // Set the response and exit
            $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }*/

        // $this->some_model->delete_something($id);
        $message = array(
            'id' => $id,
            'message' => 'Deleted the resource'
        );

        $this->set_response($message, REST_Controller::HTTP_NO_CONTENT); // NO_CONTENT (204) being the HTTP response code
    }
}