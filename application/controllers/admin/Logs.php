<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Logs extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_settings');
        $this->load->model('M_log');
        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login');
        }
        if (!$this->ion_auth->in_group('admin')) {
            redirect('page_errors');
        }
    }

    public function index()
    {
        $data['website'] = $this->M_settings->get_all_settings();
        $data['title'] = 'Logs | Admin Panel';
        $data['content'] = 'paneladmin/logs/log_access';
        $this->load->view('layouts/adminlte3', $data);
    }

    public function get_data()
    {
        $csrf_token = $this->input->server('HTTP_X_CSRF_TOKEN');
        $valid_token = $this->security->get_csrf_hash();

        log_message('debug', 'CSRF Token dari request POST: ' . ($csrf_token ?: 'TIDAK ADA'));
        log_message('debug', 'CSRF Token yang valid: ' . $valid_token);
        log_message('debug', 'Session CSRF Token: ' . $this->session->userdata('csrf_token_jkt3'));


        if (empty($csrf_token)) {
            log_message('error', 'CSRF Token kosong, periksa apakah dikirim dari frontend.');
        }

        if ($csrf_token !== $valid_token) {
            echo json_encode(['status' => 'Error', 'message' => 'Invalid CSRF Token']);
            exit();
        }

        $list = $this->M_log->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $crud) {
            $no++;
            $row = array();
            $row[] = $crud->id;
            $row[] = $crud->timestamp;
            $row[] = $crud->ip_address;
            $row[] = $crud->user_agent;
            $row[] = $crud->uri;
            $row[] = $crud->method;
            $row[] = $crud->message;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_log->count_all(),
            "recordsFiltered" => $this->M_log->count_filtered(),
            "data" => $data,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        );
        echo json_encode($output);
    }

    public function delete_all_logs()
    {
        try {
            $deleted_rows = $this->M_log->delete_all_logs();

            if ($deleted_rows !== false) {
                echo json_encode([
                    'status' => 'success',
                    'message' => "Deleted $deleted_rows old log(s) successfully",
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to delete logs'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}
