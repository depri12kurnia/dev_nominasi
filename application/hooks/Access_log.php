<?php
class Access_log
{

    public function log_access()
    {
        // Load CodeIgniter super object
        $CI = &get_instance();

        // Load Log_model
        $CI->load->model('M_log');

        // Prepare log data
        $data = array(
            'timestamp'  => date('Y-m-d H:i:s'),
            'ip_address' => $CI->input->ip_address(),
            'user_agent' => $CI->input->user_agent(),
            'uri'        => $CI->uri->uri_string(),
            'method'     => $CI->input->method(TRUE),
            'message'    => 'Page accessed'
        );

        // Insert log data into the database
        $CI->M_log->log_access($data);
    }
}
