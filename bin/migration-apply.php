<?php
require_once('bootstrap.php');
passthru('php doctrine.php migrations:migrate');