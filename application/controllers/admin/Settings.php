<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_settings');
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login');
        }
        if (!$this->ion_auth->in_group('admin')) {
            redirect('page_errors');
        }
    }

    public function index()
    {
        $data['settings'] = $this->M_settings->get_all_settings();
        // 
        $data['title'] = 'Settings | Admin Panel';
        $data['content'] = 'paneladmin/settings/store';
        $this->load->view('layouts/adminlte3', $data);
    }

    public function edit($id)
    {
        $data['settings'] = $this->M_settings->get_setting_by_id($id);
        $data['title'] = 'Settings | Admin Panel';
        $data['content'] = 'paneladmin/settings/edit';
        $this->load->view('layouts/adminlte3', $data);
    }

    public function update($id)
    {
        $data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description'),
            'company' => $this->input->post('company'),
            'address' => $this->input->post('address'),
            'telepon' => $this->input->post('telepon'),
            'email' => $this->input->post('email')
        );

        $this->M_settings->update_setting($id, $data);
        redirect('admin/settings');
    }
}
