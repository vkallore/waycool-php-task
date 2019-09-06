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

    public static function login_list() {
        // SELECT user_id, usr.email,  login_type, COUNT(login_type) as login_type_count,
        //   SUM(CASE WHEN login_type = 'Email' THEN 1 ELSE 0 END) AS email,
        //   SUM(CASE WHEN login_type = 'FB' THEN 1 ELSE 0 END) AS facebook,
        //   SUM(CASE WHEN login_type = 'G' THEN 1 ELSE 0 END) AS google
        // FROM `login_logs` AS logs
        // INNER JOIN `users` AS usr ON usr.id = logs.user_id AND usr.is_deleted = 0
        // GROUP BY user_id
        $query = self::$CI->db->select([
                                'user_id',
                                'email AS email_id',
                                'fullname',
                                "SUM(CASE WHEN login_type = 'Email' THEN 1 ELSE 0 END) AS email",
                                "SUM(CASE WHEN login_type = 'FB' THEN 1 ELSE 0 END) AS facebook",
                                "SUM(CASE WHEN login_type = 'G' THEN 1 ELSE 0 END) AS google"
                            ])
                            ->from(self::$table_name . ' logs')
                            ->join(Users_model::$table_name . ' usr ' , 'usr.id = logs.user_id AND usr.is_deleted = 0')
                            ->group_by([
                                'user_id'
                            ])
                            ->get();
        return $query->result_object();
    }
}