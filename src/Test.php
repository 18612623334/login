<?php

namespace wangliang\Test;

use Illuminate\Session\SessionManager;
use Illuminate\Config\Repository;

class Test
{
    /**
     * @param string $msg
     * @return string
     */
    public function test_rtn($msg = ''){
        echo 'running' . "\n";
    }
}