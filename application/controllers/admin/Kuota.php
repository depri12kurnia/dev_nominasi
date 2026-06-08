<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kuota extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Wajib
        $this->load->model('M_settings');
        $this->load->model('M_log_user');
        // End Wajib
        $this->load->model('M_kuota');

        if (!$this->ion_auth->in_group('admin')) {
            redirect('page_errors');
        }
    }

    public function index()
    {
        $data['website'] = $this->M_settings->get_all_settings();
        $data['title'] = 'Kuota | Admin Panel';
        $data['content'] = 'paneladmin/kuota/list';
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

        $list = $this->M_kuota->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $k) {
            $no++;
            $row = array();
            $row[] = $k->id;
            $row[] = $k->nama_prodi;
            $row[] = $k->kuota_utama;
            $row[] = $k->persentase_cad . '%'; // Menampilkan persentase cadangan di tabel
            $row[] = $k->kuota_cadangan;
            $row[] = '<a class="btn btn-primary btn-sm" href="javascript:void(0)" title="Edit" onclick="edit_kuota(' . "'" . $k->id . "'" . ')"><i class="fa fa-edit"></i></a>
                      <a class="btn btn-danger btn-sm" href="javascript:void(0)" title="Delete" onclick="delete_kuota(' . "'" . $k->id . "'" . ')"><i class="fa fa-trash"></i></a>';
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->M_kuota->count_all(),
            "recordsFiltered" => $this->M_kuota->count_filtered(),
            "data" => $data,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        );
        echo json_encode($output);
    }

    public function ajax_edit($id)
    {
        $data = $this->M_kuota->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_add()
    {
        $this->validate_csrf();
        $this->_validate();

        // DEFINISIKAN VARIABEL TERLEBIH DAHULU AGAR TIDAK UNDEFINED
        $kuota_utama    = (int) $this->input->post('kuota_utama');
        $persentase_cad = (int) round((float) $this->input->post('persentase_cad')); // Memastikan persentase bulat

        $data = array(
            'nama_prodi'     => $this->input->post('nama_prodi'),
            'kuota_utama'    => $kuota_utama,
            'persentase_cad' => $persentase_cad,
            'kuota_cadangan' => round(($persentase_cad / 100) * $kuota_utama)
        );

        $this->M_kuota->insert_kuota($data);

        // Mendapatkan user yang login
        $user = $this->ion_auth->user()->row();
        // Menyimpan log aktivitas login
        $this->M_log_user->save_log($user->id, 'Add Kuota');

        echo json_encode([
            "status" => TRUE,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        ]);
    }

    public function ajax_update()
    {
        $this->validate_csrf();
        $this->_validate();

        // DEFINISIKAN VARIABEL TERLEBIH DAHULU AGAR TIDAK UNDEFINED
        $kuota_utama    = (int) $this->input->post('kuota_utama');
        $persentase_cad = (int) round((float) $this->input->post('persentase_cad')); // Memastikan persentase bulat

        $data = array(
            'nama_prodi'     => $this->input->post('nama_prodi'),
            'kuota_utama'    => $kuota_utama,
            'persentase_cad' => $persentase_cad,
            'kuota_cadangan' => round(($persentase_cad / 100) * $kuota_utama)
        );

        $this->M_kuota->update_kuota($this->input->post('id'), $data);

        // Mendapatkan user yang login
        $user = $this->ion_auth->user()->row();
        // Menyimpan log aktivitas login
        $this->M_log_user->save_log($user->id, 'Update Kuota');

        echo json_encode([
            "status" => TRUE,
            "csrf_token" => $this->security->get_csrf_hash() // Kirim token CSRF baru
        ]);
    }

    public function ajax_delete($id)
    {
        $this->validate_csrf();

        $this->M_kuota->delete_kuota($id);

        // Mendapatkan user yang login
        $user = $this->ion_auth->user()->row();
        // Menyimpan log aktivitas login
        $this->M_log_user->save_log($user->id, 'Delete Kuota');

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

        $nama_prodi     = $this->input->post('nama_prodi');
        $kuota_utama    = $this->input->post('kuota_utama');
        $persentase_cad = $this->input->post('persentase_cad');

        if (empty($nama_prodi)) {
            $data['inputerror'][] = 'nama_prodi';
            $data['error_string'][] = 'Nama prodi wajib diisi';
            $data['status'] = FALSE;
        }

        if ($kuota_utama === '' || $kuota_utama === NULL || !is_numeric($kuota_utama)) {
            $data['inputerror'][] = 'kuota_utama';
            $data['error_string'][] = 'Kuota utama wajib diisi dan harus berupa angka';
            $data['status'] = FALSE;
        }

        // PERBAIKAN LOGIKA VALIDASI PERSENTASE ANGKA TANGGUH
        if ($persentase_cad === '' || $persentase_cad === NULL || !is_numeric($persentase_cad)) {
            $data['inputerror'][] = 'persentase_cad';
            $data['error_string'][] = 'Persentase cadangan wajib diisi dan harus berupa angka';
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
