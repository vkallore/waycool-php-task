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
     * User object
     */
    private static $user = null;

    /**
     * Login POST request
     */
    public function login_post() {
        // Validation
        $validation_rules = [
            [
                'field' => 'username',
                'label' => 'Username',
                'rules' => 'required',
            ],
            [
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'required',
            ],
        ];

        $this->validate_data($validation_rules);

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

        // Validation
        $validation_rules = [
            [
                'field' => 'social_uid',
                'label' => 'Social profile ID',
                'rules' => 'required',
            ],
            [
                'field' => 'social_site',
                'label' => 'Social site',
                'rules' => 'required',
            ],
        ];

        $this->validate_data($validation_rules);

        // Post data
        $social_uid = $this->post('social_uid');
        $social_site = $this->post('social_site');

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

        self::_login($social_site);
    }

    /**
     * Process login process
     * common to all types of login
     */
    private function _login($login_type = 'Email') {
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
            $login_log = Login_logs_model::log($user_id, $login_type);
            if($login_log !== true) {
                $message = lang('text_rest_error_while');
                $message = sprintf($message, 'login');
                $this->response([
                    'message' => $message,
                ], 500);
            }
        } else if($user_type == null) {
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

        if( $this->db->trans_status() !== false ) {
            // Response with 200
            $this->response([
                'api_key' => $key,
                'is_admin' => $user_type == 0,
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