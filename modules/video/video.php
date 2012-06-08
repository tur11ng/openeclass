<?php
/* ========================================================================
 * Open eClass 3.0
 * E-learning and Course Management System
 * ========================================================================
 * Copyright 2003-2012  Greek Universities Network - GUnet
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

/*
 * video
 *
 * @author Dimitris Tsachalis <ditsa@ccf.auth.gr>
 * @author Evelthon Prodromou <eprodromou@upnet.gr>
 * @version $Id$
 *
 * @abstract
 *
 */
/*******************************************************************
*			   VIDEO UPLOADER AND DOWNLOADER
********************************************************************

The script makes 5 things:
1. Upload video
2. Give them a name
3. Modify data about video
4. Delete link to video and simultaneously remove them
5. Show video list to students and visitors

On the long run, the idea is to allow sending realvideo . Which means only
establish a correspondence between RealServer Content Path and the user's
documents path.

*/

$require_current_course = TRUE;
$require_help = TRUE;
$helpTopic = 'Video';
$guest_allowed = true;

include '../../include/baseTheme.php';
include_once "../../include/lib/fileUploadLib.inc.php";

/**** The following is added for statistics purposes ***/
include('../../include/action.php');
$action = new action();
$action->record(MODULE_ID_VIDEO);
/**************************************/

include '../../include/lib/forcedownload.php';
require_once 'video_functions.php';
include '../../include/log.php';

$nameTools = $langVideo;

if (isset($_SESSION['prenom'])) { 
        $nick = q($_SESSION['prenom'].' '.$_SESSION['nom']);
}

// ----------------------
// download video
// ----------------------

if (isset($_GET['action']) and $_GET['action'] == "download") {
	$id = $_GET['id'];
	$real_file = $webDir."/video/".$course_code."/".$id;
	if (strpos($real_file, '/../') === FALSE) {
                $result = db_query("SELECT url FROM video WHERE course_id = $course_id AND path = " .
                                   autoquote($id), $mysqlMainDb);
		$row = mysql_fetch_array($result);
		if (!empty($row['url'])) {
			$id = $row['url'];
		}
		send_file_to_client($real_file, my_basename($id), 'inline', true);
		exit;
	} else {
		header("Refresh: ${urlServer}modules/video/video.php?course=$course_code");
	}
}

// ----------------------
// play video
// ----------------------

if (isset($_GET['action']) and $_GET['action'] == "play")
{
        $id = $_GET['id'];
        $videoPath = $urlServer ."video/". $course_code . $id;
        $videoURL = "$_SERVER[PHP_SELF]?course=$course_code&amp;action=download&amp;id=". $id;
        
        if (strpos($videoPath, '/../') === FALSE)
        {
            echo media_html_object($videoPath, $videoURL);
        }
        else
        {
            header("Refresh: ${urlServer}modules/video/video.php?course=$course_code");
        }
        exit;
}

// ----------------------
// play videolink
// ----------------------

if (isset($_GET['action']) and $_GET['action'] == "playlink")
{
        $id = $_GET['id'];
        
        echo medialink_iframe_object(html_entity_decode($id));
        exit;
}


