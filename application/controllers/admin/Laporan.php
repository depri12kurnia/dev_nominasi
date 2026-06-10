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

    public function ajax_update_status()
    {
        $id = $this->input->post('id');
        $status_raw = $this->input->post('status');
        $prodi_manual = $this->input->post('prodi_manual');
        $kelas_manual = $this->input->post('kelas');

        // echo json_encode(["debug" => $status_raw]);
        // exit;

        $split = explode('_', $status_raw);
        $pilihan = $split[0]; // Sekarang bernilai '2'
        $jenis   = $split[1]; // 'Utama'

        $mhs = $this->M_laporan->get_by_id($id);
        $nama_prodi = ($pilihan == '1') ? $mhs->pilihan_1 : $mhs->pilihan_2;
        // CEK KUOTA LAGI SEBELUM DISIMPAN
        $kuota = $this->db->get_where('kuota_prodi', ['nama_prodi' => $nama_prodi])->row();
        $terisi = $this->db->where('prodi_diterima', $nama_prodi)
            ->where('jenis_kelulusan', $jenis)
            ->count_all_results('nominasi_camaba');

        $limit = ($jenis == 'Utama') ? $kuota->kuota_utama : $kuota->kuota_cadangan;

        if ($terisi >= $limit) {
            // Pastikan header JSON disetel
            header('Content-Type: application/json');
            echo json_encode([
                "status" => FALSE,
                "message" => "Kuota $jenis untuk $nama_prodi sudah penuh!"
            ]);
            return;
        }

        // SIMPAN LANGSUNG PRODI YANG DI-FILTER (prodi_manual)
        $data_update = array(
            'prodi_diterima'   => $prodi_manual,
            'kelas_diterima'   => $kelas_manual,
            'jenis_kelulusan'  => $jenis,
            'pilihan_diterima' => $pilihan,
            'status'           => 1
        );

        $this->M_laporan->update_status(array('id' => $id), $data_update);
        echo json_encode(array("status" => TRUE));
    }

    public function ajax_reset_status()
    {
        $id = $this->input->post('id');

        $data_reset = array(
            'status'           => 0,
            'jenis_kelulusan'  => NULL,
            'pilihan_diterima' => NULL,
            'prodi_diterima'   => NULL,
            'kelas_diterima'   => NULL
        );

        $this->M_laporan->update_status(array('id' => $id), $data_reset);
        echo json_encode(array("status" => TRUE));
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


    public function ajax_view($id)
    {
        $data = $this->M_laporan->get_by_id($id);
        echo json_encode($data);
    }

    public function ajax_detail($id)
    {
        $this->ajax_view($id);
    }

    public function import_excel()
    {
        if (isset($_FILES["file_excel"]["name"])) {
            $path = $_FILES["file_excel"]["tmp_name"];
            $file_mimes = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel', 'text/plain');

            if (in_array($_FILES['file_excel']['type'], $file_mimes)) {
                $file = fopen($path, "r");
                $is_header = true;
                $insert_data = array();

                while (($row = fgetcsv($file, 10000, ",")) !== FALSE) {
                    // Fallback: Jika Excel CSV menggunakan pemisah titik koma (;) karena format Regional Indonesia
                    if (count($row) == 1 && strpos($row[0], ';') !== false) {
                        $row = explode(';', $row[0]);
                    }

                    // Lewati baris pertama (Header)
                    if ($is_header) {
                        $is_header = false;
                        continue;
                    }

                    // PROTEKSI 1: Lewati baris yang benar-benar kosong di akhir file (sering terjadi dari ekspor Excel)
                    if (empty($row) || !isset($row[1]) || trim($row[1]) == '') {
                        continue;
                    }

                    // PROTEKSI 2: Ambil data dengan aman menggunakan isset() untuk menghindari Undefined Offset
                    $no_ujian_raw    = isset($row[1]) ? $row[1] : '';
                    $nama            = isset($row[2]) ? $row[2] : '';
                    $no_daftar_raw   = isset($row[3]) ? $row[3] : '';
                    $asal_sekolah    = isset($row[4]) ? $row[4] : '';
                    $jurusan_sekolah = isset($row[5]) ? $row[5] : '';
                    $pilihan_1       = isset($row[6]) ? $row[6] : '';
                    $pilihan_2       = isset($row[7]) ? $row[7] : '';
                    $kelas_pil_1     = isset($row[8]) ? $row[8] : '';
                    $kelas_pil_2     = isset($row[9]) ? $row[9] : '';

                    // Fallback nilai default 0 jika kolom skor tidak ada di file CSV
                    $skor          = isset($row[10]) && $row[10] !== '' ? trim($row[10]) : 0;

                    // Membersihkan format angka dari file (misal '150122.0' menjadi '150122')
                    $no_ujian = preg_replace('/\.0$/', '', str_replace("'", "", $no_ujian_raw));
                    $no_daftar = preg_replace('/\.0$/', '', str_replace("'", "", $no_daftar_raw));

                    $insert_data[] = array(
                        'nomor_ujian'       => $no_ujian,
                        'nama'              => $nama,
                        'nomor_pendaftaran' => $no_daftar,
                        'asal_sekolah'      => $asal_sekolah,
                        'jurusan_sekolah'   => $jurusan_sekolah,
                        'pilihan_1'         => $pilihan_1,
                        'pilihan_2'         => $pilihan_2,
                        'kelas_pilihan_1'   => $kelas_pil_1,
                        'kelas_pilihan_2'   => $kelas_pil_2,
                        'skor'              => $skor,
                        'diunggah_pada'     => date('Y-m-d H:i:s')
                    );
                }
                fclose($file);

                if (!empty($insert_data)) {
                    $this->M_laporan->insert_batch($insert_data);
                    $this->session->set_flashdata('success', 'Data Nominasi berhasil diimport!');
                } else {
                    $this->session->set_flashdata('error', 'Gagal memproses. File kosong atau format baris tidak sesuai.');
                }
            } else {
                $this->session->set_flashdata('error', 'Format file yang diunggah harus CSV.');
            }
        }

        // Pastikan url redirect mengarah ke route/controller admin yang benar
        redirect('admin/nominasi');
    }

    public function export_excel_utama()
    {

        $prodi = $this->input->get('prodi');
        $kelas = $this->input->get('kelas');

        $this->db->from('nominasi_camaba');

        // Terapkan filter berdasarkan Prodi dan Kelas Diterima
        $this->M_laporan->_apply_filter_excel($prodi, $kelas);

        // Tambahkan kondisi 'Utama' sesuai kebutuhan Anda
        $this->db->where('jenis_kelulusan', 'Utama');
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

    public function export_excel_cadangan()
    {

        $prodi = $this->input->get('prodi');
        $kelas = $this->input->get('kelas');

        $this->db->from('nominasi_camaba');

        // Terapkan filter berdasarkan Prodi dan Kelas Diterima
        $this->M_laporan->_apply_filter_excel($prodi, $kelas);

        // Tambahkan kondisi 'Utama' sesuai kebutuhan Anda
        $this->db->where('jenis_kelulusan', 'Cadangan');
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
