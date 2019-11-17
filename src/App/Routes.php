<?php

declare(strict_types=1);


use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', 'App\Controller\DefaultController:getHelp');
$app->get('/status', 'App\Controller\DefaultController:getStatus');
$app->post('/login', 'App\Controller\User\LoginUser');



/* * *-----------------------------------fetch article-----------------------------------------------------* */

$app->get('/fetcharticle', function (Request $request, Response $response, array $args) {
    //     $response->header("Content-Type", "application/json");
    $db = new database();
    $allArray = [];
    //    $userid = $args['user_id'];
    $featuredArticle = FetchFeaturedArticle($db);
    $FetchArticles_Topic = FetchArticles_Topic($db);
    $FetchFeaturedCollection = FetchFeaturedCollection($db);
    $allArray['featuredArticle'] = $featuredArticle;
    $allArray['FetchArticles_Topic'] = $FetchArticles_Topic;
    $allArray['FetchFeaturedCollection'] = $FetchFeaturedCollection;

    //   return SuccessMessageWithData(convert_from_latin1_to_utf8_recursively($featuredArticle), $response, 'Feeds data');
    return SuccessMessageWithData($allArray, $response, 'Article Data');
});
/* * *-----------------------------------fetch article ends---------------------------------------------------------* */

/* * *-----------------------------------fetch article-----------------------------------------------------* */

$app->get('/fetcharticle2', function (Request $request, Response $response, array $args) {
    //     $response->header("Content-Type", "application/json");
    $db = new database();
    $allArray = [];
    //    $userid = $args['user_id'];
    $featuredArticle = FetchFeaturedArticle($db);
    $FetchArticles_Topic = FetchArticles_Topic($db);
    $FetchFeaturedCollection = FetchFeaturedCollection($db);
    $allArray['featuredArticle'] = $featuredArticle;
    $allArray['FetchArticles_Topic'] = $FetchArticles_Topic;
    $allArray['FetchFeaturedCollection'] = $FetchFeaturedCollection;

    //   return SuccessMessageWithData(convert_from_latin1_to_utf8_recursively($featuredArticle), $response, 'Feeds data');
    return SuccessMessageWithData($allArray, $response, 'Article Data');
});
/* * *-----------------------------------fetch article ends---------------------------------------------------------* */

/* * *-----------------------------------fetch Catehory-----------------------------------------------------* */

$app->get('/categories', function (Request $request, Response $response, array $args) {
    //     $response->header("Content-Type", "application/json");
    $limit = default_limit(9);
    $db = new database();
    $sql = "SELECT * FROM `categories`  limit 0," . $limit;
    $result = $db->select($sql);
    // print_r($result);
    if (isset($result) && !empty($result)) {
        //    print_r('jj');
        $temp_array = [];
        foreach ($result as $rs) {
            //   print_r('jj');
            // $rs["icon"] = image_url($rs["icon"]);
            $rs["article_count"] = GetCategoryCount($db, $rs["categoryid"]);
            $rs["article_image"] = GetRandomCategoryDp($db, $rs["categoryid"]);
            //            $rs["article"] = htmlspecialchars_decode($rs["article"]);
            //            $rs["article"] = mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');
            array_push($temp_array, $rs);
        }

        //   return $temp_array;
    } else {
        //  return $temp_array;
    }

    return SuccessMessageWithData($temp_array, $response, 'Category Data');
});
/* * *-----------------------------------fetch Category ends---------------------------------------------------------* */

/* * *-----------------------------------fetch getCategorydetails-----------------------------------------------------* */

$app->get('/getCategorydetails/{cat_id}', function (Request $request, Response $response, array $args) {
    $db = new database();
    $cat_id = $args["cat_id"];
    //print_r($cat_id);
    $sql = "SELECT * FROM `mobile_articles` WHERE `visible` = 1 AND `categoryid` = " . $cat_id . " ORDER BY `categoryid` DESC ";
    $result = $db->select($sql);
    // print_r($result);
    $temp_array = [];
    if (isset($result) && !empty($result)) {
        //    print_r('jj');

        foreach ($result as $rs) {
            //   print_r('jj');
            $rs["Short_discription"] = ReduceText($rs["article"]);
            //            $rs["articletitle"] = mb_convert_encoding($rs["articletitle"], 'UTF-8', 'UTF-8');
            $rs["articleimage"] = image_url($rs["articleimage"]);
            //             $rs["article"] = mb_convert_encoding($rs["article"], 'UTF-8', 'UTF-8');

            $rs["article_text"] = $rs["article"];
            $rs["article_title"] = $rs["articletitle"];
            $rs["article_image"] = $rs["articleimage"];
            $rs["readUrl"] = FormReadUrl(mobile_articles_type(), $rs["articlesid"]);

            array_push($temp_array, $rs);
        }

        //   return $temp_array;
    } else {
        //  return $temp_array;
    }

    return SuccessMessageWithData($temp_array, $response, 'Category Data');
});
/* * *-----------------------------------fetch getCategorydetails ends---------------------------------------------------------* */

/* * *-----------------------------------fetch getCategorydetails-----------------------------------------------------* */

