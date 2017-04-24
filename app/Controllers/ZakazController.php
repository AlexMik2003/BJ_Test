<?php

namespace App\Controllers;

use \Respect\Validation\Validator as valid;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class ZakazController
 *
 * @package App\Controllers
 */
class ZakazController extends BaseController
{
    /**
     * Create zakaz
     * Take data about zakaz in db
     * Prepare mail ro user about zakaz
     *
     * Request $request
     *
     * Responce $responce
     *
     * @return mixed
     */
    public function createZakaz($request,$responce)
    {
        //Check if input user data is valid
        $validation = $this->validator->validate($request,[
            'name' => valid::noWhitespace()->notEmpty(),
            'surname' => valid::noWhitespace()->notEmpty(),
            'email' => valid::noWhitespace()->notEmpty()->email(),
        ]);

        $vendor = array_filter($request->getParam("vendor"));
        $product = array_filter($request->getParam("product"));


        if($validation->failed() || empty($vendor) || empty($product))
        {
            return $responce->withRedirect($this->router->pathFor("index"));
        }

        //Create array of user input data
        $line = [];
        $count = count($vendor);
        for($i=0;$i<$count;$i++)
        {
            $line[]=[
              "vendor" => $vendor[$i],
                "product" => $product[$i],
                "count" => $request->getParam("count")[$i],
                "price" => $request->getParam("price")[$i],
            ];
        }

        $mongo = $this->db->test;
        $date = (new \DateTime())->format('Y-m-d H:i:s');

        //Create users zakaz and put it in db
        $zakaz = [
          "name" => ucfirst($request->getParam("name")),
            "surname" => ucfirst($request->getParam("surname")),
            "email" => strtolower($request->getParam("surname")),
            "date" => $date,
            "zakaz" => $line,
        ];

        $price = 0;
        foreach ($line as $value)
        {
            $price += ($value["count"] * $value["price"]);
        }


        if($mongo->insert($zakaz))
        {
            //Create connect to rabbit
            $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
            //Create channel
            $channel = $connection->channel();
            //Declare mail queue
            $channel->queue_declare('email_queue', false, false, false, false);

            //Create new message
            $msg = new AMQPMessage($zakaz);
            //Publish message in mail queue
            $channel->basic_publish($msg, '', 'email_queue');
            //Close connection
            $channel->close();
            $connection->close();


            //Create entry in elastic
            $params = [];
            $params["body"] = [
                "date" => $date,
                "name" => $request->getParam("name"),
                "surname" => $request->getParam("surname"),
                "email" => $request->getParam("email"),
                "price" => $price,
            ];

            $params["index"] = "test";
            $params["type"] = "test_type";
            $params["id"] = $zakaz["_id"];

            $str = $this->elastic->index($params);

        }

        return $responce->withRedirect($this->router->pathFor("index"));
    }
}