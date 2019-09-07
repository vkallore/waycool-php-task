<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Auth extends MY_Controller
{

    private static $user = null;

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

        self::$user = $user;

        $user_password = $user->password;

        if(!password_verify($password, $user_password)) {
            $this->response([
                'message' => lang('text_rest_invalid_credentials'),
            ], 401);
        }

        self::_login();
    }

    /**
     * Allo social login if mapped to an account
     */
    public function social_login_post() {
        // Put data
        $social_uid = $this->post('social_uid');
        $social_site = $this->post('social_site');

        // Validation
        // TODO

        $social_site = strtolower($social_site);
        $social_data = [
            "{$social_site}_uid" => $social_uid,
        ];

        $find_where = array_merge($social_data, ['is_deleted' => 0]);

        // Find user with social linked
        $user = Users_model::find_user($find_where);

        if(empty($user)) {
            $this->response([
                'message' => lang('text_rest_invalid_social_login'),
            ], 401);
        }

        self::$user = $user;

        self::_login();
    }

    /**
     * Process login process
     * common to all types of login
     */
    private function _login() {
        $user = self::$user;

        $user_id = $user->id;

        // CAN DO
        // Look for active API key, and send back it
        // Instead of processing rest

        // Start transaction
        $this->db->trans_start();

        $user_type = $user->user_type;
        // Log the login when not admin
        if($user_type != 0) {
            $login_log = Login_logs_model::log($user_id);
            if($login_log !== true) {
                $message = lang('text_rest_error_while');
                $message = sprintf($message, 'login');
                $this->response([
                    'message' => $message,
                ], 500);
            }
        } else {
            $user_type = 1;
        }

        // Create API Key - BASIC
        $key = random_string(30);
        $key_created = Keys_model::create([
            'user_id' => $user_id,
            'key' => $key,
            'level' => $user_type,
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
}