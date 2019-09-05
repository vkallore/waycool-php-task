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

        // Find user with same email ID
        $user = Users_model::find_user([
            'email' => $email,
        ]);

        // Show last active period, if user with same email is deleted
        if(!empty($user) && $user->is_deleted == 1) {
            $this->response([
                'message' => "We found that the account was active during {$user->created_at} - {$user->deleted_at}. Confirm to re-create the account.",
            ], 202); // Accepted, but not taken action
        } else if (!empty($user)) {
            $this->response([
                'message' => "An account with same email exists! Please try another.",
            ], 403);
        }

        // Generate unique userid
        $email_first_char = substr($email, 0, 1);
        $name_first_char = substr($fullname, 0, 1);
        $prefix = strtoupper($email_first_char . $name_first_char);
        $userid = uniqid($prefix);

        $random_password = random_password();
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
            $this->response([
                'message' => 'Error occurred while creating account! Please try again.',
            ], 500);
        }

        // Send email
        parent::send_email($email, 'TEST Email 😀', 'Testing 😀');

        // Response with 201
        $this->response([
            'message' => 'User created successfully. Login details have been sent your email.'
        ], 201);
    }
}