<?php
use Restserver \Libraries\REST_Controller ;
defined('BASEPATH' ) or exit('No direct script access allowed');

class BREST_Controller extends REST_Controller
{
    public function __construct ()
    {
        parent::__construct ();
        // Your own constructor code
    }
    
    public function send_success ($data, $message = "success") {
       
         $root = [
        'success' => true,
        'data' => $data,
        'message' => $message
        ];

        $this->response($root, 200);
    }

    public function send_error ($data, $message = "fail") {
       
        $root = [
        'success' => false,
        'data' => $data,
        'message' => $message
        ];

        $this->response($root, 401);
    }
}
