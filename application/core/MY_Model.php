<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    protected static $CI;

    public function __construct() {
        self::$CI =& get_instance();
    }

    /**
     * Create record
     * @param array $data - Insert data
     * @param string $table - Table name
     * @return boolean - Success state
     */
    public static function create($data, $table) {
        // If 'created_at' is not set from request, set it.
        $created_at = 'created_at';
        if(!array_key_exists($created_at, $data)) {
            $fields = self::_get_table_fields($table);
            if(in_array($created_at, $fields)) {
                $data[$created_at] = date('Y-m-d H:i:s');
            }
        }
        return self::$CI->db->insert($table, $data);
    }

    /**
     * Get table fields
     * @param string $table_name - Table name
     * @return array - array of fields
     */
    private static function _get_table_fields($table_name) {
        return self::$CI->db->list_fields($table_name);
    }
}