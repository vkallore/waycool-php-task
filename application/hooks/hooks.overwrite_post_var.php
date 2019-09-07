<?php
/**
 * Hack to Override CI XSS Filtering in some functions
 */
function overwrite_post_var ($post = array())
{
    if($_SERVER['REQUEST_METHOD'] === 'POST' && empty($post)) {
        $enc_post = json_decode(file_get_contents('php://input'), TRUE);
        $_POST = $enc_post;
    }
}