if($is_editor) {
        load_js('tools.js');
        load_modal_box(true);
        $head_content .= <<<hContent
<script type="text/javascript">
function checkrequired(which, entry) {
	var pass=true;
	if (document.images) {
		for (i=0;i<which.length;i++) {
			var tempobj=which.elements[i];
			if (tempobj.name == entry) {
				if (tempobj.type=="text"&&tempobj.value=='') {
					pass=false;
					break;
		  		}
	  		}
		}
	}
	if (!pass) {
		alert("$langEmptyVideoTitle");
		return false;
	} else {
		return true;
	}
}

</script>
hContent;
	
$d = mysql_fetch_array(db_query("SELECT video_quota FROM course WHERE code='$course_code'"));
$diskQuotaVideo = $d['video_quota'];
$updir = "$webDir/video/$course_code"; //path to upload directory
$diskUsed = dir_total_space($updir);

if (isset($_GET['showQuota']) and $_GET['showQuota'] == TRUE) {
	$nameTools = $langQuotaBar;
	$navigation[] = array('url' => "$_SERVER[PHP_SELF]?course=$course_code", 'name' => $langVideo);
	$tool_content .= showquota($diskQuotaVideo, $diskUsed);
	draw($tool_content, 2);
	exit;
}	

if (isset($_POST['edit_submit'])) { // edit
	if(isset($_POST['id'])) {
		$id = intval($_POST['id']);
		if (isset($_POST['table'])) {
			$table = $_POST['table'];
		}
		if ($table == 'video') {
			$sql = "UPDATE video SET title = ".autoquote($_POST['title']).",
                                                 description = ".autoquote($_POST['description']).",
                                                 creator = ".autoquote($_POST['creator']).",
                                                 publisher = ".autoquote($_POST['publisher'])."
                                             WHERE id = $id";	
		} elseif ($table == 'videolinks') {
			$sql = "UPDATE videolinks SET url = ".autoquote(canonicalize_url($_POST['url'])).",
                                                      title = ".autoquote($_POST['title']).",
                                                      description = ".autoquote($_POST['description']).",
                                                      creator = ".autoquote($_POST['creator']).",
                                                      publisher = ".autoquote($_POST['publisher'])."
                                                  WHERE id = $id";
		}
		$result = db_query($sql, $mysqlMainDb);                
                $txt_description = ellipsize(canonicalize_whitespace(strip_tags($_POST['description'])), 50, '+');
                Log::record(MODULE_ID_VIDEO, LOG_MODIFY,
                          array('id' => $id,                          
                                'url' => autoquote(canonicalize_url($_POST['url'])),
                                'title' => autoquote($_POST['title']),     
                                'desc' => $txt_description));
                
		$tool_content .= "<p class='success'>$langTitleMod</p><br />";
		$id = "";
	}
}	
if (isset($_POST['add_submit'])) {  // add
		if(isset($_POST['URL'])) { // add videolinks
			$url = $_POST['URL'];
			if ($_POST['title'] == '') {
				$title = $url;
			} else {
				$title = $_POST['title'];
			}
			$sql = 'INSERT INTO videolinks (course_id, url, title, description, creator, publisher, date)
                                VALUES ('.autoquote($course_id).',
                               		'.autoquote(canonicalize_url($url)).',
                                        '.autoquote($title).',
					'.autoquote($_POST['description']).',
                                        '.autoquote($_POST['creator']).',
                                        '.autoquote($_POST['publisher']).',
                                        '.autoquote($_POST['date']).')';
			$result = db_query($sql, $mysqlMainDb);
                        $id = mysql_insert_id();				
                        $txt_description = ellipsize(canonicalize_whitespace(strip_tags($_POST['description'])), 50, '+');
                        Log::record(MODULE_ID_VIDEO, LOG_INSERT,
                                @array('id' => $id,
                                       'url' => autoquote(canonicalize_url($url)),
                                       'title' => $title,
                                       'desc' => $txt_description));
			$tool_content .= "<p class='success'>$langLinkAdded</p><br />";
		} else {  // add video
			if (isset($_FILES['userFile']) && is_uploaded_file($_FILES['userFile']['tmp_name'])) {
				if ($diskUsed + @$_FILES['userFile']['size'] > $diskQuotaVideo) {
					$tool_content .= "<p class='caution'>$langNoSpace<br />
						<a href='$_SERVER[PHP_SELF]?course=$course_code'>$langBack</a></p><br />";
						draw($tool_content, 2, null, $head_content);
						exit;
				} else {
					$file_name = $_FILES['userFile']['name'];
					$tmpfile = $_FILES['userFile']['tmp_name'];
					// convert php file in phps to protect the platform against malicious codes
					$file_name = preg_replace("/\.php$/", ".phps", $file_name);
					// check for dangerous file extensions
					if (preg_match('/\.(ade|adp|bas|bat|chm|cmd|com|cpl|crt|exe|hlp|hta|' .'inf|ins|isp|jse|lnk|mdb|mde|msc|msi|msp|mst|pcd|pif|reg|scr|sct|shs|' .'shb|url|vbe|vbs|wsc|wsf|wsh)$/', $file_name)) {
						$tool_content .= "<p class='caution'>$langUnwantedFiletype:  $file_name<br />";
						$tool_content .= "<a href='$_SERVER[PHP_SELF]?course=$course_code'>$langBack</a></p><br />";
						draw($tool_content, 2, null, $head_content);
						exit;
					}
					$file_name = str_replace(" ", "%20", $file_name);
					$file_name = str_replace("%20", "", $file_name);
					$file_name = str_replace("\'", "", $file_name);
					$safe_filename = date("YmdGis").randomkeys("8").".".get_file_extension($file_name);
					$iscopy = copy("$tmpfile", "$updir/$safe_filename");
					if(!$iscopy) {
						$tool_content .= "<p class='success'>$langFileNot<br />
						<a href='$_SERVER[PHP_SELF]?course=$course_code'>$langBack</a></p><br />";
						draw($tool_content, 2, null, $head_content);
						exit;
					}
					$path = '/' . $safe_filename;
					$url = $file_name;
                                        $sql = 'INSERT INTO video
                                                       (course_id, path, url, title, description, creator, publisher, date)
                                                       VALUES ('.autoquote($course_id).', '.
                                                                 quote($path).', '.
                                                                 autoquote($url).', '.
                                                                 autoquote($_POST['title']).', '.
                                                                 autoquote($_POST['description']).', '.
                                                                 autoquote($_POST['creator']).', '.
                                                                 autoquote($_POST['publisher']).', '.
                                                                 autoquote($_POST['date']).')';
				}
                                $id = mysql_insert_id();
				$result = db_query($sql, $mysqlMainDb);
                                $txt_description = ellipsize(canonicalize_whitespace(strip_tags($_POST['description'])), 50, '+');
                                Log::record(MODULE_ID_VIDEO, LOG_INSERT,
                                        @array('id' => $id,
                                                'path' => quote($path),
                                                'url' => $_POST['url'],
                                                'title' => autoquote($_POST['title']),
                                                 'desc' => $txt_description));
				$tool_content .= "<p class='success'>$langFAdd</p><br />";
			}
		}
	}	// end of add
	if (isset($_GET['delete'])) { // delete
		$id = intval($_GET['id']);
		$table = $_GET['table'];
		$sql_select="SELECT * FROM $table WHERE course_id = $course_id AND id='".mysql_real_escape_string($id)."'";
		$result = db_query($sql_select, $mysqlMainDb);
		$myrow = mysql_fetch_array($result);
		if($table == "video") {
			unlink("$webDir/video/$course_code/".$myrow['path']);
		}
		$sql = "DELETE FROM $table WHERE course_id = $course_id AND id='".mysql_real_escape_string($id)."'";
		$result = db_query($sql, $mysqlMainDb);
                Log::record(MODULE_ID_VIDEO, LOG_DELETE, array('id' => $id));
		$tool_content .= "<p class='success'>$langDelF</p><br />";
		$id = "";
	} elseif (isset($_GET['form_input']) && $_GET['form_input'] == 'file') { // display video form
		$nameTools = $langAddV;
		$navigation[] = array('url' => "video.php?course=$course_code", 'name' => $langVideo);
		$tool_content .= "
              <form method='POST' action='$_SERVER[PHP_SELF]?course=$course_code' enctype='multipart/form-data' onsubmit=\"return checkrequired(this, 'title');\">
              <fieldset>
              <legend>$langAddV</legend>
		<table width='100%' class='tbl'>
		<tr>
		  <th valign='top'>$langWorkFile:</th>
		  <td>
		    <input type='hidden' name='id' value=''>
		    <input type='file' name='userFile' size='38'>
                    <br />
                   <span class='smaller'>$langPathUploadFile</span>
		  </td>
		<tr>
		  <th>$langTitle:</th>
		  <td><input type='text' name='title' size='55'></td>
		</tr>
		<tr>
		  <th>$langDescr:</th>
		  <td><textarea rows='3' name='description' cols='52'></textarea></td>
		</tr>
		<tr>
		  <th>$langcreator:</th>
		  <td><input type='text' name='creator' value='$nick' size='55'></td>
		</tr>
		<tr>
		  <th>$langpublisher:</th>
		  <td><input type='text' name='publisher' value='$nick' size='55'></td>
		</tr>
		<tr>
		  <th>$langDate:</th>
		  <td><input type='text' name='date' value='".date('Y-m-d G:i:s')."' size='55'></td>
		</tr>
		<tr>
		  <th>&nbsp;</th>
		  <td class='right'><input type='submit' name='add_submit' value='$dropbox_lang[uploadFile]'></td>
		</tr>

		</table>
        </fieldset>
              <div class='smaller right'>$langMaxFileSize ". ini_get('upload_max_filesize') . "</div></form> <br>";        
              
	} elseif (isset($_GET['form_input']) && $_GET['form_input'] == 'url') { // display video links form
		$nameTools = $langAddVideoLink;
		$navigation[] = array ('url' => "video.php?course=$course_code", 'name' => $langVideo);
		$tool_content .= "
		<form method='post' action='$_SERVER[PHP_SELF]?course=$course_code' onsubmit=\"return checkrequired(this, 'title');\">
                <fieldset>
                <legend>$langAddVideoLink</legend>
		<table width='100%' class='tbl'>
		<tr>
		  <th valign='top' width='190'>$langGiveURL:<input type='hidden' name='id' value=''></th>
		  <td class='smaller'><input type='text' name='URL' size='55'>
                      <br />
                      $langURL
                  </td>
		<tr>
		  <th>$langTitle:</th>
		  <td><input type='text' name='title' size='55'></td>
		</tr>
		<tr>
		  <th>$langDescr:</th>
		  <td><textarea rows='3' name='description' cols='52'></textarea></td>
		</tr>
		<tr>
		  <th>$langcreator:</th>
		  <td><input type='text' name='creator' value='$nick' size='55'></td>
		</tr>
		<tr>
		  <th>$langpublisher:</th>
		  <td><input type='text' name='publisher' value='$nick' size='55'></td>
		</tr>
		<tr>
		  <th>$langDate:</th>
		  <td><input type='text' name='date' value='".date('Y-m-d G:i')."' size='55'></td>
		</tr>
		<tr>
		  <th>&nbsp;</th>
		  <td class='right'><input type='submit' name='add_submit' value='$langAdd'></td>
		</tr>
		</table>
                </fieldset>
		</form>
		<br/>";
	}

// ------------------- if no submit -----------------------
if (isset($_GET['id']) and isset($_GET['table_edit']))  {
	$id = intval($_GET['id']);
	$table_edit = $_GET['table_edit'];
	if ($id) {
		$sql = "SELECT * FROM $table_edit WHERE course_id = $course_id AND id = $id ORDER BY title";
		$result = db_query($sql, $mysqlMainDb);
		$myrow = mysql_fetch_array($result);
		
		$id = $myrow['id'];
		$url= $myrow['url'];
		$title = $myrow['title'];
		$description = $myrow['description'];
		$creator = $myrow['creator'];
		$publisher = $myrow['publisher'];
		
		$nameTools = $langModify;
		$navigation[] = array ('url' => "video.php?course=$course_code", 'name' => $langVideo);
		$tool_content .= "
                <form method='POST' action='$_SERVER[PHP_SELF]?course=$course_code' onsubmit=\"return checkrequired(this, 'title');\">
                <fieldset>
                <legend>$langModify</legend>
                <table width='100%' class='tbl'>";
		if ($table_edit == 'videolinks') {
			$tool_content .= "
                        <tr>
                        <th>$langURL:</th>
                        <td><input type='text' name='url' value='".q($url)."' size='55'></td>
                        </tr>";
		}
		elseif ($table_edit == 'video') {
			$tool_content .= "<input type='hidden' name='url' value='".q($url)."'>";
		}
		@$tool_content .= "
		<tr>
		  <th width='90'>$langTitle:</th>
		  <td><input type='text' name='title' value='".q($title)."' size='55'></td>
		</tr>
		<tr>
		  <th>$langDescr:</th>
		  <td><textarea rows='3' name='description' cols='52'>".q($description)."</textarea></td>
	       </tr>
	       <tr>
		 <th>$langcreator:</th>
		 <td><input type='text' name='creator' value='".q($creator)."' size='55'></td>
	       </tr>
	       <tr>
		 <th>$langpublisher:</th>
		 <td><input type='text' name='publisher' value='".q($publisher)."' size='55'></td>
	       </tr>
	       <tr>
		 <th>&nbsp;</th>
		 <td class='right'><input type='submit' name='edit_submit' value='$langModify'>
		     <input type='hidden' name='id' value='".$id."'>
		     <input type='hidden' name='table' value='".$table_edit."'>
		 </td>
	       </tr>
	       </table>
	       </fieldset>
	       </form>
	       <br/>";
	}
}	// if id

if (!isset($_GET['form_input'])) {
          $tool_content .= "
          <div id='operations_container'>
	  <ul id='opslist'>
	    <li><a href='$_SERVER[PHP_SELF]?course=$course_code&amp;form_input=file'>$langAddV</a></li>
	    <li><a href='$_SERVER[PHP_SELF]?course=$course_code&amp;form_input=url'>$langAddVideoLink</a></li>
	    <li><a href='$_SERVER[PHP_SELF]?course=$course_code&amp;showQuota=TRUE'>$langQuotaBar</a></li>
	  </ul>
	</div>";
}

$count_video = mysql_fetch_array(db_query("SELECT count(*) FROM video WHERE course_id = $course_id ORDER BY title", $mysqlMainDb));
$count_video_links = mysql_fetch_array(db_query("SELECT count(*) FROM videolinks WHERE course_id = $course_id
				ORDER BY title", $mysqlMainDb));

if ($count_video[0]<>0 || $count_video_links[0]<>0) {
        // print the list if there is no editing
        $results['video'] = db_query("SELECT * FROM video WHERE course_id = $course_id ORDER BY title", $mysqlMainDb);
        $results['videolinks'] = db_query("SELECT * FROM videolinks WHERE course_id = $course_id ORDER BY title", $mysqlMainDb);
        $i = 0;
        $count_video_presented_for_admin = 1;
        $tool_content .= "
        <table width='100%' class='tbl_alt'>
        <tr>     
          <th colspan='2'><div align='left'>$langVideoDirectory</div></th>
          <th width='150'><div align='left'>$langcreator</div></th>
          <th width='150'><div align='left'>$langpublisher</div></th>
          <th width='70'>$langDate</th>
          <th width='70'>$langActions</th>
        </tr>";
        foreach($results as $table => $result)
                while ($myrow = mysql_fetch_array($result)) {
                        switch($table){
				case 'video':
					if (isset($vodServer)) {
                                            $mediaURL = $vodServer."$course_code/".$myrow['path'];
                                            $mediaPath = $mediaURL;
                                            $mediaPlay = $mediaURL;
					} else {
                                            list($mediaURL, $mediaPath, $mediaPlay) = media_url($myrow['path']);
					}
                                        $link_to_add = "<td>". choose_media_ahref($mediaURL, $mediaPath, $mediaPlay, q($myrow['title']), $myrow['path']) ."<br>\n".
                                                q($myrow['description']) . "</td><td>" .
                                                q($myrow['creator']) . "</td><td>" .
                                                q($myrow['publisher']) . "</td><td align='center'>".
                                                nice_format(date('Y-m-d', strtotime($myrow['date'])))."</td>";
                                        $link_to_save = "<a href='$mediaURL'><img src='$themeimg/save_s.png' alt='$langSave' title='$langSave'></a>&nbsp;&nbsp;";
					break;
				case "videolinks":
                                        $link_to_add = "<td>". choose_medialink_ahref(q($myrow['url']), q($myrow['title'])) ."<br>" .
                                                q($myrow['description']) . "</td><td>" .
                                                q($myrow['creator']) . "</td><td>" .
                                                q($myrow['publisher']) . "</td><td align='center'>" .
                                                nice_format(date('Y-m-d', strtotime($myrow['date']))) .
                                                "</td>";
                                        $link_to_save = "<a href='".q($myrow['url'])."' target='_blank'><img src='$themeimg/links_on.png' alt='$langPreview' title='$langPreview'></a>&nbsp;&nbsp;";
					break;
				default:
					exit;
			}
                        if ($i%2) {
				$rowClass = "class='odd'";
			} else {
				$rowClass = "class='even'";
			}
                        $tool_content .= "
                                <tr $rowClass>
                                   <td width='1' valign='top'>
                                      <img style='padding-top:3px;' src='$themeimg/arrow.png' alt=''>
                                   </td>
                                   $link_to_add
                                   <td align='right'>
                                      $link_to_save<a href='$_SERVER[PHP_SELF]?course=$course_code&amp;id=".$myrow['id']."&amp;table_edit=$table'><img src='$themeimg/edit.png' title='$langModify'></a>&nbsp;&nbsp;<a href='$_SERVER[PHP_SELF]?course=$course_code&amp;id=".$myrow['id']."&amp;delete=yes&amp;table=$table' onClick=\"return confirmation('".js_escape($langConfirmDelete ." ". $myrow['title'])."');\"><img src='$themeimg/delete.png' title='$langDelete'></a>
                                   </td>
                                </tr>";
                        $i++;
                        $count_video_presented_for_admin++;
		} // while
		$tool_content.="</table>";
	}
	else
	{
		$tool_content .= "<p class='alert1'>$langNoVideo</p>";
	}
}   // if uid=prof_id

// student view
else {
    
    load_modal_box(true);
    
	$results['video'] = db_query("SELECT * FROM video WHERE course_id = $course_id ORDER BY title", $mysqlMainDb);
	$results['videolinks'] = db_query("SELECT * FROM videolinks WHERE course_id = $course_id ORDER BY title", $mysqlMainDb);
	$count_video = mysql_fetch_array(db_query("SELECT count(*) FROM video WHERE course_id = $course_id", $mysqlMainDb));
	$count_video_links = mysql_fetch_array(db_query("SELECT count(*) FROM videolinks WHERE course_id = $course_id", $mysqlMainDb));
	if ($count_video[0]<>0 || $count_video_links[0]<>0) {
		$tool_content .= "
		<table width='100%' class='tbl_alt'>
		<tr>
                  <th colspan='2'><div align='left'>$langDirectory $langVideo</div></th>
                  <th width='70'>$langActions</th>
		</tr>";
		$i=0;
		$count_video_presented=1;
		foreach($results as $table => $result) {
			while ($myrow = mysql_fetch_array($result)) {
				switch($table){
					case 'video':
						if (isset($vodServer)) {
                                                    $mediaURL = $vodServer."$course_code/".$myrow['path'];
                                                    $mediaPath = $mediaURL;
                                                    $mediaPlay = $mediaURL;
						} else {
                                                    list($mediaURL, $mediaPath, $mediaPlay) = media_url($myrow['path']);
						}
                                                $link_to_add = "<td>". choose_media_ahref($mediaURL, $mediaPath, $mediaPlay, q($myrow['title']), $myrow['path']) ."<br /><small>" .
                                                        q($myrow['description']) . "</small></td>";
                                                $link_to_save = "<a href='$mediaURL'><img src='$themeimg/save_s.png' alt='$langSave' title='$langSave'></a>&nbsp;&nbsp;";
						break;
					case 'videolinks':
                                                $link_to_add = "<td>". choose_medialink_ahref(q($myrow['url']), q($myrow['title'])) ."<br />" .
                                                        q($myrow['description']) . "</td>";
                                                $link_to_save = "<a href='".q($myrow['url'])."' target='_blank'><img src='$themeimg/links_on.png' alt='$langPreview' title='$langPreview'></a>&nbsp;&nbsp;";
						break;
					default:
						exit;
				}
				if ($i%2) {
					$rowClass = "class='odd'";
				} else {
					$rowClass = "class='even'";
				}
				$tool_content .= "<tr $rowClass>";
				$tool_content .= "<td width='1' valign='top'><img style='padding-top:3px;' src='$themeimg/arrow.png' alt=''></td>";
				$tool_content .= $link_to_add;
                                $tool_content .= "<td align='center'>$link_to_save</td>";
				$tool_content .= "</tr>";
				$i++;
				$count_video_presented++;
			}
		}
		$tool_content .= "</table>\n";
	} else {
		$tool_content .= "<p class='alert1'>$langNoVideo</p>";
	}
}

add_units_navigation(TRUE);

if (isset($head_content)) {
	draw($tool_content, 2, null, $head_content);
} else {
        draw($tool_content, 2);
}
