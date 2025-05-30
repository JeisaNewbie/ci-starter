<?php
/**
 * post_controller_constructor 
 *
 * 컨트롤러가 인스턴스화 된 직후
 */
class auth_hook
{

    private $ci = NULL;

    private $white_list = [
        BOARD_PREFIX,
        BOARD_PREFIX . '/index',
        BOARD_PREFIX . '/view/*',
        COMMENT_PREFIX . '/get_comment/*',
        AUTH_PREFIX . '/*',
        TEST_PREFIX . '/*'
    ];

    public function init()
    {
        $this->ci =& get_instance();
        $this->ci->load->helper('url');
        $this->ci->load->library('session');
        
        $this->auth_check();
    }

    private function auth_check()
    {
        $uri = uri_string();

        foreach ($this->white_list as $allowed)
        {
            if (fnmatch($allowed, $uri))
            {
                return;
            }
        }

        if (!$this->ci->session->userdata('id'))
        {
            redirect('auth' . '/login');
        }
    }
}
