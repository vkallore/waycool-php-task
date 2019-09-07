<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Dashboard extends MY_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    /**
     * Validate admin key & level
     */
    private function _validate_admin() {
        $user_id = $this->rest->user_id;
        $user_level = $this->rest->level;

        // Not an admin user
        if($user_level != 0) {
            // Response with 401
            $this->response([
                'message' => lang('text_rest_dash_invalid_request'),
            ], 401);
        }
    }

    /**
     * Users, their login count by login type
     */
    public function user_login_list_get() {

        $this->check_and_set_pagination_data();

        $this->_validate_admin();

        $login_logs_by_type = Login_logs_model::login_logs_by_type();

        $this->api_meta_response($login_logs_by_type);
    }

    /**
     * Users, Deleted accounts
     */
    public function deleted_accounts_get() {

        $this->check_and_set_pagination_data();
        $this->_validate_admin();

        $deleted_accounts = Users_model::deleted_accounts();

        $this->api_meta_response($deleted_accounts);
    }

    /**
     * Users, their login actions
     */
    public function user_login_logs_get() {

        $this->check_and_set_pagination_data();

        $this->_validate_admin();

        $login_logs = Login_logs_model::login_logs();

        $this->api_meta_response($login_logs);
    }
}