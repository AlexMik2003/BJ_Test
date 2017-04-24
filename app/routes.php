<?php


$app->get("/", "page:Index")->setName("index");

$app->post("/zakaz", "zakaz:createZakaz")->setName('zakaz');

$app->post("/search", "search:Search")->setName('search');

