<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_logs_model extends MY_Model {

    static $table_name = 'login_logs';

    public function __construct() {
        parent::__construct();
        self::$CI = parent::$CI;
    }

    /**
     * Log login requests
     */
    public static function log($user_id, $login_type = 'Email') {
        return self::create([
            'user_id' => $user_id,
            'login_type' => $login_type,
        ]);
    }

    /**
     * Create record
     * @param array $data - Insert data
     * @param string $table_name - Table name, empty by default
     */
    public static function create($data, $table_name = '') {
        return parent::create($data, self::$table_name);
    }
}