<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_create_table_login_logs
 *
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_query_builder $db
 */
class Migration_create_table_login_logs extends CI_Migration {


    protected $table = 'login_logs';


    public function up()
    {
        $fields = array(
            'id' => [
                'type' => 'INT(11)',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT(11)',
            ],
            'login_type' => [
                'type' => 'VARCHAR(10)',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('user_id');
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
