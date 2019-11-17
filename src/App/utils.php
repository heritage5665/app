<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include_once 'EncDec.php';

use \ForceUTF8\Encoding;  // It's namespaced now.

//include_once 'Kairos.php';


function default_limit($limit = 10) {
    return $limit;
}

function default_follow_chatmessage($name) {
    return 'You are now connected with ' . $name;
}

function default_comment_limit() {
    return 10;
}

function feedtype_number() {
    return 1;
}

function followtype_number() {
    return 2;
}

function connectiontype_number() {
    return 3;
}

function feed_media_type_number() {
    return 4;
}

function mobile_articles_type() {
    return 1;
}

function location_articles_type() {
    return 2;
}

function FormatData_Character_encode($data) {
    $newText = Encoding::toUTF8($data);
    return $newText;
}

function ReadUrl() {
//    $ReadBaseUrl = 'http://localhost/hiddenWebMobile/index.php?';
  //  http://articles.hidden-history.com/example8.php?group=2&uni=3
//    https://hidden-history.com/subdomain/articles/example8.php?group=1&uni=25
//    $ReadBaseUrl = 'http://articles.hidden-history.com/example8.php?';
    $ReadBaseUrl = 'https://hidden-history.com/subdomain/articles/example8.php?';
//    $ReadBaseUrl = 'https://articles.hidden-history.com/example8.php?';
    return $ReadBaseUrl;
}

function FormReadUrl($type, $id) {
    $ReadUrl = ReadUrl() . 'group=' . $type . '&uni=' . $id . '';
    return $ReadUrl;
}

function verifyRequiredParams($required_fields, $response, $app) {
    //Assuming there is no error
    $error = false;

    //Error fields are blank
    $error_fields = "";

    //Getting the request parameters
    $request_params = $_REQUEST;

    //Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        //Getting the app instance
        $app = \Slim\Slim::getInstance();

        //Getting put parameters in request params variable
        parse_str($app->request()->getBody(), $request_params);
    }
//    print_r($required_fields);
    //Looping through all the parameters
    foreach ($required_fields as $field) {

        //if any requred parameter is missing
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            //error is true
            $error = true;

            //Concatnating the missing parameters in error fields
            $error_fields .= $field . ', ';
        }
    }

    //if there is a parameter missing then error is true
    if ($error) {
        //Creating response array
        $resp = array();

        //Getting app instance
//        $app = \Slim\Slim::getInstance();
        //Adding values to response array
        $resp["error"] = true;
        $resp["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        return $resp;
//       return ErrorMessage($response, $resp);
//        die();
    }
}

function SuccessMessage($response, $info) {
    $resp = array();
    $res = array('status' => TRUE,
        'message' => $info,
    );
    $returndata = $response->withJson($res);
    return $returndata;
}

function SuccessMessageWithData($param, $response, $info, $resp_type = 'JSON') {

    if ($resp_type === 'XML') {
//        $xmldata=json2Xml($param);
//        print_r($xmldata);

        $xmldata = array2xml($param, false);
        return $xmldata;
    } else {
        $resp = array();
        $res = array('status' => TRUE,
            'data' => $param,
            'message' => $info,
        );
//    $returndata= json_encode($res);
        $returndata = $response->withJson(FormatData_Character_encode($res), 201);
        return $returndata;
//    print_r(gettype($returndata));
//  return $response->withJson($res, 201);  
    }
}

function SuccessMessageWithDoubleData($param, $totalcount, $response, $info) {
    $resp = array();
    $res = array('status' => TRUE,
        'data' => $param,
        'total_count' => $totalcount,
        'message' => $info,
    );
//    $returndata= json_encode($res);
    $returndata = $response->withJson($res);
    return $returndata;
}

function SuccessMessageWithCustomDoubleData($param, $data, $fieldsname, $response, $info) {
    $resp = array();
    $res = array('status' => TRUE,
        'data' => $param,
        $fieldsname => $data,
        'message' => $info,
    );
//    $returndata= json_encode($res);
    $returndata = $response->withJson($res);
    return $returndata;
}

function SuccessMessageForConnection($param, $mentee_details, $mentor_details, $response, $info) {
    $resp = array();
    $res = array('status' => TRUE,
        'data' => $param,
        'mentee_details' => $mentee_details,
        'mentor_details' => $mentor_details,
        'message' => $info,
    );
//    $returndata= json_encode($res);
    $returndata = $response->withJson($res);
    return $returndata;
}

function ErrorMessage($response, $info) {
    $resp = array();
    $res = array('status' => FALSE,
        'message' => $info,
//        'status_code' => $response->withStatus(302),
    );
//    print_r($res);

    $returndata = $response->withJson($res);
    return $returndata;
//    echo $returndata;
//    echo json_encode($res);
}

function ErrorMessagewithData($response, $data, $info) {
    $resp = array();
    $res = array('status' => FALSE,
        'message' => $info,
        'data' => $data,
    );

    $returndata = $response->withJson($res);
    return $returndata;
}

function image_url($imageName = "") {
    $url = "https://hidden-history.com/public/assets/images/$imageName";
//        https://hidden-history.com/public/assets/images/big-ben-header-image-mobile.jpg
    //$npath = $url;
    return $url;
}

//function convert_from_latin1_to_utf8_recursively($dat) {
//    if (is_string($dat)) {
//        return utf8_encode($dat);
//    } elseif (is_array($dat)) {
//        $ret = [];
//        foreach ($dat as $i => $d)
//            $ret[$i] = self::convert_from_latin1_to_utf8_recursively($d);
//
//        return $ret;
//    } elseif (is_object($dat)) {
//        foreach ($dat as $i => $d)
//            $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);
//
//        return $dat;
//    } else {
//        return $dat;
//    }
//}

function FetchFeaturedArticle($db, $limit = 10) {
    $limit = default_limit();
    $sql = "select * from mobile_articles where visible = 1 and featured = 1 order by rand() asc limit 0," . $limit;
    $result = $db->select($sql);
    // print_r($result);
    if (isset($result) && !empty($result)) {
        $temp_array = [];
        foreach ($result as $rs) {
            $rs["articleimage"] = image_url($rs["articleimage"]);
            $rs["author_name"] = GetAuthorName($db, $rs["author_id"]);
            $rs["RelatedImages"] = GetRelatedImages($db, $rs["articlesid"]);
            $rs["Short_discription"] = ReduceText($rs["article"], 150);
            $rs["readUrl"] = FormReadUrl(mobile_articles_type(), $rs["articlesid"]);

            //            $rs["article"] = mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');

            $rs["article_text"] = $rs["article"];
            $rs["article_title"] = $rs["articletitle"];
            $rs["article_image"] = $rs["articleimage"];
            array_push($temp_array, $rs);
        }

        return $temp_array;
    } else {
        return [];
    }
}

function GetSingleMobileArticle($db, $article_id, $limit = 10) {
    // $limit = default_limit();
    $sql = "SELECT `mobile_articles`.* FROM `mobile_articles` WHERE `mobile_articles`.`visible` = 1 AND `mobile_articles`.`articlesid` =" . $article_id . " ORDER BY `mobile_articles`.`categoryid` DESC ";
    // print_r($sql);
    $result = $db->select($sql);
    //  print_r($result);
    $temp_array = [];
    if (isset($result) && !empty($result)) {
        //    print_r('jj');

        foreach ($result as $rs) {
            //   print_r('jj');
            $rs["Short_discription"] = ReduceText($rs["article"]);
//            $rs["articletitle"] = mb_convert_encoding($rs["articletitle"], 'UTF-8', 'UTF-8');
            $rs["articleimage"] = image_url($rs["articleimage"]);
//             $rs["article"] = mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');
//             $rs["article"] = htmlspecialchars_decode(utf8_decode(htmlentities($rs["article"], ENT_HTML5, 'utf-8', false)));
//             $rs["article"] = utf8_decode($rs["article"]);
//              $rs["article"] = Encoding::toUTF8($rs["article"]);
            $rs["article_text"] = $rs["article"];
            $rs["article_title"] = $rs["articletitle"];
            $rs["article_image"] = $rs["articleimage"];

            array_push($temp_array, $rs);
        }

        //   return $temp_array;
    } else {
        //  return $temp_array;
    }
    return $temp_array;
}

