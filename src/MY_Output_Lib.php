<?php

namespace CiLibs;


use \CI_Output;

/**
 * Created by PhpStorm.
 * User: sai
 * Date: 12/16/15
 * Time: 3:26 PM
 */
class MY_Output_Lib extends CI_Output
{
    private $returnData = array();

    public function __construct()
    {
        parent::__construct();
        $this->returnData["status"] = 200;
        $this->returnData["reason"] = null;
        $this->returnData["data"] = null;
        $this->returnData["errors"] = array();

        $this->setDebug(date("Y-m-d H:i:s"), "now", "time");
    }

    public function nocache(){
        $this->set_header('Expires: Thu, 19 Nov 1981 08:52:00 GMT');
        $this->set_header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0, post-check=0, pre-check=0");
        $this->set_header("Pragma: no-cache");
    }

    public function sendUnauth($message = "Unauthorized: Check your authentication method."){
        $this->sendOutput(HttpStatus::UNAUTHORIZED, null, $message);
    }

    public function sendAppError($message = "Internal Server Error. Contact the Admin."){
        $this->sendOutput(HttpStatus::INTERNAL_SERVER_ERROR, null, $message);
    }

    public function sendOutput($status, $data = null, $reason = null, $errors = [] ){
        $this->setReturnData($status, $data, $reason, $errors);
        $this->_sendOutput();
    }

    private function _sendOutput(){
        $this->set_status_header($this->getStatus())
            ->set_output(json_encode($this->returnData))
            ->_display();

        exit();
    }

    public function getStatus(){
        return $this->returnData["status"];
    }

    public function setReturnData($status, $data = null, $reason = null, $errors = []){
        $this->returnData["status"] = $status;
        $this->returnData["reason"] = $reason;
        $this->returnData["data"] = $data;
        $this->returnData["errors"] = $errors;
        if( ENVIRONMENT !== "development" ){
            unset($this->returnData["debug"]);
        }
        return $this;
    }

    public function setDebug($debugData, $key = "", $level = "debug"){
        if(ENVIRONMENT == "development"){
            $key = $level."::".$key;
            $this->returnData["debug"][$key] = $debugData;
        }
    }

}
