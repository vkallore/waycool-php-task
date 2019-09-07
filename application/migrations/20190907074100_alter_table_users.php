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
            'user_type' => [
                'type' => 'TINYINT(1)',
                'default' => 1,
                'comments' => '0 - Admin user, 1 - Signup user',
                'after' => 'id',
            ],
        );
        $this->dbforge->add_column($this->table, $fields);

        $this->db->insert($this->table, [
            'userid'     => 'admin@waycool.com',
            'email'      => 'admin@waycool.com',
            'password'   => password_hash('WayCool', PASSWORD_BCRYPT, ['cost' => 12]),
            'fullname'   => 'WayCool Admin',
            'user_type'  => 0,
            'created_at' => date('Y-' . rand(1, 12) . '-' . rand(1, 28) . ' H:i:s'),
        ]);
    }


    public function down()
    {
        if ($this->db->table_exists($this->table))
        {
            $this->dbforge->drop_column($this->table, [
                'user_type',
            ]);

            $this->db->where('userid', 'admin@waycool.com')
                     ->delete($this->table);
        }
    }

}
