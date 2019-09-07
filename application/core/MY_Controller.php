
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Send email
     * @param array/string $to_email
     * @param string $subject
     * @param string $message
     * @param [] $attachments
     * @param string $bcc
     */
    public function send_email($to_email, $subject = '', $message = '', $attachments = [], $bcc = '')
    {
        // Load email library
        $this->load->library('email');

        // Email Configuration
        $config = [
            'mailtype'  => 'html',
            'protocol'  => 'smtp',
            'smtp_host' => getenv('SMTP_HOST'),
            'smtp_user' => getenv('SMTP_USER'),
            'smtp_pass' => getenv('SMTP_PASS'),
            'smtp_port' => getenv('SMTP_PORT'),
            'charset'   => 'utf8',
            'crlf'      => "\r\n",
            'newline'   => "\r\n"
        ];

        // May configure it in DB as well.
        $from_email = getenv('EMAIL_FROM');

        $this->email->initialize($config);

        $this->email->clear();

        // May configure it in DB as well.
        $this->email->from($from_email, 'WayCool');

        // Array or string
        $this->email->to($to_email);

        if(!empty($bcc))
            $this->email->bcc($bcc);


        $this->email->subject($subject);
        $this->email->message($message);

        if(!empty($attachments)){
            if(is_string($attachments)){
                $str_attachment = $attachments;
                $attachments    = [];
                $attachments[]  = $str_attachment;
            }
            foreach($attachments as $attachment){
                $this->email->attach($attachment);
            }
        }

        return $this->email->send();
    }

    /**
     * Check request and set the pagination offset and per page data
     */
    public function check_and_set_pagination_data() {
        // Load pagination
        $this->config->load('pagination', TRUE);

        $offset = (int)$this->get('offset');
        $per_page = (int)$this->get('per_page');

        $config_offset = config_item('pagination')['offset'];
        $config_per_page = config_item('pagination')['per_page'];

        $new_offset = ($offset < 0 || $offset === null) ? $config_offset : $offset;
        $new_per_page = ($per_page <= 0 || $per_page === null) ? $config_per_page : $per_page;

        $this->config->set_item('offset', $new_offset);
        $this->config->set_item('per_page', $new_per_page);
    }

    /**
     * Return rest API with meta info
     * @param array $data - Array of objects
     */
    public function api_meta_response($data) {
        if(empty($data)) {
            $response['message'] = lang('text_rest_no_records');
        }
        $response = [
            'data' => $data,
            '_meta' => [
                'total_results' => config_item('pagination')['total_results'],
                'per_page' => config_item('pagination')['per_page'],
                'offset' => config_item('pagination')['offset'],
            ]
        ];

        $this->response($response, 200);
    }
}