<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_create_table_users
 *
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_query_builder $db
 */
class Migration_create_table_users extends CI_Migration {


    protected $table = 'users';


    public function up()
    {
        $fields = array(
            'id' => [
                'type' => 'INT(11)',
                'auto_increment' => true,
                'unsigned' => true,
            ],
            'userid' => [
                'type' => 'VARCHAR(255)',
                'unique' => true,
            ],
            'email' => [
                'type' => 'VARCHAR(255)',
            ],
            'password' => [
                'type' => 'VARCHAR(255)',
            ],
            'fullname' => [
                'type' => 'VARCHAR(100)',
            ],
            'age' => [
                'type' => 'TINYINT(2)',
                'null' => true,
            ],
            'gender' => [
                'type' => 'VARCHAR(10)',
                'null' => true,
            ],
            'location_address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'location_lat' => [
                'type' => 'VARCHAR(20)',
                'null' => true,
            ],
            'location_long' => [
                'type' => 'VARCHAR(20)',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
            ],
            'is_deleted' => [
                'type' => 'TINYINT(1)',
                'default' => 0,
            ],
        );
        $this->dbforge->add_field($fields);
        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('email');
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
