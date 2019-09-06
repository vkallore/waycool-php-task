<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_alter_table_users
 *
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_query_builder $db
 */
class Migration_alter_table_users extends CI_Migration {


    protected $table = 'users';


    public function up()
    {
        $fields = array(
            'google_uid' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'facebook_uid' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        );
        $this->dbforge->add_column($this->table, $fields);
    }


    public function down()
    {
        if ($this->db->table_exists($this->table))
        {
            $this->dbforge->drop_column($this->table, [
                'google_uid',
                'facebook_uid',
            ]);
        }
    }

}
