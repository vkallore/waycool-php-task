<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_API_Controller extends CI_Controller
{
    /**
     * HTTP Status Code
     * Default to - 400 Bad Request
     */
    public static $status_code = 400;

    /**
     * Error message to print as response
     */
    public static $response_message = '';

    /**
     * Response content
     */
    public static $response = [];

    public function __construct()
    {
        parent::__construct();

        @ob_end_clean();
        header('Content-Type: application/json');
    }

    /**
     * Output your result
     */
    public function _output()
    {
        $this->output->set_status_header(self::$status_code);

        /**
         * No content to show for 204
         */
        if(self::$status_code === 204)
        {
            return;
        }

        /**
         * Print response
         */
        $response['message'] = self::$response_message;

        echo json_encode($response);

        return;
    }
}

/* End of file MY_New_API_Controller.php */
/* Location: ./application/core/MY_New_API_Controller.php */
