<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_laporan extends CI_Model
{

    var $table = 'nominasi_camaba';
    // Sesuaikan variabel di Model/Controller agar match dengan array di atas:
    var $column_order = array(null, 'nomor_ujian', 'nama', 'nomor_pendaftaran', 'asal_sekolah', 'jurusan_sekolah', 'skor', 'status', null);
    var $column_search = array('nomor_ujian', 'nama', 'nomor_pendaftaran', 'asal_sekolah', 'jurusan_sekolah', 'skor', 'status');
    var $order = array('skor' => 'DESC');

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query()
    {
        $this->db->from($this->table);

        // Panggil Method Filter
        $this->_apply_filter();

        // (Kode pencarian bawaan datatables Anda biarkan di bawah sini)
        $i = 0;
        foreach ($this->column_search as $item) {
            if (isset($_POST['search']['value']) && $_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i)
                    $this->db->group_end();
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function get_by_id($id)
    {
        $this->db->from($this->table);
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    public function update_status($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }

    public function get_all_data()
    {
        $this->db->from($this->table);
        $this->_apply_filter();
        $this->db->order_by('skor', 'DESC');
        return $this->db->get()->result();
    }

    public function _apply_filter()
    {
        // Tangkap data dan bersihkan dari spasi berlebih menggunakan trim()
        $prodi   = trim($this->input->post('prodi') ? $this->input->post('prodi') : $this->input->get('prodi'));
        $pilihan = trim($this->input->post('pilihan') ? $this->input->post('pilihan') : $this->input->get('pilihan'));
        $kelas   = trim($this->input->post('kelas') ? $this->input->post('kelas') : $this->input->get('kelas'));

        // KONDISI 1: Tangkap variasi value Pilihan 1
        if ($pilihan == '1' || $pilihan == 'pilihan_1' || $pilihan == 'kelas_pilihan_1') {
            if (!empty($prodi)) {
                $this->db->where('pilihan_1', $prodi);
            }
            if (!empty($kelas)) {
                $this->db->where('kelas_pilihan_1', $kelas);
            }
        }
        // KONDISI 2: Tangkap variasi value Pilihan 2
        else if ($pilihan == '2' || $pilihan == 'pilihan_2' || $pilihan == 'kelas_pilihan_2') {
            if (!empty($prodi)) {
                $this->db->where('pilihan_2', $prodi);
            }
            if (!empty($kelas)) {
                $this->db->where('kelas_pilihan_2', $kelas);
            }
        }
        // KONDISI 3: Semua Pilihan (Kosong)
        else {
            // 
        }
    }

    public function _apply_filter_excel($prodi, $kelas)
    {
        // Bersihkan parameter
        $prodi = trim($prodi);
        $kelas = trim($kelas);

        // KONDISI: Jika prodi dan kelas DITERIMA ada, terapkan filter sesuai contoh Anda
        if (!empty($prodi)) {
            $this->db->where('prodi_diterima', $prodi);
        }

        if (!empty($kelas)) {
            $this->db->where('kelas_diterima', $kelas);
        }
    }
}
