<?php
define ('TABLE_BOARD', 'board_ns');
define ('TABLE_USER', 'users');
class Board_model_v2 extends My_Model
{
    // const RESPONSE = [
    //         'status' => FALSE,
    //         'message' => NULL
    //     ];

    public function __construct()
    {
        $this->load->database();
        $this->load->library('Category');
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
            return NULL;
        }

        if ($password !== $row->password) {
            return NULL;
        }

        return [
            'id' => $row->id,
            'username' => $row->username
        ];
    }

    public function get_board_index($qs)
    {
        $response = [
            'status' => FALSE,
            'data' => NULL
        ];
        /* 전체 글 조회 */

        $offset = $qs['num'] * ($qs['page'] - 1);

        if ($qs['search'] !== NULL) {
            $this->db->like('title', $qs['search']);
        }

        if ($qs['category'] !== 'ALL')
        {
            if (!in_array($qs['category'], Category::values(), false)) 
            {
                return $response;
            }
            $this->db->where('category', $qs['category']);
        }

        $data = $this->db->select('id, group_id, title, parent_id, depth, status, created_at, category')
            ->from(TABLE_BOARD)
            ->order_by('group_id', 'DESC')
            ->order_by('l_value', 'ASC')
            ->limit($qs['num'], $offset)
            ->get()
            ->result_array();
        
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

        return $data;
    }

    public function set_board()
    {
        $user_id = $this->validate_login();

        if ($user_id === FALSE) {
            return FALSE;
        }

        $category = $this->input->post('category');

        if (!in_array($category, Category::values(), false)) {
            return FALSE;
        }
        

        $data = [
            'user_id' => $user_id,
            'title' => $this->input->post('title'),
            'content' => $this->input->post('content'),
            'category' => $category,
            'status' => 'ACTIVE',
            'parent_id' => NULL,
            'depth' => 0,
            'l_value' => 1,
            'r_value' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert(TABLE_BOARD, $data);
        $insert_id = $this->db->insert_id();

        // 쿼리 빌더로 수정
        // $this->db->set('group_id', 'id')
        //     ->where('id', $insert_id)
        //     ->update(TABLE_BOARD);
        return $this->db->query('UPDATE board_ns SET group_id = id WHERE id = ?', $insert_id);
    }

    public function set_content($id)
    {
        $response = [
            'status' => FALSE,
            'message' => NULL
        ];

        $user_id = $this->validate_login();

        if ($user_id === FALSE) 
        {
            $response['message'] = '로그인이 필요합니다.';
            return $response;
        }

        $attributes = ['id' => $id];

        $query = $this->db->get_where(TABLE_BOARD, $attributes);

        $row = $query->row_array();

        $new_left = $row['r_value'];
        $new_right = $new_left + 1;

        $data = [
            'user_id' => $user_id,
            'title' => $row['title'],
            'content' => $this->input->post('content'),
            'category' => $row['category'],
            'parent_id' => $id,
            'group_id' => $row['group_id'],
            'depth' => $row['depth'] + 1,
            'l_value' => $new_left,
            'r_value' => $new_right,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'AVTIVE'
        ];

        $where_attr_left = ['l_value >=' => $new_right, 'group_id' => $row['group_id']];
        $where_attr_right = ['r_value >=' => $new_left, 'group_id' => $row['group_id']];

        $this->db->set('l_value', 'l_value + 2', FALSE)
            ->where($where_attr_left)
            ->update(TABLE_BOARD);

        $this->db->set('r_value', 'r_value + 2', FALSE)
            ->where($where_attr_right)
            ->update(TABLE_BOARD);

        $this->db->insert(TABLE_BOARD, $data);
        $insert_id = $this->db->insert_id();

        $response['status'] = TRUE;
        $response['message'] = '/board_v2/view/' . $insert_id;

        return $response;
    }

    public function update_content($id)
    {
        $response = [
            'status' => FALSE,
            'message' => NULL
        ];

        $user_id = $this->validate_login();

        if ($user_id === FALSE) 
        {
            $response['message'] = '로그인이 필요합니다.';
            return $response;
        }

        $data = $this->db->get_where(TABLE_BOARD, ['id' => $id]);
        
        if ($data->num_rows() === 0)
        {
            $response['message'] = '게시글이 존재하지 않습니다.';
            return $response;
        } 
        
        $ret = $this->validate_user($user_id, $data->row(0)->user_id);

        if ($ret === FALSE)
        {
            $response['message'] = '본인의 게시글만 수정 가능합니다.';
            return $response;
        }

        $where = [
            'id' => $id,
            'user_id' => $user_id
        ];

        $data = [
            'content' => $this->input->post('content'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where($where)
            ->update(TABLE_BOARD, $data);
        
        $response['status'] = TRUE;
        $response['message'] = '/board_v2/view/' . $id;

        return $response;
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

        return $data;
    }

    public function set_comment($id)
    {
        $user_id = $this->validate_login();

        if ($user_id === FALSE) {
            return FALSE;
        }

        $data = [
            'user_id' => $user_id,
            'board_ns_id' => $id,
            'comment' => $this->input->post('comment'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'ACTIVE'
        ];

        $this->db->insert('comment', $data);
    }

    public function delete_board($id)
    {
        $response = [
            'status' => FALSE,
            'message' => NULL
        ];

        $user_id = $this->validate_login();

        if ($user_id === FALSE) 
        {
            $response['message'] = '로그인이 필요합니다.';
            return $response;
        }

        $data = $this->db->get_where(TABLE_BOARD, ['id' => $id]);
        
        if ($data->num_rows() === 0)
        {
            $response['message'] = '게시글이 존재하지 않습니다.';
            return $response;
        } 

        $data = $data->row(0);
        
        $ret = $this->validate_user($user_id, $data->user_id);

        if ($ret === FALSE)
        {
            $response['message'] = '본인의 게시글만 삭제 가능합니다.';
            return $response;
        }

        $data_to_delete = [
            'user_id' => $data->user_id,
            'board_ns_id' => $data->id,
            'title' => $data->title,
            'content' => $data->content
        ];

        $where = [
            'id' => $id,
            'user_id' => $user_id
        ];

        $this->db->insert('deleted_board', $data_to_delete);

        $query = $this->getSoftDeleteQuery(TABLE_BOARD, $where);
        $this->db->query($query);

        $response['status'] = TRUE;
        $response['message'] = '삭제 되었습니다.';

        return $response;
    }

    private function validate_login()
    {
        $user_id = $this->session->userdata('id');

        if ($user_id === NULL) {
            return FALSE;
        }

        return $user_id;
    }

    private function validate_user($user_id, $user_id_to_validate)
    {
        if ($user_id_to_validate !== $user_id)
        {
            return FALSE;
        }
        return TRUE;
    }
}
