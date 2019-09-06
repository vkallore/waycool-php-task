<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Auth extends MY_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function login_post() {
        // Post data
        $userid = $this->post('username');
        $password = $this->post('password');

        // Find user with same email ID
        $user = Users_model::find_user([
            'userid' => $userid,
            'is_deleted' => 0,
        ]);

        // Show last active period, if user with same email is deleted
        if(empty($user)) {
            $this->response([
                'message' => "Invalid login. Please try again.",
            ], 400);
        }

        $user_password = $user->password;

        if(!password_verify($password, $user_password)) {
            $this->response([
                'message' => "Invalid credentials. Please try again.",
            ], 401);
        }

        // Start transaction
        $this->db->trans_start();

        // Log the email login
        // TODO

        // Create API Key - BASIC/No tie up with user at the moment
        $key = random_string(30);
        $key_created = Keys_model::create([
            'key' => $key,
            'level' => 1,
            'ignore_limits' => 0,
        ]);

        if($key_created !== true) {
            $this->response([
                'message' => 'Error occurred while login! Please try again.',
            ], 500);
        }

        // End transaction
        $this->db->trans_complete();

        if( $this->db->trans_status() !== false ){
            // Response with 200
            $this->response([
                'api_key' => $key,
                'message' => 'Login successfull.',
            ], 200);
        } else {
            // Response with 500
            $this->response([
                'message' => 'Error occurred! Please try again.',
            ], 500);
        }
    }
}