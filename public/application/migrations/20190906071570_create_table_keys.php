<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_create_table_keys
 *
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_query_builder $db
 */
class Migration_create_table_keys extends CI_Migration {


    protected $table = 'keys';


    public function up()
    {
        $fields = array(
            'id' => [
                'type' => 'INT(11)',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'key' => [
                'type' => 'VARCHAR(40)',
            ],
            'level' => [
                'type' => 'TINYINT(2)',
            ],
            'ignore_limits' => [
                'type' => 'TINYINT(1)',
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table($this->table, true);
    }


    public function down()
    {
        if ($this->db->table_exists($this->table))
        {
            $this->dbforge->drop_table($this->table);
        }
    }

}
