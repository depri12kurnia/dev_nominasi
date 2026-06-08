<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_settings');
        $this->load->model('M_users');
        $this->load->model('M_log_user');

        if (!$this->ion_auth->in_group('admin')) {
            redirect('page_errors');
        }
    }

    public function index()
    {
        $data['website'] = $this->M_settings->get_all_settings();
        $data['groups'] = $this->M_users->get_groups();
        $data['title'] = 'Users | Admin Panel';
        $data['content'] = 'paneladmin/users/list';
        $this->load->view('layouts/adminlte3', $data);
    }

    public function ajax_list()
    {
        $csrf_token = $this->input->server('HTTP_X_CSRF_TOKEN');
        $valid_token = $this->security->get_csrf_hash();

        // log_message('debug', 'CSRF Token dari request POST: ' . ($csrf_token ?: 'TIDAK ADA'));
        // log_message('debug', 'CSRF Token yang valid: ' . $valid_token);
        // log_message('debug', 'Session CSRF Token: ' . $this->session->userdata('csrf_token_jkt3'));


        if (empty($csrf_token)) {
            log_message('error', 'CSRF Token kosong, periksa apakah dikirim dari frontend.');
        }

        if ($csrf_token !== $valid_token) {
            echo json_encode(['status' => 'Error', 'message' => 'Invalid CSRF Token']);
            exit();
        }

        $list = $this->M_users->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $user) {
            $no++;
            $row = array();
            $row[] = $no++;
            $row[] = $user->email;
            $row[] = $user->first_name;
            $row[] = $user->group_name;
            $row[] = '<a class="btn btn-primary btn-sm" href="javascript:void(0)" title="Edit" onclick="edit_user(' . "'" . $user->id . "'" . ')"><i class="fa fa-edit"></i></a>
                      <a class="btn btn-danger btn-sm" href="javascript:void(0)" title="Delete" onclick="delete_user(' . "'" . $user->id . "'" . ')"><i class="fa fa-trash"></i></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_users->count_all(),
            "recordsFiltered" => $this->M_users->count_filtered(),
            "data" => $data,
            "csrf_token" => $this->security->get_csrf_hash()
        );
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->M_users->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        $this->validate_csrf(); // Validasi CSRF

        $this->_validate(); // Validasi form

        $group_id = $this->input->post('group_id'); // Ambil ID grup

        $data = array(
            'password'   => $this->input->post('password'),
            'email'      => $this->input->post('email'),
            'first_name' => $this->input->post('first_name'),

        );

        // Daftarkan user dengan grup yang dipilih
        $user_id = $this->ion_auth->register(
            $data['email'],
            $data['password'],
            $data['email'],
            array('first_name' => $data['first_name']),
            array($group_id)
        );

        if ($user_id) {
            // Mendapatkan user yang sedang login
            $user = $this->ion_auth->user()->row();

            if ($user) {
                // Simpan log aktivitas pendaftaran
                $this->M_log_user->save_log($user->id, 'User registered');
            }

            echo json_encode([
                "status" => TRUE,
                "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
            ]);
        } else {
            echo json_encode([
                "status" => FALSE,
                "message" => "Registrasi gagal",
                "csrf_token" => $this->security->get_csrf_hash() // Token CSRF tetap dikirim
            ]);
        }
    }

    public function ajax_update()
    {
        $this->validate_csrf(); // Validasi CSRF

        $this->_validate(); // Validasi form

        $user_id = $this->input->post('id');
        $group_id = $this->input->post('group_id');

        $data = array(
            'email'      => $this->input->post('email'),
            'first_name' => $this->input->post('first_name')
        );

        $password = $this->input->post('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        // Update data user
        if ($this->ion_auth->update($user_id, $data)) {
            // Perbarui grup pengguna jika ada perubahan
            if (!empty($group_id)) {
                $this->ion_auth->remove_from_group(NULL, $user_id); // Hapus semua grup sebelumnya
                $this->ion_auth->add_to_group($group_id, $user_id); // Tambahkan ke grup baru
            }

            // Mendapatkan user yang sedang login
            $user = $this->ion_auth->user()->row();

            if ($user) {
                // Simpan log aktivitas update user
                $this->M_log_user->save_log($user->id, "Updated user ID: $user_id");
            }

            echo json_encode([
                "status" => TRUE,
                "message" => "User berhasil diperbarui",
                "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
            ]);
        } else {
            echo json_encode([
                "status" => FALSE,
                "message" => "Gagal memperbarui user",
                "csrf_token" => $this->security->get_csrf_hash()
            ]);
        }
    }

    public function ajax_delete($id)
    {
        $this->validate_csrf();

        $this->ion_auth->delete_user($id);
        $this->M_users->delete_user_groups($id);

        // Mendapatkan user yang login
        $user = $this->ion_auth->user()->row();
        // Menyimpan log aktivitas login
        $this->M_log_user->save_log($user->id, 'User Deleted');

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

        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $first_name = $this->input->post('first_name');


        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'Valid email is required';
            $data['status'] = FALSE;
        }

        if (empty($password) || strlen($password) < 6) {
            // For update, password is optional
            if (!$this->input->post('id')) {
                $data['inputerror'][] = 'password';
                $data['error_string'][] = 'Password must be at least 6 characters';
                $data['status'] = FALSE;
            }
        }

        if (empty($first_name)) {
            $data['inputerror'][] = 'first_name';
            $data['error_string'][] = 'First name is required';
            $data['status'] = FALSE;
        }

        $group_id = $this->input->post('group_id');
        if (empty($group_id)) {
            $data['inputerror'][] = 'group_id';
            $data['error_string'][] = 'Group is required';
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