function GetSingleLocationArticles($db, $article_id) {
    $sql = "SELECT `categories`.`icon`,`locations`.* FROM `locations`,`categories` WHERE `locations`.`categoryid`=`categories`.`categoryid` AND `locations`.`locationid`=" . $article_id . " ORDER BY `locations`.`categoryid` DESC  ";
    $result = $db->select($sql);
    // print_r($result);
    $temp_array = [];
    if (isset($result) && !empty($result)) {
        //    print_r('jj');

        foreach ($result as $rs) {
            //   print_r('jj');
            $rs["Short_discription"] = ReduceText($rs["article_text"], 50);
//            $rs["article_title"] = mb_convert_encoding($rs["article_title"], 'UTF-8', 'UTF-8');
            $rs["article_image"] = image_url($rs["article_image"]);
            $rs["catname"] = catid2name($db, $rs["categoryid"]);
//            $rs["article_text"] = mb_convert_encoding($rs["article_text"], 'UTF-8', 'UTF-8');
//             $rs["article_text"] = $rs["article"];
//            $rs["Article_title"] = $rs["articletitle"];
//            $rs["article_image"] = $rs["articleimage"]; 
            array_push($temp_array, $rs);
        }
    }
    return $temp_array;
}

function FetchArticles_Topic($db, $limit = 10) {
    $limit = default_limit();
    $sql = "select * from mobile_articles where visible = 1 order by articlesid desc limit 0," . $limit;
    $result = $db->select($sql);
    $temp_array = [];
    if (isset($result) && !empty($result)) {

        foreach ($result as $rs) {
            $rs["articleimage"] = image_url($rs["articleimage"]);
            $rs["author_name"] = GetAuthorName($db, $rs["author_id"]);
            $rs["RelatedImages"] = GetRelatedImages($db, $rs["articlesid"]);
//            $rs["article"] = htmlspecialchars_decode($rs["article"]);
            $rs["Short_discription"] = ReduceText($rs["article"], 150);
            $rs["readUrl"] = FormReadUrl(mobile_articles_type(), $rs["articlesid"]);

//            $rs["article"] = mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');
//            $rs["articletitle"] = mb_convert_encoding($rs["articletitle"], 'UTF-8', 'UTF-8');
            $rs["article_text"] = $rs["article"];
            $rs["article_title"] = $rs["articletitle"];
            $rs["article_image"] = $rs["articleimage"];

            array_push($temp_array, $rs);
        }

        return $temp_array;
    } else {
        return $temp_array;
    }
}

function FetchFeaturedCollection($db, $limit = 10) {
    $limit = default_limit($limit);
    $sql = "select * from mobile_articles where visible = 1 order by rand() asc limit 0," . $limit;
    $result = $db->select($sql);
    $temp_array = [];
    if (isset($result) && !empty($result)) {

        foreach ($result as $rs) {
            $rs["articleimage"] = image_url($rs["articleimage"]);
            $rs["author_name"] = GetAuthorName($db, $rs["author_id"]);
            $rs["RelatedImages"] = GetRelatedImages($db, $rs["articlesid"]);
            $rs["Short_discription"] = ReduceText($rs["article"], 150);
            $rs["readUrl"] = FormReadUrl(mobile_articles_type(), $rs["articlesid"]);

//            $rs["article"] = mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');
//            $rs["articletitle"] = mb_convert_encoding($rs["articletitle"], 'UTF-8', 'UTF-8');
//            $rs["article"] = htmlspecialchars_decode($rs["article"]);
            // $rs["article"] = mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');
            $rs["article_text"] = $rs["article"];
            $rs["Article_title"] = $rs["articletitle"];
            $rs["article_image"] = $rs["articleimage"];
            array_push($temp_array, $rs);
        }

        return $temp_array;
    } else {
        return $temp_array;
    }
}

function GetAuthorName($db, $authorId) {

    $sth = $db->select("SELECT * FROM `authors`  WHERE authorid='" . $authorId . "'");

    if (!empty($sth)) {
        return $sth[0]['author'];
    } else {
        return '';
    }
}

function GetRelatedImages($db, $articleId) {

    $result = $db->select("select * from relatedImages where articlesid='" . $articleId . "'");

    $temp_array = [];
    if (isset($result) && !empty($result)) {

        foreach ($result as $rs) {
            $rs["articleimage"] = image_url($rs["articleimage"]);

            array_push($temp_array, $rs);
        }

        return $temp_array;
    } else {
        return $temp_array;
    }
}

function GetCategoryCount($db, $cat_id) {

    $sth = $db->select("select * from mobile_articles where visible = 1 and  categoryid='" . $cat_id . "'");

    if (!empty($sth)) {
        return count($sth);
    } else {
        return 0;
    }
}

function GetRandomCategoryDp($db, $cat_id) {
    $sql = "select articleimage from mobile_articles where visible = 1 and  categoryid='" . $cat_id . "'  ORDER BY rand() LIMIT 1";
//print_r($sql);
    $sth = $db->select($sql);

    $imageurl = '';
    if (isset($sth) && !empty($sth)) {
        $imageurl = image_url($sth[0]["articleimage"]);
    } else {
//         $imageurl='https://image.shutterstock.com/image-vector/golden-emblem-badge-umbrella-icon-600w-1276778647.jpg';
        $imageurl = GetCatImage($db, $cat_id);
    }
    return $imageurl;
}

function catid2name($db, $cat_id) {

    $sth = $db->select("select * from categories where   categoryid='" . $cat_id . "'");

    if (!empty($sth)) {
        return $sth[0]['category'];
    } else {
        return '';
    }
}

function GetCatIcon($db, $cat_id) {

    $sth = $db->select("select * from categories where   categoryid='" . $cat_id . "'");

    if (!empty($sth)) {
        return $sth[0]['icon'];
    } else {
        $sth = $db->select("select * from categories where   categoryid='0'");
        if (!empty($sth)) {
            return $sth[0]['icon'];
        } else {
            return 'https://res.cloudinary.com/custocrypt/image/upload/v1563105341/HHPNG%20image/compresses/Information_Pin_Hidden_History.png';
        }
    }
}

function GetCatImage($db, $cat_id) {

    $sth = $db->select("select * from categories where   categoryid='" . $cat_id . "'");

    if (!empty($sth)) {
        return $sth[0]['category_dp'];
    } else {
        $sth = $db->select("select * from categories where   categoryid='0'");
        if (!empty($sth)) {
            return $sth[0]['category_dp'];
        } else {
            return 'https://image.shutterstock.com/image-vector/golden-emblem-badge-umbrella-icon-600w-1276778647.jpg';
        }
    }
}

function ReduceText($text, $count = 250) {
    $reducedText = '';
    $reducedText = substr($text, 0, $count);
//$reducedText=mb_convert_encoding($reducedText, 'UTF-8', 'UTF-8');
    return $reducedText;
}

function GetcategoryDetails($db, $cat_id) {
    $sql = "SELECT * FROM `mobile_articles` WHERE `visible` = 1 AND `categoryid` = " . $cat_id . " ORDER BY `categoryid` DESC ";
    $result = $db->select($sql);
    // print_r($result);
    $temp_array = [];
    if (isset($result) && !empty($result)) {
        //    print_r('jj');

        foreach ($result as $rs) {
            //   print_r('jj');
            $rs["readUrl"] = FormReadUrl(mobile_articles_type(), $rs["articlesid"]);

            $rs["Short_discription"] = ReduceText($rs["article"]);
//            $rs["articletitle"] = mb_convert_encoding($rs["articletitle"], 'UTF-8', 'UTF-8');
            $rs["articleimage"] = image_url($rs["articleimage"]);
//             $rs["article"] = mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');
            $rs["article_text"] = $rs["article"];
            $rs["Article_title"] = $rs["articletitle"];
            $rs["article_image"] = $rs["articleimage"];
            array_push($temp_array, $rs);
        }
    }
    return $temp_array;
}

