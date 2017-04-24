<?php


namespace App\Controllers;

/**
 * Class PageController - main site page
 *
 * @package App\Controllers
 */
class PageController extends  BaseController
{
    /**
     * Show main page with data from db
     *
     * Request $request
     *
     * Responce $responce
     *
     * @return mixed
     */
    public function Index($request,$responce)
    {

        $arr = [];

       //Select db and get all data from collection
        $db = $this->mongo->selectDB("test");

        $collection = $db->test;

        $cursor = $collection->find();

        if(!empty($cursor)) {
            $array = iterator_to_array($cursor);
            foreach ($array as $value) {
                $params["index"] = "test";
                $params["type"] = "test_type";
                $params["id"] = $value["_id"];
                $arr[] = $this->elastic->get($params);
            }

        }

        //Create users zakaz data for showing in main page
        $userdata = [];
        foreach ($arr as $value)
        {
           $userdata[] = $value["_source"];
        }

        return $this->view->render($responce,"app.twig",["zakaz" => $userdata]);
    }
}