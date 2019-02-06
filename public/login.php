<?php
/*=========================================================================
  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id$
  Language:  PHP
  Date:      $Date$
  Version:   $Revision$

  Copyright (c) Kitware, Inc. All rights reserved.
  See LICENSE or http://www.cdash.org/licensing/ for details.

  This software is distributed WITHOUT ANY WARRANTY; without even
  the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
  PURPOSE. See the above copyright notices for more information.
=========================================================================*/

use CDash\Config;
use Illuminate\Support\Collection;

include dirname(__DIR__) . '/config/config.php';
include_once 'include/common.php';
require_once 'include/pdo.php';
include_once 'include/version.php';
include_once 'include/login_functions.php';

$config = Config::getInstance();

// --------------------------------------------------------------------------------------
// main
// --------------------------------------------------------------------------------------
// $mysession = ['login' => false, 'passwd' => false, 'ID' => false, 'valid' => false, 'langage' => false];

$session_OK = (int)cdash_auth();

if (!$session_OK && !@$noforcelogin) {
    $errors = Collection::make([]);
    echo view(
        'auth.login',
        [
            'title' => 'Login',
            'errors' => $errors,
        ]
    )->render();
    return;
}
