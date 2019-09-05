<?php

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Users extends CI_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function index_post()
    {
        // Post data
        $user_name = $this->post('name');
        $user_age = $this->post('age');
        $user_gender = $this->post('gender');
        $user_address = $this->post('address');
        $user_email = $this->post('email');

        // Find user with same email ID

        // Show last active period, if user with same email is deleted
        // 200

        // Create a new user if email not found
        // 201
    }
}