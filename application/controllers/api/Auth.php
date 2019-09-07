<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Auth extends MY_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    /**
     * Login POST request
     */
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
                'message' => lang('text_rest_invalid_login'),
            ], 400);
        }

        // CAN DO
        // Look for active API key, and send back it
        // Instead of processing rest

        $user_password = $user->password;

        if(!password_verify($password, $user_password)) {
            $this->response([
                'message' => lang('text_rest_invalid_credentials'),
            ], 401);
        }

        $user_id = $user->id;

        // Start transaction
        $this->db->trans_start();

        // Log the login when not admin
        if($user->user_type != 0) {
            $login_log = Login_logs_model::log($user_id);
            if($login_log !== true) {
                $message = lang('text_rest_error_while');
                $message = sprintf($message, 'login');
                $this->response([
                    'message' => $message,
                ], 500);
            }
        }

        // Create API Key - BASIC
        $key = random_string(30);
        $key_created = Keys_model::create([
            'user_id' => $user_id,
            'key' => $key,
            'level' => $user->user_type,
            'ignore_limits' => 0,
        ]);

        if($key_created !== true) {
            $message = lang('text_rest_error_while');
            $message = sprintf($message, 'login');
            $this->response([
                'message' => $message,
            ], 500);
        }

        // End transaction
        $this->db->trans_complete();

        if( $this->db->trans_status() !== false ){
            // Response with 200
            $this->response([
                'api_key' => $key,
                'message' => lang('text_rest_login_success'),
            ], 200);
        } else {
            // Response with 500
            $this->response([
                'message' => lang('text_rest_common_error'),
            ], 500);
        }
    }

    public function social_login_post() {
        // G & FB
    }
}