<?php

namespace AppBundle\Service;


class WSXflex
{
	private $client;
	private $cid;
	private $uid;
	private $upw;
	private $json_request;
	
    public function __construct($client, $cid, $uid, $upw) {
    	$this->client 	= $client;
    	$this->cid		= $cid;
    	$this->uid		= $uid;
    	$this->upw		= $upw;  
    	
    	$this->setAuthStructure();
    }
    
    public function getEmployees()
    {
    	$response = $this->client->post('employee', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	$this->json_request,
    			
    	]);
    
    	return json_decode($response->getBody());
    }
    
    public function getProjects($level)
    {
    	$this->json_request["PRTLEV"] = $level;
    	
    	$response = $this->client->post('project', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	$this->json_request,
    	]);
    
    	return json_decode($response->getBody());
    }
    
    public function getAppointments()
    {
    	$response = $client->post('caqread', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	$this->json_request,
    			]
    	]);
    
    	return json_decode($response->getBody());
    }
    
    public function getControlOrderDates($employee)
    {
    	$this->json_request["DTB"] = "2018-10-01";
    	$this->json_request["DTE"] = "2018-12-31";
    	$this->json_request["EMPNR"] = $employee;
    	
    	$response = $client->post('cas', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	$this->json_request,
    	]);
    
    	return json_decode($response->getBody());
    }
    
    public function getControlOrderInst($capnr)
    {
    	$this->json_request["CAPNR"] = $capnr;
    
    	$response = $client->post('coins', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	$this->json_request,
    	]);
    
    	return json_decode($response->getBody());
    }
    
    public function getControlOrderFields($capnr, $pronr)
    {
    	$this->json_request["CAPNR"] = $capnr;
    	$this->json_request["PRONR"] = $pronr;
    
    	$response = $client->post('cofld', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	$this->json_request,
    	]);
    
    	return json_decode($response->getBody());
    }
    
    public function setControlOrderValue($pronr, $dfdpk, $newval)
    {
    	$this->json_request["PRONR"] 	= $pronr;
    	$this->json_request["DFDPK"] 	= $dfdpk;
    	$this->json_request["NEWVAL"] 	= $newval;
    
    	$response = $client->post('coadd', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	$this->json_request,
    	]);
    
    	return json_decode($response->getBody());
    }
    
    private function setAuthStructure()
    {
    	$this->json_request["ACCDAT"] = ["CID" => $this->cid, "UID" => $this->uid, "UPW" => $this->upw];
    }
    
}