<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends MY_Model {

    static $table_name = 'users';

    public function __construct() {
        parent::__construct();
        self::$CI = parent::$CI;
    }

    /**
     * Find user by fields & value pair
     * @param array
     * @return object
     */
    public static function find_user(array $fields_and_values) {
        self::$CI->db->select([
                        'id',
                        'email',
                        'userid',
                        'password',
                        'created_at',
                        'deleted_at',
                        'is_deleted',
                    ])
                 ->from(self::$table_name)
                 ->order_by('id', 'DESC');
        if(!empty($fields_and_values)) {
            self::$CI->db->where($fields_and_values);
        }
        $query = self::$CI->db->get();
        $result = $query->row_object();
        return $result;
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