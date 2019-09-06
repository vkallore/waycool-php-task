<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Dashboard extends MY_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function user_login_list_get() {
        $user_id = $this->rest->user_id;
        $user_level = $this->rest->level;

        // Not an admin user
        // if($user_level !== 0) {
        //     // Response with 401
        //     $this->response([
        //         'message' => lang('text_rest_dash_invalid_request'),
        //     ], 401);
        // }

        $login_list = Login_logs_model::login_list();

        $response = [
            'data' => $login_list
        ];
        if(empty($login_list)) {
            $response['message'] = lang('text_rest_no_records');
        }
        $this->response($response, 200);
    }
}