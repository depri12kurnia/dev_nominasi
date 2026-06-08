<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_gains_table extends CI_Migration
{
    public function up()
    {
        // Create gains table
        $this->dbforge->add_field(array(
            'id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'user_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE
            ),
            'title' => array(
                'type' => 'VARCHAR',
                'constraint' => 255
            ),
            'description' => array(
                'type' => 'LONGTEXT',
                'null' => TRUE
            ),
            'category' => array(
                'type' => 'ENUM',
                'constraint' => array('IRPC', 'BPPA', 'AHIC', 'E2IPBC'),
                'null' => TRUE
            ),
            'date' => array(
                'type' => 'DATE',
                'null' => TRUE
            ),
            'submission_link' => array(
                'type' => 'TEXT',
                'null' => TRUE
            ),
            'status' => array(
                'type' => 'ENUM',
                'constraint' => array('draft', 'submitted', 'under_review', 'accepted', 'rejected'),
                'default' => 'draft'
            ),
            'created_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            ),
            'updated_at' => array(
                'type' => 'DATETIME',
                'null' => TRUE
            )
        ));

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->create_table('gains', TRUE);

        // Add foreign key constraint
        $this->db->query('ALTER TABLE gains ADD CONSTRAINT fk_gains_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    public function down()
    {
        // Drop foreign key
        $this->db->query('ALTER TABLE gains DROP FOREIGN KEY fk_gains_user_id');

        // Drop table
        $this->dbforge->drop_table('gains', TRUE);
    }
}
