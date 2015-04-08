<?php

@define('PROJECT_DIR', realpath(dirname(__DIR__)));
@define('TESTS_DIR', PROJECT_DIR . '/tests');
@define('FIXTURES_DIR', TESTS_DIR . '/fixtures');
@define('TMP_DIR', FIXTURES_DIR . '/tmp');

include_once PROJECT_DIR . '/bootstrap.php';
