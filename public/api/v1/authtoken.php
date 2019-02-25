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

include dirname(dirname(dirname(__DIR__))) . '/config/config.php';
require_once 'include/pdo.php';
include 'public/login.php';
require_once 'include/common.php';
require_once 'include/pdo.php';

use CDash\Model\AuthToken;
use CDash\Model\User;


/* Handle DELETE requests */
if (!function_exists('rest_delete')) {
    function rest_delete($userid)
    {
        $json = [];
        $hash = null;
        $response = response();
        if (isset($_REQUEST['token'])) {
            $hash = AuthToken::HashToken($_REQUEST['token']);
        } elseif (isset($_REQUEST['hash'])) {
            $hash = $_REQUEST['hash'];
        }
        if (is_null($hash)) {
            $json['error'] = 'No token or hash specified';
            return response()->json($json, 400);
//        echo json_encode($response);
//        http_response_code(400);
//        return;
        }

        $authToken = new AuthToken();
        $authToken->Hash = $hash;
        $authToken->UserId = $userid;
        if (!$authToken->Delete()) {
            $json['error'] = 'Error deleting token';
            return response()->json($json, 400);
//        echo json_encode($response);
//        http_response_code(400);
//        return;
        }
        return $response->json([], 201);
    }
}

/* Handle POST requests */
if (!function_exists('rest_post')) {
    function rest_post($userid)
    {
        $response = [];
        if (!isset($_REQUEST['description'])) {
            $response['error'] = 'No description specified';
            return response()->json($response, 400);
//        echo json_encode($response);
//        http_response_code(400);
//        return;
        }

        $authToken = new AuthToken();
        $authToken->UserId = $userid;
        $authToken->Description = $_REQUEST['description'];
        $token = $authToken->Generate();
        $authToken->Save();

        $marshaledToken = $authToken->marshal();
        // Include the actual token (not its hash) in the response.
        $marshaledToken['token'] = $token;
        $response['token'] = $marshaledToken;
        return response()->json($response, 200);
//    http_response_code(200);
//    echo json_encode($response);
    }
}

// Make sure we have a valid login.
if (!Auth::check()) {
    return response('This service requires authentication');
}

$User = Auth::user();

// Read input parameters (if any).
$rest_input = file_get_contents('php://input');
if (!is_array($rest_input)) {
    $rest_input = json_decode($rest_input, true);
}
if (is_array($rest_input)) {
    $_REQUEST = array_merge($_REQUEST, $rest_input);
}

// Route based on what type of request this is.
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'DELETE':
        return rest_delete($User->Id);
    case 'POST':
    default:
        return rest_post($User->Id);
}

