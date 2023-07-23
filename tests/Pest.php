<?php

function d($var)
{
    ob_end_clean();
    var_dump($var);
}