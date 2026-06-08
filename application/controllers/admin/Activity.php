<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Activity extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_settings');
        $this->load->model('M_log_user');
        $this->load->library('ion_auth');
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('security');

        if (!$this->ion_auth->logged_in()) {
            redirect('auth/login');
        }
        if (!$this->ion_auth->in_group('admin')) {
            show_error('You do not have permission to access this page.');
        }
    }

    public function index()
    {
        $data['website'] = $this->M_settings->get_all_settings();
        $data['csrf_token'] = $this->security->get_csrf_token_name();
        $data['csrf_hash'] = $this->security->get_csrf_hash();
        // 
        $data['title'] = 'Activity | Admin Panel';
        $data['content'] = 'paneladmin/logs/activity';
        $this->load->view('layouts/adminlte3', $data);
    }

    public function get_csrf_token()
    {
        $csrf_token = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();
        echo json_encode(array(
            'csrf_token' => $csrf_token,
            'csrf_hash' => $csrf_hash
        ));
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

        $list = $this->M_log_user->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $crud) {
            $no++;
            $row = array();
            $row[] = $crud->id;
            $row[] = $crud->user_id;
            $row[] = $crud->action;
            $row[] = $crud->timestamp;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_log_user->count_all(),
            "recordsFiltered" => $this->M_log_user->count_filtered(),
            "data" => $data,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        );
        echo json_encode($output);
    }

    public function delete_all_activity()
    {
        // Set JSON header
        header('Content-Type: application/json');

        try {
            // Check table existence first
            if (!$this->db->table_exists('user_logs')) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Table user_logs does not exist'
                ]);
                return;
            }

            // Get current count for feedback
            $current_count = $this->db->count_all('user_logs');

            // Hapus semua activity log
            $delete_result = $this->M_log_user->delete_all_activity();

            if ($delete_result !== false) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'All activity logs deleted successfully (' . $delete_result . ' rows deleted)',
                    'csrf_token' => $this->security->get_csrf_hash()
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to delete activity logs - table may not exist or be empty'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Delete error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}
