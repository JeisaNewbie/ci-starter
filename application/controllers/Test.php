<?php
class Test extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('test_model');
    }

    public function test($method = NULL)
    {
        if ($method == 'delete')
        {
            $this->test_model->do_delete();
        }
        else if ($method == 'create')
        {
            $this->test_model->do_test();
        }
        else
        {
            log_message('error', 'test called');
        }

        redirect('/board_v2');
    }
}