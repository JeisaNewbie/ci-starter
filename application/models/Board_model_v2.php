<?php
class Board_model_v2 extends My_Model
{
    const TABLE_NAME = 'board_ns';
    public function __construct()
    {
        $this->load->database();
    }

    public function create_account()
    {
        $id = $this->input->post('id');
        $password = $this->input->post('password');
        $username = $this->input->post('username');

        $check = $this->db->select('user_id')
            ->from('users')
            ->where('user_id', $id)
            ->get();

        if ($check->num_rows() > 0) {
            log_message('error', $check->num_rows());
            return FALSE;
        }

        $data = array(
            'user_id' => $id,
            'password' => $password,
            'username' => $username
        );

        $this->db->insert('users', $data);
        return TRUE;
    }

    public function login()
    {
        log_message('error', 'board_model_v2 login');
        $id = $this->input->post('id');
        $password = $this->input->post('password');

        $check = $this->db->select('id, user_id, password, username')
            ->from('users')
            ->where('user_id', $id)
            ->get();

        $row = $check->row(0);

        if ($row === NULL) {
            log_message('error', $check->num_rows());
            return NULL;
        }

        if ($password !== $row->password) {
            return NULL;
        }

        return array(
            'id' => $row->id,
            'username' => $row->username
        );
    }

    public function get_page_num($num, $table, $where = array())
    {
        $page = $this->get_data_num($table, $where);
        $mod = $page % $num;
        $page = (int)($page / $num);

        return (($page > 0) && ($mod > 0)) ? $page + 1 : $page;
    }

    public function get_data_num($table, $where = array())
    {
        if ($where !== NULL) {
            $this->db->where($where);
        }

        return $this->db->count_all_results($table);
    }

    public function get_board_index($search, $num, $page)
    {
        /* 전체 글 조회 */
        //CONCAT(REPEAT("--", depth), title) AS 
        $offset = $num * ($page - 1);

        $this->db->select('id, group_id, title, parent_id, depth, status, created_at');
        $this->db->from('board_ns');

        if ($search !== '') {
            $this->db->like('title', $search);
        }

        $this->db->order_by('group_id', 'DESC');
        $this->db->order_by('l_value', 'ASC');
        $this->db->limit($num, $offset);

        $data = $this->db->get()->result_array();

        // ToDo: 메서드 추출
        foreach ($data as &$data_item) {
            if ($data_item['status'] === 'INACTIVE') {
                $data_item['title'] = '삭제된 게시글입니다.';
                $data_item['content'] = '삭제된 게시글입니다.';
            }
        }
        return $data;
    }

    public function get_board_view($id = NULL)
    {
        /* 특정 글 조회, parent_id 와 동일한 id를 같이 조회 */
        $query = $this->db->query(
            'WITH recursive cte AS (
            SELECT  *
            FROM    board_ns
            WHERE   id = ?
            UNION ALL
            SELECT	b.*
            FROM board_ns b
            INNER JOIN cte ON b.id = cte.parent_id) SELECT * FROM cte ORDER BY depth;',
            $id
        );

        $data = $query->result_array();

        // ToDo: 메서드 추출
        foreach ($data as &$data_item) {
            if ($data_item['status'] === 'INACTIVE') {
                $data_item['title'] = '삭제된 게시글입니다.';
                $data_item['content'] = '삭제된 게시글입니다.';
            }
        }
        return $data;
    }

    public function set_board()
    {
        $user_id = $this->validate_user();

        if ($user_id === FALSE) {
            return FALSE;
        }

        $data = array(
            'user_id' => $user_id,
            'title' => $this->input->post('title'),
            'content' => $this->input->post('content'),
            'parent_id' => NULL,
            'depth' => 0,
            'l_value' => 1,
            'r_value' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'ACTIVE'
        );

        $this->db->insert('board_ns', $data);
        $insert_id = $this->db->insert_id();
        // 쿼리 빌더로 수정
        return $this->db->query('UPDATE board_ns SET group_id = id WHERE id = ?', $insert_id);
    }

    // ToDo: 메서드 명 변경 필요
    public function set_content($id)
    {
        $user_id = $this->validate_user();

        if ($user_id === FALSE) {
            return FALSE;
        }

        $attributes = array('id' => $id);

        $query = $this->db->get_where('board_ns', $attributes);

        $row = $query->row_array();

        $new_left = $row['r_value'];
        $new_right = $new_left + 1;

        $data = array(
            'user_id' => $user_id,
            'title' => $row['title'],
            'content' => $this->input->post('content'),
            'parent_id' => $id,
            'group_id' => $row['group_id'],
            'depth' => $row['depth'] + 1,
            'l_value' => $new_left,
            'r_value' => $new_right,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'AVTIVE'
        );

        $where_attr_left = array('l_value >=' => $new_right, 'group_id' => $row['group_id']);
        $where_attr_right = array('r_value >=' => $new_left, 'group_id' => $row['group_id']);

        $this->db->set('l_value', 'l_value + 2', FALSE);
        $this->db->where($where_attr_left);
        $this->db->update('board_ns');

        $this->db->set('r_value', 'r_value + 2', FALSE);
        $this->db->where($where_attr_right);
        $this->db->update('board_ns');

        $this->db->insert('board_ns', $data);
        $insert_id = $this->db->insert_id();

        return $insert_id;
    }

    public function get_comment($id, $num, $page)
    {
        $offset = $num * ($page - 1);
        $from_to = array($id, $offset, (int)$num);

        $query = $this->db->query(
            'SELECT * FROM comment
            WHERE board_ns_id = ?
            ORDER BY created_at DESC
            LIMIT ?, ?;',
            $from_to
        );

        $data = $query->result_array();

        // ToDo: 메서드 추출
        foreach ($data as &$data_item) {
            if ($data_item['status'] === 'INACTIVE') {
                $data_item['title'] = '삭제된 게시글입니다.';
                $data_item['content'] = '삭제된 게시글입니다.';
            }
        }
        return $data;
    }

    public function set_comment($id)
    {
        $user_id = $this->validate_user();

        if ($user_id === FALSE) {
            return FALSE;
        }

        $data = array(
            'user_id' => $user_id,
            'board_ns_id' => $id,
            'comment' => $this->input->post('comment'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'ACTIVE'
        );

        $this->db->insert('comment', $data);
    }

    public function delete_board($id)
    {
        $user_id = $this->validate_user();

        if ($user_id === FALSE) {
            return FALSE;
        }

        $where = array(
            'id' => $id,
            'user_id' => $user_id
        );

        $query = $this->db->select('id')
            ->from('board_ns')
            ->where($where)
            ->get();

        $result = $query->row(0);

        if ($result === NULL) {
            return FALSE;
        } else {
            log_message('error', $result->id);
            $query = $this->getSoftDeleteQuery('board_ns', array('id' => $result->id));
            $this->db->query($query);
            return TRUE;
        }
    }

    private function validate_user()
    {
        $user_id = $this->session->userdata('id');

        if ($user_id === NULL) {
            return FALSE;
        }

        return $user_id;
    }
}
