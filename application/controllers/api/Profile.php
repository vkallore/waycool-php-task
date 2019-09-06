<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Profile extends MY_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    /**
     * Link social profile to user account
     */
    public function link_social_put() {
        // Put data
        $social_uid = $this->put('social_uid');
        $social_site = $this->put('social_site');

        // Validation
        // TODO

        $user_id = $this->rest->user_id;

        // Update user data
        $where = ['id' => $user_id];
        $social_site = strtolower($social_site);
        $data = [
            "{$social_site}_uid" => $social_uid,
        ];

        $user_updated = Users_model::update($where, $data);

        if($user_updated !== true) {
            $message = lang('text_rest_error_while');
            $message = sprintf($message, 'updating account');
            // Response with 201
            $this->response([
                'message' => $message,
            ], 400);
        }

        // Response with 201
        $this->response([
            'message' => lang('text_rest_user_updated'),
        ], 200);
    }
}