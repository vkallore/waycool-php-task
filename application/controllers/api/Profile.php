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

        $social_site = strtolower($social_site);
        $social_data = [
            "{$social_site}_uid" => $social_uid,
        ];

        $find_where = array_merge($social_data, ['is_deleted' => 0]);

        // Check if the social UID is linked to any user
        $social_linked_user = Users_model::find_user($find_where);

        // Social account linked to another active account already
        if(!empty($social_linked_user)) {
            // Response with 400
            $this->response([
                'message' => lang('text_rest_social_linked_already'),
            ], 400);
        }

        // Update user data
        $where = ['id' => $user_id];

        $user_updated = Users_model::update($where, $social_data);

        if($user_updated !== true) {
            $message = lang('text_rest_error_while');
            $message = sprintf($message, 'linking social account');
            // Response with 400
            $this->response([
                'message' => $message,
            ], 400);
        }

        // Response with 200
        $this->response([
            'message' => lang('text_rest_social_linked_success'),
        ], 200);
    }

    /**
     * Delete user account
     */
    public function index_delete() {
        $user_id = $this->rest->user_id;

        // Start transaction
        $this->db->trans_start();

        // Soft delete user account
        $where = ['id' => $user_id];
        $data = [
            'is_deleted' => 1,
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        $user_updated = Users_model::update($where, $data);

        $message = lang('text_rest_error_while');
        $message = sprintf($message, 'deleting account');

        if($user_updated !== true) {
            // Response with 400
            $this->response([
                'message' => $message,
            ], 400);
        }

        // Delete all keys
        $keys_deleted = Keys_model::delete(['user_id' => $user_id]);
        if($keys_deleted !== true) {
            // Response with 400
            $this->response([
                'message' => $message,
            ], 400);
        }

        // End transaction
        $this->db->trans_complete();

        if( $this->db->trans_status() !== false ){
            // Response with 200
            $this->response([
                'message' => lang('text_rest_user_deleted'),
            ], 200);
        } else {

            // Response with 201
            $this->response([
                'message' => $message,
            ], 400);
        }
    }
}