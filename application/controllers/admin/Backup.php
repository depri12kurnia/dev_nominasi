<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backup extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->dbutil();
        $this->load->helper('download');
        $this->load->model('M_settings');
        $this->load->model('M_backup');

        if (!$this->ion_auth->in_group('admin')) {
            redirect('page_errors');
        }
    }

    public function data_all()
    {
        // Backup seluruh database
        $prefs = array(
            'format'      => 'zip',
            'filename'    => 'backup_all_' . date('Y-m-d_H-i-s') . '.sql'
        );

        $backup = $this->dbutil->backup($prefs);
        $db_name = 'db_backup_all_' . date('Y-m-d_H-i-s') . '.zip';

        force_download($db_name, $backup);
    }

    public function data_nominasi()
    {
        // Backup hanya tabel nominasi_camaba
        $prefs = array(
            'tables'      => array('nominasi_camaba'), // Nama tabel
            'format'      => 'zip',
            'filename'    => 'backup_nominasi_' . date('Y-m-d_H-i-s') . '.sql'
        );

        $backup = $this->dbutil->backup($prefs);
        $db_name = 'db_backup_nominasi_' . date('Y-m-d_H-i-s') . '.zip';

        force_download($db_name, $backup);
    }

    public function truncate_nominasi()
    {

        // 2. Eksekusi Truncate
        $this->M_backup->truncate_nominasi_table();

        // 3. Berikan feedback ke admin
        $this->session->set_flashdata('message', 'Tabel nominasi_camaba berhasil dikosongkan.');
        redirect('admin/settings');
    }
}
