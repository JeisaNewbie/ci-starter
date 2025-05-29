<?php
class Board_model extends My_Model
{
    private $response = [
        'status' => FALSE,
        'message' => NULL
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->library('category');
        $this->load->library('utils');
    }

    public function get_board_index($qs)
    {        
        /* 전체 글 조회 */

        $offset = $qs['num'] * ($qs['page'] - 1);

        if ($qs['search'] != NULL) 
        {
            $this->db->like('title', $qs['search']);
        }

        if ($qs['category'] != 'ALL')
        {
            if (!in_array($qs['category'], Category::values(), false)) 
            {
                return $this->response;
            }
            $this->db->where('category', $qs['category']);
        }

        $data = $this->db
            ->select('id, group_id, title, parent_id, depth, status, created_at, category')
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

    public function create_board()
    {
        $user_id = $this->session->userdata('id');

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

        return $this->db
            ->set('group_id', $insert_id)
            ->where('id', $insert_id)
            ->update(TABLE_BOARD);
    }

    public function create_child_board($parent_id)
    {
        $user_id = $this->session->userdata('id');

        $attributes = ['id' => $parent_id];

        $query = $this->db->get_where(TABLE_BOARD, $attributes);

        $row = $query->row_array();

        $new_left = $row['r_value'];
        $new_right = $new_left + 1;

        $data = [
            'user_id' => $user_id,
            'title' => $row['title'],
            'content' => $this->input->post('content'),
            'category' => $row['category'],
            'parent_id' => $parent_id,
            'group_id' => $row['group_id'],
            'depth' => $row['depth'] + 1,
            'l_value' => $new_left,
            'r_value' => $new_right,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 'ACTIVE'
        ];

        $where_attr_left = ['l_value >=' => $new_right, 'group_id' => $row['group_id']];
        $where_attr_right = ['r_value >=' => $new_left, 'group_id' => $row['group_id']];

        $this->db
            ->set('l_value', 'l_value + 2', FALSE)
            ->where($where_attr_left)
            ->update(TABLE_BOARD);

        log_message('error', $this->db->last_query());

        $this->db
            ->set('r_value', 'r_value + 2', FALSE)
            ->where($where_attr_right)
            ->update(TABLE_BOARD);
        
        log_message('error', $this->db->last_query());

        $this->db->insert(TABLE_BOARD, $data);
        log_message('error', $this->db->last_query());
        $insert_id = $this->db->insert_id();


        $this->response['status'] = TRUE;
        $this->response['message'] = '/board/view/' . $insert_id;

        return $this->response;
    }

    public function update_board($board_id)
    {
        
        $user_id = $this->session->userdata('id');

        $data = $this->db->get_where(TABLE_BOARD, ['id' => $board_id]);
        
        if ($data->num_rows() == 0)
        {
            $this->response['message'] = '게시글이 존재하지 않습니다.';
            return $this->response;
        }
        
        $ret = Utils::validate_user($user_id, $data->row(0)->user_id);

        if ($ret == FALSE)
        {
            $this->response['message'] = '본인의 게시글만 수정 가능합니다.';
            return $this->response;
        }

        $where = [
            'id' => $board_id,
            'user_id' => $user_id
        ];

        $data = [
            'content' => $this->input->post('content'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db
            ->where($where)
            ->update(TABLE_BOARD, $data);
        
        $this->response['status'] = TRUE;
        $this->response['message'] = '/board/view/' . $board_id;

        return $this->response;
    }

    public function delete_board($board_id)
    {
        $user_id = $this->session->userdata('id');

        $data = $this->db->get_where(TABLE_BOARD, ['id' => $board_id]);
        
        if ($data->num_rows() == 0)
        {
            $this->response['message'] = '게시글이 존재하지 않습니다.';
            return $this->response;
        } 

        $data = $data->row(0);

        $ret = Utils::validate_user($user_id, $data->user_id);
        
        if ($ret == FALSE)
        {
            $this->response['message'] = '본인의 게시글만 삭제 가능합니다.';
            return $this->response;
        }

        $data_to_delete = [
            'user_id' => $data->user_id,
            'board_ns_id' => $data->id,
            'title' => $data->title,
            'content' => $data->content
        ];

        $where = [
            'id' => $board_id,
            'user_id' => $user_id
        ];

        $this->db->insert('deleted_board', $data_to_delete);

        $query = $this->getSoftDeleteQuery(TABLE_BOARD, $where);
        $this->db->query($query);

        $this->response['status'] = TRUE;
        $this->response['message'] = '삭제 되었습니다.';

        return $this->response;
    }
}