function GetLocationArticles($db) {
    $sql = "SELECT `categories`.`icon`,`locations`.* FROM `locations`,`categories` WHERE `locations`.`categoryid`=`categories`.`categoryid` ORDER BY `locations`.`categoryid` DESC ";
    $result = $db->select($sql);
    // print_r($result);
    $temp_array = [];
    if (isset($result) && !empty($result)) {
        //    print_r('jj');

        foreach ($result as $rs) {
            //   print_r('jj');
            $rs["Short_discription"] = ReduceText($rs["article_text"], 50);
//            $rs["article_title"] = mb_convert_encoding($rs["article_title"], 'UTF-8', 'UTF-8');
            $rs["article_image"] = image_url($rs["article_image"]);
            $rs["catname"] = catid2name($db, $rs["categoryid"]);
//            $rs["article_text"] = mb_convert_encoding($rs["article_text"], 'UTF-8', 'UTF-8');
            $rs["readUrl"] = FormReadUrl(location_articles_type(), $rs["locationid"]);
//             $rs["article_text"] = $rs["article"];
//            $rs["Article_title"] = $rs["articletitle"];
//            $rs["article_image"] = $rs["articleimage"]; 
            array_push($temp_array, $rs);
        }
    }
    return $temp_array;
}

function GetRandomLocationArticle($db) {
    $sql = "SELECT `categories`.`icon`,`locations`.* FROM `locations`,`categories` WHERE `locations`.`categoryid`=`categories`.`categoryid` order by rand() asc limit 0,10";
    $result = $db->select($sql);
    // print_r($result);
    $temp_array = [];
    if (isset($result) && !empty($result)) {
        //    print_r('jj');

        foreach ($result as $rs) {
            //   print_r('jj');
            $rs["Short_discription"] = ReduceText($rs["article_text"], 50);
//            $rs["article_title"] = mb_convert_encoding($rs["article_title"], 'UTF-8', 'UTF-8');
            $rs["article_image"] = image_url($rs["article_image"]);
            $rs["catname"] = catid2name($db, $rs["categoryid"]);
            $rs["readUrl"] = FormReadUrl(location_articles_type(), $rs["locationid"]);

//            $rs["article_text"] = mb_convert_encoding($rs["article_text"], 'UTF-8', 'UTF-8');
            array_push($temp_array, $rs);
        }
    }
    return $temp_array;
}

function GetArticlesWithLocation($db) {
// $sql = "SELECT * FROM `mobile_articles` WHERE visible=1 AND longitude >0 OR latitude>0 ORDER BY `mobile_articles`.`articlesid` ASC";
// $sql = "SELECT `categories`.`icon`,`mobile_articles`.* FROM `mobile_articles`,`categories` WHERE `mobile_articles`.`categoryid`=`categories`.`categoryid` AND `mobile_articles`.`visible`=1 AND `mobile_articles`.`longitude` >0 OR `mobile_articles`.`latitude` >0 ORDER BY `mobile_articles`.`articlesid` ASC";
//    $sql = "SELECT `categories`.`icon`,`mobile_articles`.* FROM `mobile_articles`,`categories` WHERE `mobile_articles`.`categoryid`=`categories`.`categoryid` AND `mobile_articles`.`visible`=1 AND `mobile_articles`.`longitude` >0 OR `mobile_articles`.`latitude` >0 AND `mobile_articles`.`include_on_map`=1 ORDER BY `mobile_articles`.`articlesid` DESC";
//    
    $sql = "SELECT `mobile_articles`.* FROM `mobile_articles` WHERE  `mobile_articles`.`visible`=1 AND `mobile_articles`.`longitude` >0 OR `mobile_articles`.`latitude` >0 AND `mobile_articles`.`include_on_map`=1  ORDER BY `mobile_articles`.`articlesid` DESC";

    $result = $db->select($sql);
    //  print_r($result);
    $temp_array = [];
    if (isset($result) && !empty($result)) {
        //    print_r('jj')

        foreach ($result as $rs) {
            //   print_r('jj');
//           $rs["article_title"]=mb_convert_encoding($rs["articletitle"], 'UTF-8', 'UTF-8');
//           $rs["article_text"]=mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');
                  $rs["article_text"] = $rs["article"];
            $rs["article_title"] = $rs["articletitle"];
            $rs["article_image"] = $rs["articleimage"];
          
            $rs["Short_discription"] = ReduceText($rs["article_text"], 50);
//            $rs["article_title"] = mb_convert_encoding($rs["article_title"], 'UTF-8', 'UTF-8');
            $rs["article_image"] = image_url($rs["article_image"]);
            $rs["catname"] = catid2name($db, $rs["categoryid"]);
            $rs["icon"] = GetCatIcon($db, $rs["categoryid"]);
            $rs["readUrl"] = FormReadUrl(mobile_articles_type(), $rs["articlesid"]);

//            $rs["article_text"] = mb_convert_encoding($rs["article_text"], 'UTF-8', 'UTF-8');
       
//            $rs["article_image"] = $rs["articleimage"];
              unset($rs["articletitle"]);
            unset($rs["article"]);
            unset($rs["articleimage"]);
            array_push($temp_array, $rs);
        }
    }
    return $temp_array;
}

