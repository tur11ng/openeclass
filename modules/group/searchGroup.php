<?php

/* ========================================================================
 * Open eClass 3.0
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2014  Greek Universities Network - GUnet
 * A full copyright notice can be read in "/info/copyright.txt".
 * For a full list of contributors, see "credits.txt".
 *
 * Open eClass is an open platform distributed in the hope that it will
 * be useful (without any warranty), under the terms of the GNU (General
 * Public License) as published by the Free Software Foundation.
 * The full license can be read in "/info/license/license_gpl.txt".
 *
 * Contact address: GUnet Asynchronous eLearning Group,
 *                  Network Operations Center, University of Athens,
 *                  Panepistimiopolis Ilissia, 15784, Athens, Greece
 *                  e-mail: info@openeclass.org
 * ======================================================================== */


 require_once '../../include/baseTheme.php';

 if(isset($_GET['q']['term'])){
     $q = $_GET['q']['term'];
     $c_id = $_GET['course'];
 
     $taglist = Database::get()->queryArray("SELECT id, name FROM `group` WHERE name LIKE ?s AND course_id = ?d ORDER BY name", "%$q%",$c_id);
     if ($taglist) {
         foreach ($taglist as $tag) {
             $tags[] = array('id' => $tag->name, 'text' => $tag->name);
         }
     } else {
         $tags = array();
     }
 
     echo json_encode($tags);
 }
 
