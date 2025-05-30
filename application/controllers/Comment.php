<?php
class Comment extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_model');
    }

    public function get_comment($board_id)
    {
        $query_string = $this->get_qs();

        $query_parameters = [
            'like' => NULL,
            'where' => [
                'board_ns_id' => $board_id
            ],
            'table' => TABLE_COMMENT
        ];

        $data['comments'] = $this->comment_model->get_comment($board_id, $query_string['num'], $query_string['page']);
        $data['num'] = count($data['comments']);
        $data['pages'] = $this->get_pages($query_string, $query_parameters);
        $data['pages']['current_page'] = $query_string['page'];
        $data['pages']['total_data'] -= 10 * ($query_string['page'] - 1);
        $data['id'] = $board_id;
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function create_comment($board_id)
    {
        $this->comment_model->create_comment($board_id);
        echo "OK";
    }
}