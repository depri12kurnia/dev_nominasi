<?php
class M_users extends CI_Model
{
    var $table = 'users';
    var $column_order = array('users.id', 'users.username', 'users.email', 'users.first_name', 'users.last_name', 'groups.name');
    var $column_search = array('users.username', 'users.email', 'users.first_name', 'users.last_name', 'groups.name');
    var $order = array('users.id' => 'desc');

    private function _get_datatables_query()
    {
        $this->db->select('users.id, users.username, users.email, users.first_name, users.last_name, groups.name as group_name');
        $this->db->from($this->table);
        $this->db->join('users_groups', 'users_groups.user_id = users.id');
        $this->db->join('groups', 'groups.id = users_groups.group_id');
        $this->db->where('groups.name !=', 'registrant');
        $this->db->order_by('users.id', 'desc');

        $i = 0;

        foreach ($this->column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) {
                    $this->db->group_end();
                }
                $i++;
            }
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables()
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered()
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
        $this->db->select('users.id, users.username, users.email, users.first_name, users.last_name, users_groups.group_id');
        $this->db->from($this->table);
        $this->db->join('users_groups', 'users_groups.user_id = users.id');
        $this->db->where('users.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_groups()
    {
        $this->db->select('id, name');
        $this->db->from('groups');
        $query = $this->db->get();
        return $query->result();
    }

    public function add_user_group($user_id, $group_id)
    {
        $data = array(
            'user_id' => $user_id,
            'group_id' => $group_id
        );
        $this->db->insert('users_groups', $data);
    }

    public function update_user_group($user_id, $group_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('users_groups');
        $this->add_user_group($user_id, $group_id);
    }

    public function delete_user_groups($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->delete('users_groups');
    }

    public function get_user_id_by_username($username)
    {
        $this->db->select('id');
        $this->db->from('users');
        $this->db->where('username', $username);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        } else {
            return FALSE;
        }
    }

    public function is_username_exists($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('users'); // Sesuaikan dengan nama tabel user

        return $query->num_rows() > 0;
    }
}
