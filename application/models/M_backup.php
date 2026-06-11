<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_backup extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function truncate_nominasi_table()
    {
        // Mengosongkan tabel dan mereset auto-increment
        return $this->db->truncate('nominasi_camaba');
    }
}
