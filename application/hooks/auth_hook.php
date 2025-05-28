<?php
/**
 * post_controller_constructor 
 *
 * 컨트롤러가 인스턴스화 된 직후
 */
class auth_hook
{

    private $ci = NULL;

    public function init()
    {
        $this->ci =& get_instance();
        $this->ci->load->helper('url');
        $this->ci->load->library('session');
        
        $this->_auth_check();
    }

    private function _auth_check()
    {
        define('PATH_PREFIX', 'board_v2');
        
        $white_list = [
            PATH_PREFIX,
            PATH_PREFIX . '/login',
            PATH_PREFIX . '/index',
            PATH_PREFIX . '/view/*',
            PATH_PREFIX . '/get_comment/*'
        ];

        $uri = uri_string();

        foreach ($white_list as $allowed)
        {
            log_message('error', 'PATH_PREFIX: ' . $allowed);
            log_message('error', 'URI: ' . $uri);
            if (fnmatch($allowed, $uri))
            {
                return;
            }
        }

        if (!$this->ci->session->userdata('username'))
        {
            redirect(PATH_PREFIX . '/login');
        }
    }
}