$app->get('/getsingleArticle/{type}/{article_id}', function (Request $request, Response $response, array $args) {
    $db = new database();
    $article_id = $args["article_id"];
    $type = $args["type"];
    $data = [];
    if ($type == 1) { // mobilearticle
        //echo 'here1';
        $data = GetSingleMobileArticle($db, $article_id);
    } else if ($type == 2) { // Location Article
        //  echo 'here2';
        $data = GetSingleLocationArticles($db, $article_id);
    } else {
        // echo 'here';
        $data = [];
    }
    //print_r($cat_id);


    return SuccessMessageWithData($data, $response, 'Single Data');
});
/* * *-----------------------------------fetch getCategorydetails ends---------------------------------------------------------* */

/* * *-----------------------------------fetch maplocation-----------------------------------------------------* */

$app->get('/maplocation', function (Request $request, Response $response, array $args) {
    //     $response->header("Content-Type", "application/json");
    $limit = default_limit(9);
    $db = new database();
    $sql = "SELECT * FROM `categories`  limit 0," . $limit;
    $result = $db->select($sql);
    // print_r($result);
    if (isset($result) && !empty($result)) {
        //    print_r('jj');
        $temp_array = [];
        foreach ($result as $rs) {
            //   print_r('jj');
            // $rs["icon"] = image_url($rs["icon"]);
            $catdatils = GetcategoryDetails($db, $rs["categoryid"]);
            $rs["article_count"] = GetCategoryCount($db, $rs["categoryid"]);
            $rs["catDetails"] = $catdatils;


            array_push($temp_array, $rs);
        }

        //   return $temp_array;
    }
    $generalArray = [];
    $LocationArticles = GetLocationArticles($db);
    $ArticlesWithLocation = GetArticlesWithLocation($db);
    $RandomLocationArticle = GetRandomLocationArticle($db);
    $newArry = array_merge($LocationArticles, $ArticlesWithLocation);
    // array_push($generalArray,$newArry);
    // array_push($generalArray,$temp_array);
    $generalArray['LocationMap'] = $newArry;
    $generalArray['Cat_segment'] = $temp_array;
    $generalArray['RandomLocationArticle'] = $RandomLocationArticle;
    return SuccessMessageWithData($generalArray, $response, 'Location Deatils');
});
/* * *-----------------------------------fetch maplocation ends---------------------------------------------------------* */

/* * *-----------------------------------Verify Token --------------------------------------------------------* */
$app->post('/savefcmtoken', function (Request $request, Response $response, array $args) {
    $route = 'savefcmtoken';
    $db = new database();
    $input = $request->getParsedBody();
    $isAuth = authenticateRequest($request, $db, $route);
    if ($isAuth === 1) {
        //        $resp_type = getResponseType($request);
        $resp = verifyRequiredParams(array('fctoken'), $response, $this);
        if ($resp['error'] === TRUE) {
            $errormessage = $resp['message'];
            return ErrorMessage($response, $errormessage);
        }
        $fctoken = $input['fctoken'];

        $tokenstatus = checkToken($db, $fctoken);
        if ($tokenstatus !== false) {

            $dateTime = date('Y-m-d g:i:s A');
            $UpdateTokenParam = array(
                'token' => $fctoken,
                'date_updated' => $dateTime,
                'status' => 1,

            );
            $condition = "token='" . $fctoken . "' ";
            $db->update("tokens", $UpdateTokenParam, $condition);
            return SuccessMessage($response, 'Token Updated Successfully');
        } else {
            $dateTime = date('Y-m-d g:i:s A');
            $UpdateTokenParam = array(
                'token' => $fctoken,
                'date_added' => $dateTime,
                'date_updated' => $dateTime,
                'status' => 1,

            );
            $condition = "token='" . $fctoken . "' ";
            $db->insert("tokens", $UpdateTokenParam);
            return SuccessMessage($response, 'Token recorded Successfully');
        }
    } else if ($isAuth === 2) {

        return ErrorMessage($response, 'Invalid Api Key');
    } else {
        return ErrorMessage($response, 'Please Supply your API token');
    }
});
/* * *---------------------------------- Verify Token --------------------------------------------------------* */



$app->group('/hiddenhistory', function () use ($app) {

    $app->group('/users', function () use ($app) {
        $app->get('', 'App\Controller\User\GetAllUsers');
        $app->get('/[{id}]', 'App\Controller\User\GetOneUser'); //->add(new App\Middleware\AuthMiddleware($app))
        $app->put('profile/[{id}]', 'App\Controller\User\UpdateUser');
    });
    $app->group('/tours', function () use ($app) {
        $app->get('', 'App\Controller\Tour\GetAllTours'); //fetch all tours saved by all users
        $app->get('/[{id}]', 'App\Controller\Tour\GetSavedTours'); //get all tours save by a user 
        $app->post('', 'App\Controller\Tour\saveTour'); //saveTour
        $app->delete('/[{id}]', 'App\Controller\Tour\DeleteTour');
        $app->put('like/[{id}]', 'App\Controller\Tour\LikeArticle'); //like article
        $app->put('dislike/[{id}]', 'App\Controller\Tour\DislikeArticle'); //dislike article
    });
});
