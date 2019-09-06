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
     * Update record
     * @param array $where - Where field & values
     * @param array $data - Update data
     * @param string $table_name - Table name, empty by default
     */
    public static function update($where, $data, $table_name = '') {
        $set_where = self::set_where_fields($where, $table_name);
        if(!$set_where) {
            return false;
        }
        return self::$CI->db->update($table_name, $data);
    }

    /**
     * Delete record
     * @param array $where - Where field & values
     * @param string $table_name - Table name, empty by default
     */
    public static function delete($where, $table_name = '') {
        $set_where = self::set_where_fields($where, $table_name);
        if(!$set_where) {
            return false;
        }
        return self::$CI->db->delete($table_name);
    }

    /**
     * Get table fields
     * @param string $table_name - Table name
     * @return array - array of fields
     */
    private static function _get_table_fields($table_name) {
        return self::$CI->db->list_fields($table_name);
    }

    /**
     * Set where fields for the current query
     * @param array - $where
     * @return boolean - If no where is set, it won't process
     * to avoid getting the table updated by mistake with this method!
     */
    protected static function set_where_fields($where, $table) {
        $fields = self::_get_table_fields($table);
        $where_fields = [];
        foreach($where as $field => $value) {
            // Avoid SQL errors, only valid fields should consider for query
            if(in_array($field, $fields)) {
                $where_fields[$field] = $value;
            }
        }
        if(!empty($where_fields)) {
            self::$CI->db->where($where_fields);
            return true;
        }
        return false;
    }
}