<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends MY_Model {

    static $table_name = 'users';

    public function __construct() {
        parent::__construct();
        self::$CI = parent::$CI;
    }

    /**
     * Find user by fields & value pair
     * @param array - $where - Fields and values for where
     * @param array - $select - Additional select fields
     * @return object
     */
    public static function find_user(array $where, array $select = []) {
        $default_select = [
            'id',
            'email',
            'userid',
            'password',
            'created_at',
            'deleted_at',
            'is_deleted',
        ];
        $arr_select = array_merge($default_select, $select);
        self::$CI->db->select($arr_select)
                 ->from(self::$table_name)
                 ->order_by('id', 'DESC');
        if(!empty($where)) {
            $set_where = parent::set_where_fields($where, self::$table_name);
            if(!$set_where) {
                return false;
            }
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

    /**
     * Update record
     * @param array $where - Where field & values
     * @param array $data - Update data
     * @param string $table_name - Table name, empty by default
     */
    public static function update($where, $data, $table_name = '') {
        return parent::update($where, $data, self::$table_name);
    }

    /**
     * List of deleted accounts
     * @return array of objects
     */
    public static function deleted_accounts() {
        self::$CI->db->select([
                'userid',
                'email',
                'fullname',
                'created_at',
                'deleted_at',
            ])
            ->from(self::$table_name)
            ->where('is_deleted', 1);
        $query = self::$CI->db->get();
        $result = $query->result_object();
        return $result;
    }
}