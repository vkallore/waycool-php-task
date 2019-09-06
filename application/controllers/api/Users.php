<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Users extends MY_Controller
{
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    public function index_post() {
        // Post data
        $email = $this->post('email');
        $fullname = $this->post('fullname');
        $user_age = $this->post('age');
        $gender = $this->post('gender');
        $location_address = $this->post('address');
        $location_lat = $this->post('latitude');
        $location_long = $this->post('longitude');

        // Validation
        // TODO

        $recreate_confirm = $this->post('recreate_confirm');

        // Find user with same email ID
        $user = Users_model::find_user([
            'email' => $email,
        ]);

        // Show last active period, if user with same email is deleted
        if(!empty($user) && $user->is_deleted == 1) {
            $message = lang('text_rest_account_last_active');
            $message = sprintf($message, $user->created_at, $user->deleted_at);
            // Do not process if not re-confirm
            if(empty($recreate_confirm)) {
                $this->response([
                    'message' => $message,
                ], 202); // Accepted, but not taken action
            }
        } else if (!empty($user)) {
            $this->response([
                'message' => lang('text_rest_account_exists'),
            ], 403);
        }

        // Generate unique userid
        $email_first_char = substr($email, 0, 1);
        $name_first_char = substr($fullname, 0, 1);
        $prefix = strtoupper($email_first_char . $name_first_char);
        $userid = uniqid($prefix);

        $random_password = random_string();
        $password_hash = password_hash($random_password, PASSWORD_BCRYPT, ['cost' => 12]);

        // Create a new user if email not found
        $user = Users_model::create([
            'userid' => $userid,
            'email' => $email,
            'password' => $password_hash,
            'fullname' => $fullname,
            'age' => $user_age,
            'gender' => $gender,
            'location_address' => $location_address,
            'location_lat' => $location_lat,
            'location_long' => $location_long,
        ]);

        if($user !== true) {
            $message = lang('text_rest_error_while');
            $message = sprintf($message, 'creating account');
            $this->response([
                'message' => $message,
            ], 500);
        }

        /**
         * Send email right away using SMTP/API
         * or
         * can save it in DB to process via scheduler
         */
        // Load language files
        $this->lang->load('email_content_lang', 'english');

        $email_subject = $this->lang->line('registration_email_subject');
        $email_content = $this->lang->line('registration_email_content');

        // Sending password via email is a legacy thought. Ignoring for now.
        $email_body = sprintf($email_content, $fullname, $userid, $random_password);
        $sent_email = parent::send_email($email, $email_subject, $email_body);

        $email_sent_message = $sent_email
                                ? lang('text_rest_login_email_success')
                                : lang('text_rest_login_email_failure');
        // Response with 201
        $this->response([
            'message' => lang('text_rest_user_created') . $email_sent_message,
        ], 201);
    }
}