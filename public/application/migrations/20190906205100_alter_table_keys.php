<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Migration_alter_table_keys
 *
 * @property CI_DB_forge         $dbforge
 * @property CI_DB_query_builder $db
 */
class Migration_alter_table_keys extends CI_Migration {


    protected $table = 'keys';


    public function up()
    {
        $fields = array(
            'user_id' => [
                'type' => 'INT(11)',
                'null' => true,
                'after' => 'id',
            ],
        );
        $this->dbforge->add_column($this->table, $fields);
    }


    public function down()
    {
        if ($this->db->table_exists($this->table))
        {
            $this->dbforge->drop_column($this->table, [
                'user_id',
            ]);
        }
    }

}
