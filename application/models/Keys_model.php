<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Keys_model extends MY_Model {

    static $table_name = 'keys';

    public function __construct() {
        parent::__construct();
        self::$CI = parent::$CI;
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