<?php
class Comment extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('comment_model');
    }

    public function get_comment($id, $num = 20, $page = 1)
    {
        $query_string = $this->get_qs();

        $query_parameters = [
            'like' => NULL,
            'where' => [
                'board_ns_id' => $id
            ],
            'table' => TABLE_COMMENT
        ];

        $data['comments'] = $this->comment_model->get_comment($id, $num, $page);
        $data['num'] = count($data['comments']);
        $data['pages'] = $this->get_pages($query_string, $query_parameters);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    public function set_comment($id)
    {
        $this->comment_model->set_comment($id);
        echo "OK";
    }
}