function checkToken($db, $token) {

    $sth = $db->select("SELECT * FROM `tokens` WHERE `tokens`.`token`='" . $token . "' AND `tokens`.`status`=1");

    if (!empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}








function GetSchoolName($db, $deploymentIdx) {

    $sth = $db->select("SELECT * FROM `schoolbox`  WHERE idx='" . $deploymentIdx . "'");

    if (!empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function SchooldetailsFromURl($db, $subdomain) {

    $sth = $db->select("SELECT * FROM `schoolbox` WHERE `schoolbox`.`subdomain`='" . $subdomain . "' ORDER BY `subdomain` ASC ");

    if (!empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function EncrollFaceNow($args) {

    $Kairos = new Kairos(APP_id, APP_key);
    return $Kairos->enroll($args);
}

function RecognizeFaceNow($args) {

    $Kairos = new Kairos(APP_id, APP_key);
    return $Kairos->recognize($args);
}

function SubjectId2AdmissionNumber($subject_id) {
    $f = explode('_', $subject_id);
    $admissionNumber = str_replace("-", "/", $f[2]);
    return $admissionNumber;
}

function MarkAttendanceNow($db, $attendance_reason, $school_idx, $sex, $personincharge, $admissionNumber, $class_name, $term_name, $session_name, $period, $period_period, $attendance_status, $medium) {
    $date = date("Y-m-d");
//                    $QUERY_PERIOD = '';
    $dateTime = date('d/m/Y g:i:s A');
    if ($period == 1 && $attendance_status == 1) {
        $period = 'Morning Present';
    } elseif ($period == 2 && $attendance_status == 1) {
        $period = 'Afternoon Present';
    } elseif ($period == 1 && $attendance_status == 0) {
        $period = 'Morning Absentee';
    } elseif ($period == 2 && $attendance_status == 0) {
        $period = 'Afternoon Absentee';
    } else {
        $period = '#';
    }
    $db = new database();
    $attendance_code = $school_idx . '/' . $admissionNumber . '/' . $date . '/' . $period_period;
    $checkAttandance_status = checkAttandance_status($db, $attendance_code);
    if ($checkAttandance_status !== false) {
        $data_insert_status = UpdateMarkedAttendance($db, $attendance_code, $attendance_reason, $school_idx, $sex, $personincharge, $admissionNumber, $date, $dateTime, $class_name, $term_name, $session_name, $period, 4, $medium);

        if ($data_insert_status !== false) {

            return 'Attendance Updated Successfully';
        } else {
            return 'Error Updating Attendance, Try again later';
        }
    } else {

        $data_insert_status = MarkeAttendance($db, $attendance_code, $attendance_reason, $school_idx, $sex, $personincharge, $admissionNumber, $date, $dateTime, $class_name, $term_name, $session_name, $period, 4, $medium);
        if ($data_insert_status !== false) {

            return 'Attendance marked Successfully';
        } else {
            return 'Error Saving Attendance, Try again later';
        }
    }
}

function CheckIfAdmissionNumberExistInSchool($db, $admissionNumber) {

    $sth = $db->select("SELECT * FROM `studentbox` where `admissionnumber`='" . $admissionNumber . "' ORDER BY `idx` DESC  ");

    if (!empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function CheckIfStudenthasbeenMovedtoclass($db, $deployment_id, $admissionnumber, $class, $term, $year) {

    $sth = $db->select("SELECT * FROM `studenttermbox` WHERE `schoolidx`='" . $deployment_id . "' AND `admissionnumber`='" . $admissionnumber . "' AND `class`='" . $class . "' AND `term`='" . $term . "' AND `year`='" . $year . "'  ");

    if (!empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function checkAttandance_status($db, $attendanceCode) {
    $sql = "SELECT * FROM `attendancebox` WHERE `attendancecode`='" . $attendanceCode . "'";
//print_r($sql);
    $sth = $db->select($sql);

    if (!empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function VerifyIfStaffExistInSchool($db, $mobile) {

    $sth = $db->select("SELECT * FROM `loger` WHERE `loger`.`mobile`='" . $mobile . "' AND `loger`.`status`=1");

    if (!empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function MarkeAttendance($db, $attendancecode, $reason, $schooluniqueidx, $gender, $personincharge, $admissionnumber, $dateattended, $timestp, $class, $term, $year, $period, $notifyparent, $medium) {

    $attendancebox_data = array(
        'attendancecode' => $attendancecode,
        'reason' => $reason,
        'schooluniqueidx' => $schooluniqueidx,
        'gender' => $gender,
        'personincharge' => $personincharge,
        'admissionnumber' => $admissionnumber,
        'dateattended' => $dateattended,
        'timestp' => null,
        'class' => $class,
        'term' => $term,
        'year' => $year,
        'period' => $period,
        'notifyparent' => $notifyparent,
        'meduim' => $medium,
    );
    return $db->insert('attendancebox', $attendancebox_data);
}

function UpdateMarkedAttendance($db, $attendancecode, $reason, $schooluniqueidx, $gender, $personincharge, $admissionnumber, $dateattended, $timestp, $class, $term, $year, $period, $notifyparent, $medium) {

    $attendancebox_data = array(
        'reason' => $reason,
        'schooluniqueidx' => $schooluniqueidx,
        'gender' => $gender,
        'personincharge' => $personincharge,
        'admissionnumber' => $admissionnumber,
        'dateattended' => $dateattended,
        'timestp' => null,
        'class' => $class,
        'term' => $term,
        'year' => $year,
        'period' => $period,
        'notifyparent' => $notifyparent,
        'meduim' => $medium,
    );
    $condition = "attendancecode='" . $attendancecode . "'";
    return $db->update("attendancebox", $attendancebox_data, $condition);
//   return $db->insert('attendancebox', $attendancebox_data);
}

function VerifyOTPtoken($db, $mobile, $token) {

    $sth = $db->select("SELECT * FROM `general_OTP` WHERE `general_OTP`.`mobile`='" . $mobile . "' AND `general_OTP`.`status`=1 AND `general_OTP`.`token`='" . $token . "' ORDER BY `general_OTP`.`idx` DESC Limit 1");

    if (!empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function getMyClassList($db, $schooluniqueidx, $useridx, $subdomain) {
//   $schooluniqueidx= 326;
//   $useridx=2529;
    $sql = "SELECT classx as classname,schooluniqueidx as schoolid FROM `storesubjectassignment` WHERE `storesubjectassignment`.`schooluniqueidx` ='" . $schooluniqueidx . "' AND `storesubjectassignment`.`useridx`='" . $useridx . "' AND `storesubjectassignment`.`tabu` LIKE '%formteacher%'";
    $sth = $db->select($sql);

    if (!empty($sth)) {
        return $sth;
    } else {
        return getListOfClassInSchool($db, $subdomain);
    }
}

function getTermList($db) {
    $sql = "SELECT term FROM studenttermbox GROUP BY term";
    $sth = $db->select($sql);

    if (!empty($sth)) {
        return $sth;
    } else {
        return false;
    }
}

function getSessionList($db) {
    $sql = "SELECT year FROM studenttermbox GROUP BY year ";
    $sth = $db->select($sql);

    if (!empty($sth)) {
        return $sth;
    } else {
        return false;
    }
}

function determinUserType($administrator, $student, $educator, $parent) {
    $usertype = '';
    if ($administrator == 1) {
        $usertype = 'administrator';
    } elseif ($student == 1) {
        $usertype = 'student';
    } elseif ($educator == 1) {
        $usertype = 'educator';
    } elseif ($parent == 1) {
        $usertype = 'parent';
    }

    return $usertype;
}

function getListOfClassInSchool($db, $subdomain) {
    $subdomain = $sql = "SELECT class as classname,schoolidx as schoolid FROM `classbox` WHERE `subdomain`='" . $subdomain . "' ";
//print_r($sql);
    $sth = $db->select($sql);

    if (!empty($sth)) {
        return $sth;
    } else {
        return false;
    }
}

function generatesOTP($table, $field1, $field2, $value1, $db, $size = 4) {
    $token = "";

    for ($i = 0; $i < $size; $i++) {
        $key = array_merge(range(0, 9));
        $token .= $key [array_rand($key)];
    }
    if (isOTPExist($table, $field1, $field2, $value1, $token, $db) > 0) {
        generatesOTP($table, $field1, $field2, $value1, $db, $size);
    } else {

        return $token;
    }
}

function isOTPExist($table, $field1, $field2, $value1, $value2, $db) {
    $sth = $db->select("Select * from $table WHERE $field1 = :field1 AND $field2 = :field2", array(":field1" => $value1, ":field2" => $value2));
    return count($sth);
}

function istokenExist($table, $field, $token, $db) {
    $sth = $db->select("Select * from $table WHERE $field = :field", array(":field" => $token));
    return count($sth);
}

function encryptdata($data) {
    $key = 'AHUE6wpgHdfiCBfufNouWlOsUrM8sr80l17xnuY';
//$raw       = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.';
//$raw       = 'troggeinteractive';
    $meta = ['name' => 'davies', 'email' => 'David.jinad@gmail.com'];


    $encrypted = encrypt($key, $data, $meta);
    return $encrypted;
//$encrypted = "AHUE6wpgHdfiCBfufNouWlOsUrM8sr80l17xnuY+NSNol60dI2+3nFC5IHd1SHKCm3UEcIzQ";
//$decrypted = decrypt($key, $encrypted, $meta);
}

function decryptdata($data) {
    $key = 'AHUE6wpgHdfiCBfufNouWlOsUrM8sr80l17xnuY';
//$raw       = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.';
//$raw       = 'troggeinteractive';
    $meta = ['name' => 'davies', 'email' => 'David.jinad@gmail.com'];

    $decrypted = decrypt($key, $data, $meta);
    return $decrypted;
}

function VesselEncriptorReversible_de($String) {

//		$this->ValueReturn = $String;
//		$String = explode("[][]", $String);



    $String = base64_decode(base64_decode($String));



    return $String;
}

//End  VesselEncriptorReversible_en($String);





/*

  This method will decript a string with an in-built decriptor, which is reversible

  @Para: $String - The string to be parsed

  @Return: $ValueReturn - Already parsed string

 */



function VesselEncriptorReversible_en($String) {

    $this->ValueReturn = base64_encode(base64_encode($String)) . '[][]' . time();



    return $this->ValueReturn;
}

//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<,


function getStudentFromParent($db, $schoolidx, $mobile) {

    $sth = $db->select(" SELECT * FROM `studentbox` WHERE `studentbox`.`schoolidx`=" . $schoolidx . " AND (`studentbox`.`phonenumber`='" . $mobile . "' OR `studentbox`.`motherphone`='" . $mobile . "')");

    if (!empty($sth)) {
        return $sth;
    } else {
        return $sth;
    }
}

function VerifyAdmissionNumber($db, $admissionnumber) {

    $sth = $db->select("SELECT * FROM `studentbox` WHERE `admissionnumber`='" . $admissionnumber . "' ");

    if (!empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function getResponseType($request) {
//        $responsetype='JSON';
    if ($request->hasHeader('ResponseType')) {
        $headerValueArray = $request->getHeader('ResponseType');
        $req_res_type = $headerValueArray[0];
        if ($req_res_type !== 'XML') {
            return 'JSON';
        } else {
            return 'XML';
        }
    } else {
        return 'JSON';
    }
}

function authenticateRequest($request, $db, $route) {
//        $responsetype='JSON';
//    if ($request->hasHeader('ApiToken')) {
//        $headerValueArray = $request->getHeader('ApiToken');
//        $Api_token = $headerValueArray[0];
//        $sth = $db->select(" SELECT * FROM `apibox`,`apibox_detail` WHERE `apibox_detail`.`apibox_idx`=`apibox`.`idx` AND `apibox_detail`.`status`=1 AND `apibox`.`status`=1 AND `apibox_detail`.`thekey`='" . $Api_token . "' ");
//
//        if (!empty($sth)) {
//            recordUsage($db, $route, $Api_token);
//            return 1;
//        } else {
//            return 2;
//        }
//    } else {
//        return 3;
//    }
    return 1;
}

function recordUsage($db, $route, $token) {
    $dateTime = date('d/m/Y g:i:s A');
    $apibox_usage_data = array(
        'apibox_detail_id' => getCompanyId($db, $token),
        'route' => $route,
        'datetime' => $dateTime,
    );
    $db->insert('apibox_usage', $apibox_usage_data);
}

function getCompanyId($db, $token) {

    $sth = $db->select("SELECT `apibox`.`idx` as companyid FROM `apibox`,`apibox_detail` WHERE `apibox_detail`.`apibox_idx`=`apibox`.`idx` AND `apibox_detail`.`status`=1 AND `apibox`.`status`=1 AND `apibox_detail`.`thekey`='" . $token . "' ");

    if (isset($sth[0]["companyid"]) && !empty($sth)) {
        return $sth[0]["companyid"];
    } else {
        return FALSE;
    }
}

function json2Xml($jsonFile) {
    // print_r($jsonFile);
//read the JSON file
//$jsonFile = file_get_contents('members.json');
//decode the data
    $jsonFile_decoded = json_decode($jsonFile);

//create a new xml object
    $xml = new SimpleXMLElement('<studentDatas/>');

//loop through the data, and add each record to the xml object
    foreach ($jsonFile_decoded AS $students) {
        foreach ($students as $studentdata) {
            $studentdata = $xml->addChild('student');
            $studentdata->addChild('lastName', 'jinad');
            $studentdata->addChild('firstName', 'Olawakld');
            $studentdata->addChild('age', '90');
            $studentdata->addChild('sex', 'male');
            $studentdata->addChild('location', 'ibadan');
        }
    }

//set header content type
//Header('Content-type: text/xml');
//output the xml file
// print($xml->asXML());
    return $xml->asXML();
//print($xml->asXML());
}

function array2xml($array, $xml = false) {

    if ($xml === false) {
        $xml = new SimpleXMLElement('<result/>');
    }

    foreach ($array as $key => $value) {
        //   print_r($value);
        if (is_array($value)) {
            array2xml($value, $xml->addChild($key));
        } else {
            $xml->addChild($key, $value);
        }
    }

    return $xml->asXML();
}

//>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>




function didFieldExist($db, $table, $field, $value) {

    $sth = $db->select("Select * from $table WHERE $field = :field", array(":field" => $value));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function didFieldExist2($db, $table, $field, $value) {

    $sth = $db->select("Select * from $table WHERE $field = :field AND status=0", array(":field" => $value));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function haveIrequestTokenBefore($db, $table, $field, $value) {

    $sth = $db->select("Select * from $table WHERE $field = :field AND status=0 ", array(":field" => $value));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function userToken2id($db, $login_token) {

    $sth = $db->select("Select * from login WHERE login_token = :login_token", array(":login_token" => $login_token));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function postToken2id($db, $table, $field, $token) {

    $sth = $db->select("Select * from $table WHERE $field = :token", array(":token" => $token));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function postdetails($db, $table, $field, $token) {

    $sth = $db->select("Select * from $table WHERE $field = :token", array(":token" => $token));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function postid2postToken($db, $table, $field, $token) {

    $sth = $db->select("Select * from $table WHERE $field = :token", array(":token" => $token));

    if (isset($sth[0]["feed_id"]) && !empty($sth)) {
        return $sth[0]["feed_id"];
    } else {
        return FALSE;
    }
}

function postToken2posterid($db, $table, $field, $token) {

    $sth = $db->select("Select * from $table WHERE $field = :token", array(":token" => $token));

    if (isset($sth[0]["poster"]) && !empty($sth)) {
        return $sth[0]["poster"];
    } else {
        return FALSE;
    }
}

function attachmentId2Postid($db, $attchment_id) {

    $sth = $db->select("Select * from feed_attachment WHERE id = :attachment_id", array(":attachment_id" => $attchment_id));

    if (isset($sth[0]["feed_id"]) && !empty($sth)) {
        return $sth[0]["feed_id"];
    } else {
        return FALSE;
    }
}

function didPasswordMatch($db, $userid, $password) {

    $sth = $db->select("Select * from login WHERE password = :password and id =:id", array(":password" => $password, ":id" => $userid));
//    $sth = $db->select("Select * from login WHERE id = ".$userid." ");
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIlikeBefore($db, $table, $feed_id, $liked_by, $value1, $myid) {

    $sth = $db->select("Select * from $table WHERE $feed_id = :field1 AND $liked_by = :myid AND status =0", array(":field1" => $value1, ":myid" => $myid));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIfollowBefore($db, $myid, $user2) {

    $sth = $db->select("Select * from following WHERE user1_id = :myid AND user2_id = :user2 AND status =0", array(":myid" => $myid, ":user2" => $user2));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIaddintrestBefore($db, $myid, $intrest_id) {

    $sth = $db->select("Select * from myareaofintrest WHERE userid = :userid AND intrest_id = :intrest_id AND status =0", array(":userid" => $myid, ":intrest_id" => $intrest_id));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIUnaddintrestBefore($db, $myid, $intrest_id) {

    $sth = $db->select("Select * from myareaofintrest WHERE userid = :userid AND intrest_id = :intrest_id AND status =1", array(":userid" => $myid, ":intrest_id" => $intrest_id));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIunfollowBefore($db, $myid, $user2) {

    $sth = $db->select("Select * from following WHERE user1_id = :myid AND user2_id = :user2 AND status =1", array(":myid" => $myid, ":user2" => $user2));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function AREWeInConnectionBefore($db, $myid, $user2, $concode) {

    $sth = $db->select("Select * from connect WHERE mentor_id = :myid AND mentee_id = :user2 AND con_code = :concode AND status =3", array(":myid" => $myid, ":user2" => $user2, ":concode" => $concode));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function con_code2ids($db, $concode) {

    $sth = $db->select("Select * from connect WHERE  con_code = :concode AND status =0", array(":concode" => $concode));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function amCommentOwner($db, $feed_id, $comment_by, $comment_id) {

    $sth = $db->select("Select id from comment WHERE  feed_id = :feed_id AND comment_by = :comment_by AND id = :comment_id AND status =0", array(":feed_id" => $feed_id, ":comment_by" => $comment_by, ":comment_id" => $comment_id));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0]['id'];
    } else {
        return FALSE;
    }
}

function amPostOwner($db, $feed_id, $post_by) {

    $sth = $db->select("Select id from feeds WHERE  feed_id = :feed_id AND poster = :poster  AND status =0", array(":feed_id" => $feed_id, ":poster" => $post_by));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0]['id'];
    } else {
        return FALSE;
    }
}

function isConnectionGoing($db, $myid, $user2, $concode) {

    $sth = $db->select("Select * from connect WHERE mentor_id = :myid AND mentee_id = :user2 AND con_code = :concode AND status =0", array(":myid" => $myid, ":user2" => $user2, ":concode" => $concode));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function AREWeInConnectionBefore1($db, $myid, $user2) {

//    $sth = $db->select("Select * from connect WHERE mentor_id = :myid AND mentee_id = :user2 AND (status =3 OR status =0)", array(":myid" => $myid,":user2" => $user2));
//    $sth = $db->select("Select * from connect WHERE mentor_id = :myid AND mentee_id = :user2 AND  status =3", array(":myid" => $myid,":user2" => $user2));
    $sth = $db->select("Select * from connect WHERE mentor_id = :user2 AND mentee_id = :myid AND status =3", array(":myid" => $myid, ":user2" => $user2));

//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function AREWeConnected($db, $myid, $con_code) {

    $sth = $db->select("Select * from connect WHERE (mentor_id = :myid OR  mentee_id = :myid) AND  con_code = :concode AND status =0", array(":myid" => $myid, ":concode" => $con_code));

//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function getareaofintrest($db, $myid) {
    $sql = "SELECT `intrest_categories`.`name` AS intrest FROM `myareaofintrest`,`intrest_categories` WHERE `intrest_categories`.`id`=`myareaofintrest`.`intrest_id` AND `myareaofintrest`.`status`='0' AND `intrest_categories`.`status`='0' AND `myareaofintrest`.`userid`='" . $myid . "' ";
    $result = $db->select($sql);

    if (isset($result) && !empty($result)) {

        return $result;
    } else {
        return $result;
    }
}

function haveIUnlikeBefore($db, $table, $feed_id, $liked_by, $value1, $myid) {
    $sth = $db->select("Select * from $table WHERE $feed_id = :field1 AND $liked_by = :myid AND status =1", array(":field1" => $value1, ":myid" => $myid));
//     print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function amIWhoAmI($db, $response, $login_token) {

    $sth = $db->select("Select * from login WHERE login_token = :login_token", array(":login_token" => $login_token));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
//        die();
        return ErrorMessage($response, "Your id is not correct please logout and login again");
    }
}

function id2name($db, $uid) {

    $sth = $db->select("Select fullname from profile WHERE userid = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth[0]["fullname"]) && !empty($sth)) {
        return $sth[0]["fullname"];
    } else {
        return FALSE;
    }
}

function NotificationCount($db, $myid) {


    $sql = "SELECT message,token,type FROM `notifications` where  status =0 and userid ='" . $myid . "'";
    $result = $db->select($sql);

//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return count($sth);
    } else {
        return 0;
    }
}

function id2type($db, $uid) {

    $sth = $db->select("Select user_type from login WHERE id = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth[0]["user_type"]) && !empty($sth)) {
        return $sth[0]["user_type"];
    } else {
        return FALSE;
    }
}

function getBriefDetails($db, $uid) {

    $sth = $db->select("Select description from profile WHERE userid = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth[0]["description"]) && !empty($sth)) {
        return $sth[0]["description"];
    } else {
        return 'No description';
    }
}

function myOtherDetails($db, $uid) {

    $sth = $db->select("Select * from profile WHERE userid = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function countComment($db, $table, $field, $value) {

    $sth = $db->select("Select COUNT(*) as commentcount  from $table WHERE $field = :value AND `$table`.`status`='0'", array(":value" => $value));

    if (isset($sth[0]["commentcount"]) && !empty($sth)) {
        return $sth[0]["commentcount"];
    } else {
        return 0;
    }
}

function countlikes($db, $table, $field, $value) {

    $sth = $db->select("Select COUNT(*) as likecount  from $table WHERE $field = :value AND `$table`.`status`='0'", array(":value" => $value));

    if (isset($sth[0]["likecount"]) && !empty($sth)) {
        return $sth[0]["likecount"];
    } else {
        return 0;
    }
}

function countAllfunding($db) {

    $sth = $db->select("SELECT COUNT(*) as TotalFunding FROM `fundings`  where status=0 ");

    if (isset($sth[0]["TotalFunding"]) && !empty($sth)) {
        return $sth[0]["TotalFunding"];
    } else {
        return 0;
    }
}

function counttotalCourseCount($db, $id) {

    $sth = $db->select("SELECT COUNT(*) as Totalclick FROM `course_clicks`  where status=0 AND course_id='" . $id . "'");

    if (isset($sth[0]["Totalclick"]) && !empty($sth)) {
        return $sth[0]["Totalclick"];
    } else {
        return 0;
    }
}

function GettotalFollower($db, $id) {

    $sth = $db->select("SELECT COUNT(*) as TotalFollower FROM `following`  where status=0 AND user2_id='" . $id . "'");

    if (isset($sth[0]["TotalFollower"]) && !empty($sth)) {
        return $sth[0]["TotalFollower"];
    } else {
        return 0;
    }
}

function counttotalFundingClick($db, $id) {

    $sth = $db->select("SELECT COUNT(*) as Totalclick FROM `funding_clicks`  where status=0 AND funding_id='" . $id . "'");

    if (isset($sth[0]["Totalclick"]) && !empty($sth)) {
        return $sth[0]["Totalclick"];
    } else {
        return 0;
    }
}

function countAllcourses($db) {

    $sth = $db->select("SELECT COUNT(*) as TotalShort_courses FROM `short_courses`  where status=0 ");

    if (isset($sth[0]["TotalShort_courses"]) && !empty($sth)) {
        return $sth[0]["TotalShort_courses"];
    } else {
        return 0;
    }
}

function countAlljobPosted($db) {

    $sth = $db->select("SELECT COUNT(*) as Totaljobs FROM `jobs`  where status=0 ");

    if (isset($sth[0]["Totaljobs"]) && !empty($sth)) {
        return $sth[0]["Totaljobs"];
    } else {
        return 0;
    }
}

function CheckIfIlike($db, $table, $field, $value, $field2, $myid) {

    $sth = $db->select("Select id from $table WHERE $field = :value AND $field2 = :myid AND status=0", array(":value" => $value, ":myid" => $myid));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function id2username($db, $uid) {

    $sth = $db->select("Select username from login WHERE id = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth[0]["username"]) && !empty($sth)) {
        return $sth[0]["username"];
    } else {
        return FALSE;
    }
}

function getlastmessage($db, $myid, $concode, $table, $field) {

    $sth = $db->select("Select  *  FROM $table WHERE ((sender='" . $myid . "') OR (reciever='" . $myid . "')) AND $field='" . $concode . "'  ORDER BY `$table`.`id` DESC LIMIT 1");
//    print_r($sth);
    if (isset($sth[0]["message"]) && !empty($sth)) {
        return $sth[0]["message"];
    } else {
        return 'nill';
    }
}

function msgCounter($db, $myid, $concode, $table) {

    $sth = $db->select("Select count(*) as msgcont from  `$table` WHERE status=:status AND ((reciever=:reciever) AND (con_code=:con_code)) ", array(":reciever" => $myid, ":con_code" => $concode, ":status" => '0'));
    if (!empty($sth)) {
        return $sth[0]['msgcont'];
    } else {
        return 0;
    }
}

function timestamp2time($timestamp) {
    $timestamp_inseconds = $timestamp / 1000;
    return date('h:i a', $timestamp_inseconds);
}

function concode2duration($db, $con_code) {

    $sth = $db->select("Select duration from connect_request WHERE con_code = :con_code", array(":con_code" => $con_code));
//    print_r($sth);
    if (isset($sth[0]["duration"]) && !empty($sth)) {
        return $sth[0]["duration"];
    } else {
        return FALSE;
    }
}

function sortBy($field, &$array, $direction = 'asc') {
    usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if ($a == $b)
        {
            return 0;
        }

        return ($a ' . ($direction == 'desc' ? '>' : '<') . ' $b) ? -1 : 1;
    '));

    return true;
}

function getconnectiondetails($db, $con_code, $mentor_id) {

    $sth = $db->select("Select * from connect_request WHERE con_code = :con_code AND user_id='" . $mentor_id . "'", array(":con_code" => $con_code));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function getconnectiondetails_from_mentee($db, $con_code) {

    $sth = $db->select("Select * from connect_request WHERE con_code = :con_code", array(":con_code" => $con_code));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function durationid2name($db, $id) {

    $sth = $db->select("Select * from connect_duration WHERE id = :id", array(":id" => $id));
//    print_r($sth);
//    if (isset($sth[0]["name"]) && !empty($sth)) {
//        return $sth[0]["name"];
//    }
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function add2date($days2add) {
    $today = date("Y-m-d"); // Or can put $today = date ("Y-m-d");
//$days2add=6;

    $newt = strtotime($today . "+" . $days2add . " days");
    return $newt;
//print_r("\n");
//print_r($newt);
//print_r("\n");
//print_r(date("c",$newt));
}

function username2id($db, $uname) {

    $sth = $db->select("Select id from login WHERE username = :uname", array(":uname" => $uname));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function email2id($db, $email) {

    $sth = $db->select("Select id from login WHERE email = :email", array(":email" => $email));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function id2email($db, $id) {

    $sth = $db->select("Select email from login WHERE id = :id", array(":id" => $id));
//    print_r($sth);
    if (isset($sth[0]["email"]) && !empty($sth)) {
        return $sth[0]["email"];
    } else {
        return FALSE;
    }
}

function checkifclickbefore($db, $table, $userid, $field1, $value, $Field2) {

    $sth = $db->select("Select id from $table WHERE $field1 = :value AND $Field2 = :value2", array(":value" => $value, ":value2" => $userid));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function recordNotification($db, $message, $token, $type, $uid, $otherparty) {

    $notifications_data = array(
        'message' => $message,
        'token' => $token,
        'type' => $type,
        'time' => time(),
        'userid' => $uid,
        'otherid' => $otherparty,
        'status' => 0,
    );
    $db->insert('notifications', $notifications_data);
}

function startRegularChat($db, $sender, $reciever, $name) {
    $dateTime = date('d/m/Y g:i:s A');

    $handshake = generatesToken('regular_chat', 'handshake', $db);
    $message = default_follow_chatmessage($name);
    $regular_chat_data = array(
        'sender' => $sender,
        'reciever' => $reciever,
        'handshake' => $handshake,
        'message' => $message,
        'date' => time(),
        'datetime' => $dateTime,
        'attachement' => '999',
        'status' => 0,
    );
    $db->insert('regular_chat', $regular_chat_data);
}

function getPostMedia($db, $table, $condition, $value) {

    $sth = $db->select("Select * from $table WHERE $condition = :value", array(":value" => $value));

    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function LoadExistingData($db, $email, $table) {

    $sth = $db->select("Select * from $table WHERE email = :email", array(":email" => $email));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function loadMoreComments($db, $response, $page, $table, $field, $value) {
    $limit = default_comment_limit();
    $currentpage = $page - 1;
    $startingpoint = $currentpage * $limit;
    $totalcount = countComment($db, $table, $field, $value);
    if ($startingpoint > $totalcount) {
        return ErrorMessage($response, "No data to load again");
    } else {
        $sth = $db->select("SELECT `profile`.`fullname`,`profile`.`profile_pix` ,`login`.`username`, `comment`.`comment`, `comment`.`datetime`, `comment`.`id` AS comment_id FROM `$table` AS `comment`,`profile`,`login` WHERE `comment`.`comment_by`=`profile`.`userid` AND `login`.`id`=`comment`.`comment_by` AND `comment`.`$field`=" . $value . " AND `comment`.`status`='0'  ORDER BY `comment`.`id` DESC  LIMIT " . $startingpoint . ',' . $limit);

        $temp_array = [];
        if (isset($sth) && !empty($sth)) {
            foreach ($sth as $rs) {
                $rs["datetime2"] = date("c", $rs["datetime"]);
                array_push($temp_array, $rs);
            }
//            return $temp_array;
            return SuccessMessageWithData($temp_array, $response, 'Comment for page' . $page);
        } else {
//            return $sth;
            return SuccessMessageWithData($sth, $response, 'Comment for page>>' . $page);
        }
    }
}

function loadSmallComment_Feeds($db, $value) {

    $sth = $db->select("SELECT `profile`.`fullname`,`profile`.`profile_pix` ,`login`.`username`, `comment`.`comment`, `comment`.`datetime`, `comment`.`id` AS comment_id FROM `comment`,`profile`,`login` WHERE `comment`.`comment_by`=`profile`.`userid` AND `login`.`id`=`comment`.`comment_by` AND `comment`.`feed_id`=" . $value . " AND `comment`.`status`='0'  ORDER BY `comment`.`id` DESC LIMIT " . default_comment_limit());
    $temp_array = [];
    if (isset($sth) && !empty($sth)) {
        foreach ($sth as $rs) {
            $rs["datetime2"] = date("c", $rs["datetime"]);


            array_push($temp_array, $rs);
        }
        return $temp_array;
    } else {
        return $sth;
    }
}

function loadSmallComment_Attachment_Feeds($db, $value) {

    $sth = $db->select("SELECT `profile`.`fullname`,`profile`.`profile_pix` ,`login`.`username`, `comment`.`comment`, `comment`.`datetime`, `comment`.`id` AS comment_id FROM `feed_attachment_comment` AS `comment`,`profile`,`login` WHERE `comment`.`comment_by`=`profile`.`userid` AND `login`.`id`=`comment`.`comment_by` AND `comment`.`attachment_id`=" . $value . " AND `comment`.`status`='0'  ORDER BY `comment`.`id` DESC LIMIT " . default_comment_limit());
    $temp_array = [];
    if (isset($sth) && !empty($sth)) {
        foreach ($sth as $rs) {
            $rs["datetime2"] = date("c", $rs["datetime"]);
            array_push($temp_array, $rs);
        }
        return $temp_array;
    } else {
        return $sth;
    }
}

function loadAllComment_Feeds($db, $value) {

    $sth = $db->select("SELECT `profile`.`fullname`,`profile`.`profile_pix` ,`login`.`username`, `comment`.`comment`, `comment`.`datetime`,`comment`.`id` AS comment_id FROM `comment`,`profile`,`login` WHERE `comment`.`comment_by`=`profile`.`userid` AND `login`.`id`=`comment`.`comment_by` AND `comment`.`feed_id`=" . $value . " AND `comment`.`status`='0'");
    $temp_array = [];
    if (isset($sth) && !empty($sth)) {
        foreach ($sth as $rs) {
            $rs["datetime2"] = date("c", $rs["datetime"]);


            array_push($temp_array, $rs);
        }
        return $temp_array;
    } else {
        return $sth;
    }
}

function generateUsername($db, $email) {
    $emailArray = explode('@', $email);

    $tempusername = $emailArray[0];

    while (usernameExist($db, $tempusername)) {
        $pin = generatepin();
        $tempusername = $tempusername . $pin;
    }
    return $tempusername;
}

function usernameExist($db, $tempusername) {


    $sth = $db->select("Select * from login WHERE username = :username", array(":username" => $tempusername));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function didEmailExist($db, $email, $table) {

    $sth = $db->select("Select * from login WHERE email = :email", array(":email" => $email));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function haveIchatToday($db, $con_code, $day) {

    $sth = $db->select("Select * from mentorship_activities WHERE con_code = :con_code AND day = :day", array(":con_code" => $con_code, ":day" => $day));

    if (isset($sth) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function generatesToken($table, $field, $db, $size = 10) {
    $token = "";

    for ($i = 0; $i < $size; $i++) {
        $key = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $token .= $key [array_rand($key)];
    }
    if (istokenExist($table, $field, $token, $db) > 0) {
        generatesToken($table, $field, $db, $size);
    } else {

        return $token;
    }
}

function generates_Reset_Token($table, $field, $db, $size = 10) {
    $token = "";

    for ($i = 0; $i < $size; $i++) {
        $key = array_merge(range('a', 'z'), range('A', 'Z'));
        $token .= $key [array_rand($key)];
    }
    if (istokenExist($table, $field, $token, $db) > 0) {
        generatesToken($table, $field, $db, $size);
    } else {

        return $token;
    }
}

function generatepin() {
    $pin = "";

    for ($i = 0; $i < 3; $i++) {
        $key = array_merge(range(0, 9));
        $pin .= $key [array_rand($key)];
    }

    return $pin;
}

function get_time_ago($time) {
    $time_difference = time() - $time;

    if ($time_difference < 1) {
        return 'less than 1 second ago';
    }
    $condition = array(12 * 30 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60 => 'month',
        24 * 60 * 60 => 'day',
        60 * 60 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($condition as $secs => $str) {
        $d = $time_difference / $secs;

        if ($d >= 1) {
            $t = round($d);
            return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}

function fetchawards($db, $uid) {

    $sth = $db->select("Select * from awards WHERE user_id = :user_id AND status = 0", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function getcountryFlag($country) {
    $url = 'https://restcountries.eu/rest/v2/name/' . $country;
//    print_r($url);
    $service_url = $url;
    $response = loadURL($service_url);
//    return $response[1]["flag"] || $response[0]["flag"];
    if ($country == 'India') {
        return $response[1]["flag"];
    } else {
        return $response[0]["flag"];
    }
}

function getmycountry($db, $myid) {

    $sth = $db->select("Select country from profile WHERE userid = :id", array(":id" => $myid));
//    print_r($sth);
    if (isset($sth[0]["country"]) && !empty($sth)) {
        return $sth[0]["country"];
    } else {
        return FALSE;
    }
}

function fetchPlacesVisited($db, $uid) {

    $sth = $db->select("Select * from placevisited WHERE user_id = :user_id AND status = 0", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function fetchVertificate($db, $uid) {

    $sth = $db->select("Select * from certificate WHERE user_id = :user_id AND status = 0", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function fetchexperience($db, $uid) {

    $sth = $db->select("Select * from experience WHERE user_id = :user_id AND status = 0", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function fetchactivities($db, $uid) {

    $sth = $db->select("Select * from activities WHERE user_id = :user_id AND status = 0 order by `id` DESC LIMIT 6", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function fetchSkills($db, $uid) {

    $sth = $db->select("Select * from skillNendorsement WHERE user_id = :user_id AND status = 0 order by `id` DESC LIMIT 6", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function getmemberInTeam($db, $positionId) {

    $sth = $db->select("Select * from `ourteam` WHERE position =:positionid AND status = 0  ORDER BY `ourteam`.`id` ASC ", array(":positionid" => $positionId));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function savecopytodb($db, $sender, $reciever, $message, $con_code) {
    $dateTime = date('d/m/Y g:i:s A');
    $connect_data = array(
        'sender' => $sender,
        'reciever' => $reciever,
        'message' => $message,
        'con_code' => $con_code,
        'date' => time(),
        'datetime' => $dateTime,
        'attachement' => 0,
        'status' => 0,
    );
    $db->insert('connection_chat', $connect_data);
}

function updateactivites_tbl($db, $con_code) {
    $dateex = date("Y-m-d");
    $months = explode('-', $dateex);
//         print_r($months);
//         $month=$months[1];
//         $year=$months[0];
    $day = $months[2];
    $res = haveIchatToday($db, $con_code, $day);
    if ($res === FALSE) {
        $mentorship_activities_data = array(
            'con_code' => $con_code,
            'activities' => "Start search session",
            'start_time' => time(),
            'end_time' => time(),
            'date' => time(),
            'day' => $day,
            'status' => 0
        );
        $db->insert('mentorship_activities', $mentorship_activities_data);
    } else {
        $mentorship_activities_data = array(
//                'con_code ' => $con_code,
//                 'activities' => "Start search session",
//                'start_time' => time(),
            'end_time' => time(),
//                'date' => time(),
//                'day' => $day,
            'status' => 0
        );
        $condition = "con_code='" . $con_code . "' AND day='" . $day . "'";
        $db->update("mentorship_activities", $mentorship_activities_data, $condition);
    }
}

function loadURL($service_url) {
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
//        die('error occured during curl exec. Additioanl info: ' . var_export($info));
        die('<div class="alert alert-danger outPopUp" style="font-size: 17px;color: red;position: absolute;height: 200px;z-index: 15;top: 60%;left: 50%;margin: -100px 0 0 -150px;">An error occure please \n check your network connection and try again</div>');
    }
    curl_close($curl);

    $decoded = json_decode($curl_response);
    $t = objectToArray($decoded);
    return $t;
}

function objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return array_map(__FUNCTION__, $d);
    } else {
        // Return array
        return $d;
    }
}

function postdata($service_url, $curl_post_data) {
    $curl = curl_init($service_url);
//    print_r($curl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
//    curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($curl_post_data));
    $curl_response = curl_exec($curl);
//    print_r($curl_response);
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
        die('error occured during curl exec. Additioanl info: ' . var_export($info));
    }
    curl_close($curl);
    $decoded = json_decode($curl_response);
//print_r($decoded);
    $t = objectToArray($decoded);
// print_r($t);
//    print_r($t['status']);
//    if (isset($t['status']) && $t['status'] == 'ERROR') {
//        die('error occured: ' . $t['status']);
//    }
//echo $t['response']  [0]['id'] ;
//   return  $t['response'];
    return $t;
}

//function objectToArray($d) {
//    if (is_object($d)) {
//        // Gets the properties of the given object
//        // with get_object_vars function
//        $d = get_object_vars($d);
//    }
//
//    if (is_array($d)) {
//        /*
//         * Return array converted to object
//         * Using __FUNCTION__ (Magic constant)
//         * for recursive call
//         */
//        return array_map(__FUNCTION__, $d);
//    } else {
//        // Return array
//        return $d;
//    }
//}




function cUrlGetData($url, $post_fields = null, $headers = null, $response) {
    $ch = curl_init();
    $timeout = 10;
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($post_fields && !empty($post_fields)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    }
//    print_r($headers);
    if ($headers && !empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    //set the content type to application/json
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','app_id:7b585406','app_key:b32fa9b848e9e2295b36a04ca68077de'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
        return ErrorMessage($response, curl_error($ch));
    }
    curl_close($ch);

//        curl_close($curl);
    $decoded = json_decode($data);
//print_r($decoded);
    $t = objectToArray($decoded);
// print_r($t);
//    print_r($t['status']);
//    if (isset($t['status']) && $t['status'] == 'ERROR') {
//        die('error occured: ' . $t['status']);
//    }
//echo $t['response']  [0]['id'] ;
//   return  $t['response'];
    return $decoded;

//    return $data;
}

function SendSms($Receiver, $Message) {
    $Sender = 'EdvesOTP';
    $RealReceive = str_split($Receiver);

    if (sizeof($RealReceive) < 11) {
        $RealReceiver = '234';
        for ($start = 0; $start < sizeof($RealReceive); $start++) {
            $RealReceiver .= $RealReceive[$start];
        }
    } elseif (sizeof($RealReceive) < 13) {
        $RealReceiver = '234';
        for ($start = 1; $start < sizeof($RealReceive); $start++) {
            $RealReceiver .= $RealReceive[$start];
        }
    } else {

        $RealReceiver = $Receiver;
    }

    $order = array("&");

    $replace = array('and');

    $newstr = str_replace($order, $replace, $Message);

    $order = array("andamp;", "andamp");

    $replace = array("and", "and");

    $newstr = str_replace($order, $replace, $newstr);

    $Mess = $newstr;



    //setting the request based on the above parameters

    $url = 'https://smartsmssolutions.com/api/';

    $token = 'VJBxHgvJUYpIIsc8hRZAu4DA4JUe4nomQBv5OBziqtCrImulXowSYqcoOpU0GcyLVUy8G1Wzv6VPNgRuqgtQGLIWanHGNISg97tb';

    $params = array(
        'sender' => $Sender,
        'to' => $RealReceiver,
        'message' => $Mess,
        'type' => '0', //This can be set as desired. 0 = Plain text ie the normal SMS
        'routing' => '6', //This can be set as desired. 3 = Deliver message to DND phone numbers via the corporate route
        'token' => $token
    );
    $params = http_build_query($params);
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

    $content2 = curl_exec($ch);

    curl_close($ch);

    $ArrayEploded = explode("||", $content2);

    return $ArrayJson = json_decode($ArrayEploded[1], true);
    // print_r($ArrayJson);
}
