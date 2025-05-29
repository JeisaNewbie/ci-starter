<?php
class Auth_model extends My_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function create_account()
    {
        $id = $this->input->post('id');
        $password = $this->input->post('password');
        $username = $this->input->post('username');

        $check = $this->db->select('user_id')
            ->from(TABLE_USER)
            ->where('user_id', $id)
            ->get();

        if ($check->num_rows() > 0) {
            return FALSE;
        }

        $data = [
            'user_id' => $id,
            'password' => $password,
            'username' => $username
        ];

        $this->db->insert(TABLE_USER, $data);
        return TRUE;
    }

    public function login()
    {
        $id = $this->input->post('id');
        $password = $this->input->post('password');

        $check = $this->db->select('id, user_id, password, username')
            ->from(TABLE_USER)
            ->where('user_id', $id)
            ->get();

        $row = $check->row(0);

        if ($row === NULL) {
            return FALSE;
        }

        if ($password !== $row->password) {
            return FALSE;
        }

        $login = [
            'id' => $row->id,
            'username' => $row->username
        ];

        $this->session->set_userdata($login);

        return TRUE;
    }
}
