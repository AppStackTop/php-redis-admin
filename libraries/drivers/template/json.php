<?php
class JsonTemplate
{
    public function render($data, $setHeader = False)
    {
        $setHeader AND ($data ? header('HTTP/1.0 200 OK') : header('HTTP/1.0 404 Not Found'));

        echo json_encode($data);
        die();
    }
}