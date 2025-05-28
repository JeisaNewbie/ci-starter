<?php
class Test_model extends My_Model
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

    private function set_board_test($user_id, $title, $content)
    {
        $values = Category::values();
        $category = $values[array_rand(Category::values(), 1)];

        $data = [
            'user_id' => $user_id,
            'title' => $title,
            'content' => $content,
            'category' => $category,
            'status' => 'ACTIVE',
            'parent_id' => NULL,
            'depth' => 0,
            'l_value' => 1,
            'r_value' => 2,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('board_ns', $data);
        $insert_id = $this->db->insert_id();
        $this->db->query('UPDATE board_ns SET group_id = id WHERE id = ?', $insert_id);
        return $insert_id;
    }

    public function set_content_test($user_id, $id, $content)
    {
        $attributes = ['id' => $id];

        $query = $this->db->get_where('board_ns', $attributes);

        $row = $query->row_array();

        $new_left = $row['r_value'];
        $new_right = $new_left + 1;

        $data = [
            'user_id' => $user_id,
            'title' => $row['title'],
            'content' => $content,
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
            ->update('board_ns');

        $this->db->set('r_value', 'r_value + 2', FALSE)
            ->where($where_attr_right)
            ->update('board_ns');

        $this->db->insert('board_ns', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }

    private function back_tracking($parent_id, $cur_depth, $depth)
    {
        if ($cur_depth == $depth)
        {
            return ;
        }

        $width = rand(WIDTH_MIN, WIDTH_MAX);
        
        for ($i = 0; $i < $width; $i++)
        {
            $content = bin2hex(random_bytes(8));
            $id = $this->set_content_test(7, $parent_id, $content);  
            $this->back_tracking($id, $cur_depth + 1, $depth);
        }
    }

    public function do_test()
    {
        define('BOARD_NUM', 100);
        define('USER_ID', 7);

        define('DEPTH_MIN', 0);
        define('DEPTH_MAX', 5);
        
        define('WIDTH_MIN', 0);
        define('WIDTH_MAX', 5);

        $title = bin2hex(random_bytes(8));
        $content = bin2hex(random_bytes(8));

        for ($i = 0; $i <= BOARD_NUM; $i++)
        {
            $id = $this->set_board_test(USER_ID, $title, $content);
            $depth = rand(DEPTH_MIN, DEPTH_MAX);
            $this->back_tracking($id, 0, $depth);
        }
    }

    public function do_delete()
    {
        $this->db->empty_table('board_ns');
    }
}