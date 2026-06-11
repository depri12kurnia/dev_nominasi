<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Laporan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('M_settings');
        $this->load->model('M_laporan');
        $this->load->model('M_log_user');
        $this->load->model('M_users');

        // Pastikan library ion_auth sudah diload (biasanya di autoload.php)
        if (!$this->ion_auth->in_group(array('admin'))) {
            redirect('admin/page_errors');
        }
    }

    public function index()
    {
        $data['website'] = $this->M_settings->get_all_settings();
        $data['groups'] = $this->M_users->get_groups();
        $data['title'] = 'Laporan Management | Admin Panel';
        $data['content'] = 'paneladmin/laporan/list';
        $this->load->view('layouts/adminlte3', $data);
    }

    // 1. UPDATE di dalam method ajax_list()
    public function ajax_list()
    {
        // 1. Ambil input dari POST (dikirim oleh DataTables)
        $prodi = $this->input->post('prodi');
        $kelas = $this->input->post('kelas');
        $jenis = $this->input->post('jenis');

        // 2. Terapkan filter ke query builder
        // $this->db->from('nominasi_camaba');
        $this->M_laporan->_apply_filter_excel($prodi, $kelas, $jenis);

        // 3. Jalankan get_datatables
        $list = $this->M_laporan->get_datatables();
        $data = array();
        $no = $_POST['start'];

        foreach ($list as $nominasi) {
            $status_label = '';

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $nominasi->nomor_ujian;
            $row[] = $nominasi->nama;
            $row[] = $nominasi->nomor_pendaftaran;
            $row[] = $nominasi->asal_sekolah;
            $row[] = $nominasi->jurusan_sekolah;
            $row[] = $nominasi->skor;

            // Logika Status Kelulusan
            if (!empty($nominasi->jenis_kelulusan)) {
                $warna = ($nominasi->jenis_kelulusan == 'Utama') ? 'badge-success' : 'badge-warning';
                $status_label = '<span class="badge ' . $warna . '">Lulus Pilihan. ' . $nominasi->pilihan_diterima . ' (' . $nominasi->jenis_kelulusan . ') ' . $nominasi->kelas_diterima . '<br>Prodi:' . $nominasi->prodi_diterima . '</span>';
            } else {
                $status_label = '<span class="badge badge-secondary">Belum Ditentukan</span>';
            }
            $row[] = $status_label;

            $data[] = $row;
        }
        if (empty($this->input->post('prodi'))) {
            echo json_encode([
                "draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [] // KOSONGKAN DI SINI
            ]);
            return;
        } else {
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->M_laporan->count_all(),
                "recordsFiltered" => $this->M_laporan->count_filtered(),
                "data" => $data,
                "csrf_token" => $this->security->get_csrf_hash() // Penting untuk update token di view
            );

            echo json_encode($output);
        }
    }

    // Fungsi Mengambil Info Kuota untuk View ---
    public function ajax_get_kuota()
    {
        $prodi = $this->input->post('prodi');
        $kelas = $this->input->post('kelas');

        // 1. Ambil Kuota
        $kuota = $this->db->get_where('kuota_prodi', [
            'nama_prodi' => $prodi,
            'kelas'      => $kelas
        ])->row();

        $kuota_utama = $kuota ? (int)$kuota->kuota_utama : 0;
        $kuota_cadangan = $kuota ? (int)$kuota->kuota_cadangan : 0;

        // 2. Hitung yang sudah DITERIMA (Hasil Final)
        // Ini jauh lebih akurat daripada menghitung pilihan pendaftaran
        $terisi_utama = $this->db->where('prodi_diterima', $prodi)
            ->where('kelas_diterima', $kelas)
            ->where('jenis_kelulusan', 'Utama')
            ->count_all_results('nominasi_camaba');

        $terisi_cadangan = $this->db->where('prodi_diterima', $prodi)
            ->where('kelas_diterima', $kelas)
            ->where('jenis_kelulusan', 'Cadangan')
            ->count_all_results('nominasi_camaba');

        echo json_encode([
            'kuota_utama'     => $kuota_utama,
            'kuota_cadangan'  => $kuota_cadangan,
            'terisi_utama'    => $terisi_utama,
            'terisi_cadangan' => $terisi_cadangan,
            'csrf_token'      => $this->security->get_csrf_hash()
        ]);
    }

    public function export_excel()
    {

        $prodi = $this->input->get('prodi');
        $kelas = $this->input->get('kelas');
        $jenis = $this->input->get('jenis');

        $this->db->from('nominasi_camaba');

        // Terapkan filter berdasarkan Prodi dan Kelas Diterima
        $this->M_laporan->_apply_filter_excel($prodi, $kelas, $jenis);

        // Tambahkan kondisi 'Utama' sesuai kebutuhan Anda
        $this->db->order_by('skor', 'DESC');

        $nominasi_data = $this->db->get()->result();


        require_once APPPATH . '../vendor/autoload.php';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();


        $headers = ['ID', 'No. Ujian', 'Nama', 'No. Pendaftaran', 'Asal Sekolah', 'Jurusan Sekolah', 'Pilihan 1', 'Kelas Pilihan 1', 'Pilihan 2', 'Kelas Pilihan 2', 'Skor', 'Prodi Diterima', 'Kelas Diterima', 'Jenis Kelulusan', 'Diunggah Pada'];

        $column = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($column . '1', $h);
            $sheet->getStyle($column . '1')->getFont()->setBold(true);
            $column++;
        }

        $row = 2;
        $no = 1;
        foreach ($nominasi_data as $v) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $v->nomor_ujian);
            $sheet->setCellValue('C' . $row, $v->nama);
            $sheet->setCellValue('D' . $row, "'" . $v->nomor_pendaftaran);
            $sheet->setCellValue('E' . $row, $v->asal_sekolah);
            $sheet->setCellValue('F' . $row, $v->jurusan_sekolah);
            $sheet->setCellValue('G' . $row, $v->pilihan_1);
            $sheet->setCellValue('H' . $row, $v->kelas_pilihan_1);
            $sheet->setCellValue('I' . $row, $v->pilihan_2);
            $sheet->setCellValue('J' . $row, $v->kelas_pilihan_2);
            $sheet->setCellValue('K' . $row, $v->skor);
            $sheet->setCellValue('L' . $row, $v->prodi_diterima);
            $sheet->setCellValue('M' . $row, $v->kelas_diterima);
            $sheet->setCellValue('N' . $row, $v->jenis_kelulusan);
            $sheet->setCellValue('O' . $row, $v->diunggah_pada);
            $row++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Data_Nominasi_' . date('Y-m-d_H-i-s') . '.xlsx"');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function get_request_csrf_token()
    {
        $token_name = $this->security->get_csrf_token_name();
        $csrf = $this->input->post($token_name);
        if (empty($csrf)) {
            $csrf = $this->input->post('csrf_token');
        }
        if (empty($csrf)) {
            $csrf = $this->input->server('HTTP_X_CSRF_TOKEN');
        }
        return $csrf;
    }

    private function validate_csrf()
    {
        $csrf = $this->get_request_csrf_token();
        $valid = $this->security->get_csrf_hash();

        if ($csrf !== $valid) {
            echo json_encode([
                "status" => FALSE,
                "message" => "Invalid CSRF",
                "received_csrf" => $csrf,
                "expected_csrf" => substr($valid, 0, 8) . '...'
            ]);
            exit();
        }
    }
}
