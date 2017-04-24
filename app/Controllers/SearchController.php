<?php

namespace App\Controllers;

/**
 * Class SearchController - searching zakaz per different parameters
 *
 * @package App\Controllers
 */
class SearchController extends BaseController
{
    /**
     * Get search string and find data in db
     * Show data
     *
     * Request $request
     *
     * Responce $responce
     *
     * @return mixed
     */
    public function Search($request,$responce)
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

        //Create users zakaz data
        $userdata = [];
        $data = [];
        foreach ($arr as $value)
        {
            $userdata[] = [
                "data" => $value["_source"],
                "id" => $value["_id"],
            ];
            $udata[] = $value["_source"];
        }

        //Compare user zakaz data and search string
        // put result in array and show it
        $search = [];
        $time_start = $request->getParam("year")."-".$request->getParam("month")."-".$request->getParam("day")." 00:00:00";
        $time_end = $request->getParam("year")."-".$request->getParam("month")."-".$request->getParam("day")." 23:59:59";
        foreach ($userdata as $data)
        {
           if(strcasecmp($request->getParam("search"), $data["id"]) == 0 ||
                stripos($request->getParam("search"),$data["data"]["name"]) !==false ||
                stripos($request->getParam("search"),$data["data"]["surname"]) !==false ||
                stripos($request->getParam("search"),$data["data"]["email"]) !==false ||
                $request->getParam("price_from")<=$data["data"]["price"] && $data["data"]["price"]<=$request->getParam("price_to") ||
                strtotime($time_start)<=strtotime($data["data"]["date"]) && strtotime($data["data"]["date"])<=strtotime($time_end)
                )
            {
                $search[] = $data["data"];
            }


        }


        return $this->view->render($responce,"app.twig",["search" => $search,"zakaz" => $udata]);

    }
}