<?php

/**
 * @param integer $length - Length of password expected
 * @return string - Randomg password
 */
function random_string($length = 10) {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $password = [];
    $alpha_length = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++)  {
        $n = rand(0, $alpha_length);
        $password[] = $alphabet[$n];
    }
    return implode($password);
}
?>