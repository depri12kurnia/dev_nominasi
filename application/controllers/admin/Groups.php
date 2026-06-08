<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Groups extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Wajib
        $this->load->model('M_settings');
        $this->load->model('M_log_user');
        // End Wajib
        $this->load->model('M_groups');

        // if (!$this->ion_auth->is_admin()) {
        //     redirect('auth/login');
        // }
        if (!$this->ion_auth->in_group('admin')) {
            redirect('page_errors');
        }
    }

    public function index()
    {
        $data['website'] = $this->M_settings->get_all_settings();
        $data['title'] = 'Groups | Admin Panel';
        $data['content'] = 'paneladmin/groups/list';
        $this->load->view('layouts/adminlte3', $data);
    }

    public function ajax_list()
    {
        $csrf_token = $this->input->server('HTTP_X_CSRF_TOKEN');
        $valid_token = $this->security->get_csrf_hash();

        if (empty($csrf_token)) {
            log_message('error', 'CSRF Token kosong, periksa apakah dikirim dari frontend.');
        }

        if ($csrf_token !== $valid_token) {
            echo json_encode(['status' => 'Error', 'message' => 'Invalid CSRF Token']);
            exit();
        }

        $list = $this->M_groups->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $group) {
            $no++;
            $row = array();
            $row[] = $group->id;
            $row[] = $group->name;
            $row[] = $group->description;
            $row[] = '<a class="btn btn-primary btn-sm" href="javascript:void(0)" title="Edit" onclick="edit_group(' . "'" . $group->id . "'" . ')"><i class="fa fa-edit"></i></a>
                      <a class="btn btn-danger btn-sm" href="javascript:void(0)" title="Delete" onclick="delete_group(' . "'" . $group->id . "'" . ')"><i class="fa fa-trash"></i></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_groups->count_all(),
            "recordsFiltered" => $this->M_groups->count_filtered(),
            "data" => $data,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        );
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->M_groups->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        $this->validate_csrf();

        $this->_validate();
        $data = array(
            'name' => $this->input->post('name'),
            'description' => $this->input->post('description')
        );

        $this->M_groups->insert_group($data);

        // Mendapatkan user yang login
        $user = $this->ion_auth->user()->row();
        // Menyimpan log aktivitas login
        $this->M_log_user->save_log($user->id, 'Add Group');

        echo json_encode([
            "status" => TRUE,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        ]);
    }

    public function ajax_update()
    {
        $this->validate_csrf();

        $this->_validate();
        $data = array(
            'name' => $this->input->post('name'),
            'description'    => $this->input->post('description')
        );

        $this->M_groups->update_group($this->input->post('id'), $data);

        // Mendapatkan user yang login
        $user = $this->ion_auth->user()->row();
        // Menyimpan log aktivitas login
        $this->M_log_user->save_log($user->id, 'Update Group');

        echo json_encode([
            "status" => TRUE,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        ]);
    }

    public function ajax_delete($id)
    {
        $this->validate_csrf();

        $this->M_groups->delete_group($id);

        // Mendapatkan user yang login
        $user = $this->ion_auth->user()->row();
        // Menyimpan log aktivitas login
        $this->M_log_user->save_log($user->id, 'Delete Group');

        echo json_encode([
            "status" => TRUE,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        ]);
    }

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        $name = $this->input->post('name');
        $description = $this->input->post('description');

        if (empty($name)) {
            $data['inputerror'][] = 'name';
            $data['error_string'][] = 'name is required';
            $data['status'] = FALSE;
        }

        if (empty($description)) {
            $data['inputerror'][] = 'description';
            $data['error_string'][] = 'description is required';
            $data['status'] = FALSE;
        }


        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }


    private function validate_csrf()
    {
        $csrf_token = $this->input->server('HTTP_X_CSRF_TOKEN');
        $valid_token = $this->security->get_csrf_hash();

        if ($csrf_token !== $valid_token) {
            echo json_encode(['status' => 'Error', 'message' => 'Invalid CSRF Token']);
            exit();
        }
    }
}
