<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    
    /**
     * @Route("/sync_emp", name="sync_emp")
     */
    public function syncEmployees()
    {
    	$employees = $this->getEmployees();
    	
    	foreach($employees as $employee)
    	{
    		$this->setEmployee($employee);
    	}
    	
    	return $this->render('base.html.twig');
    }
    
    /**
     * @Route("/sync_proj", name="sync_proj")
     */
    public function syncProjects()
    {
    	// level 1 Xflex projects
    	$projects = $this->getProjects(1);
    	 
    	foreach($projects as $project)
    	{
    		$this->setProject($project, 1);
    	}
    	
    	// level 2 Xflex projects
    	$projects = $this->getProjects(2);
    	
    	foreach($projects as $project)
    	{
    		$this->setProject($project, 2);
    	}
    	
    	// level 3 Xflex projects
    	$projects = $this->getProjects(3);
    	
    	foreach($projects as $project)
    	{
    		$this->setProject($project, 3);
    	}
    	
    	// level 4 Xflex projects
    	$projects = $this->getProjects(4);
    	
    	foreach($projects as $project)
    	{
    		$this->setProject($project, 4);
    	}
    	
    	return $this->render('base.html.twig');
    }
    
    /**
     * @Route("/sync_rdv", name="sync_rdv")
     */
    public function syncAppointments()
    {
    	$appointments = $this->getAppointments();
    	
    	foreach($appointments as $appointment)
    	{
    		$this->setAppointment($appointment);
    	}
    	
    	return $this->render('base.html.twig');
    }
    
    /**
     * @Route("/sync_cas", name="sync_cas")
     */
    public function syncControlOrderDates()
    {
    	$dates = $this->getControlOrderDates(4);
    	 
    	foreach($dates as $date)
    	{
    		$installation = null;
    		$field = null;
    		
    		$installations = $this->getControlOrderInst($date->CAPNR);
    		if(!empty($installations)) {
    			$installation = $installations[0];
    			$fields = $this->getControlOrderFields($date->CAPNR, $installation->PRONR);
    			if(!empty($fields)) {
    				$field = $fields[0];
    			}
    		}
    			
    		$this->setControlOrderDate($date, $installation, $field, 4); // 4 = static employee! Use a variable!!
    	}
    	 
    	return $this->render('base.html.twig');
    }
    
    
    
    
    /*
     * VISUAL PLANNING
     */
    
    /* to put inside a service ! */
    /**
     * Sample request
     * 
     * @Route("/vpattr", name="vpattr")
     */
    public function getAttributes()
    {
    	$client   = $this->get('eight_points_guzzle.client.api_vp');
    	$response = $client->get('/demovp6/ws/rest/attributes?apikey=eccb85a1-a9b0-9919-97a2-a98820c98e4f');
    
    	//return $response;
    	return new JsonResponse(json_decode($response->getBody(),true));
    }
    
    
    
    public function setEmployee($employee)
    {
    	$client   = $this->get('eight_points_guzzle.client.api_vp');
    
    	$response = $client->put('/demovp6/ws/rest/resource/modify', [
    			'headers' => ["apikey"=>"eccb85a1-a9b0-9919-97a2-a98820c98e4f", "Content-Type"=>"application/json"],
    			'json' => 	[
    						'attributes' => [
    							["entityName" => "No", "entityValue" => $employee->EMPNR],
    							["entityName" => "Nom", "entityValue" => $employee->EMP],
    						],
    						'keys' => [
    							["entityName" => "No", "entityValue" => $employee->EMPNR],
    						],
    						"resourceModel" => "COLLABORATEURS",
    						"forceCreate" => "true",
    			]
    
    	]);
    
    	return new JsonResponse(json_decode($response->getBody(),true));
    }
    
    public function setProject($project, $level)
    {
    	$client   = $this->get('eight_points_guzzle.client.api_vp');
    
    	$response = $client->put('/demovp6/ws/rest/resource/modify', [
    			'headers' => ["apikey"=>"eccb85a1-a9b0-9919-97a2-a98820c98e4f", "Content-Type"=>"application/json"],
    			'json' => 	[
    					'attributes' => [
    							["entityName" => "No", "entityValue" => $project->PRONR],
    							["entityName" => "Nom", "entityValue" => $project->PRO],
    							["entityName" => "Niveau", "entityValue" => $level],
    					],
    					'keys' => [
    							["entityName" => "No", "entityValue" => $project->PRONR],
    					],
    					"resourceModel" => "PROJETS",
    					"forceCreate" => "true",
    			]
    
    	]);
    
    	return new JsonResponse(json_decode($response->getBody(),true));
    }
    
    public function setAppointment($appointment)
    {
    	$client   = $this->get('eight_points_guzzle.client.api_vp');
    
    	$response = $client->post('/demovp6/ws/rest/event/add', [
    			'headers' => ["apikey"=>"eccb85a1-a9b0-9919-97a2-a98820c98e4f", "Content-Type"=>"application/json"],
    			'json' => 	[
    					'attributes' => [
    							["entityName" => "Evénement-Date de début", "entityValue" => str_replace('T',' ',$appointment->DTB)],
    							["entityName" => "Evénement-Date de fin", "entityValue" => str_replace('T',' ',$appointment->DTE)],
    							["entityName" => "COLLABORATEURS-No", "entityValue" => $appointment->EMPNR],
    							["entityName" => "PROJETS-No", "entityValue" => $appointment->PRONR],
    							["entityName" => "Saisie heures-Sujet", "entityValue" => $appointment->NAS],
    							["entityName" => "Saisie heures-Informations", "entityValue" => $appointment->NAL],
    							["entityName" => "Saisie heures-Priorité", "entityValue" => $appointment->PRIO],
    							["entityName" => "Saisie heures-CAQNR", "entityValue" => $appointment->CAQNR],
    							
    					]
    			]
    
    	]);
    
    	return new JsonResponse(json_decode($response->getBody(),true));
    }
    
    public function setControlOrderDate($date, $installation, $field, $employee)
    {
    	$client   = $this->get('eight_points_guzzle.client.api_vp');
    
    	$tmpStartDate = explode('T', $date->DT);
    	$tmpEndDate = explode('T', $date->TM);
    	
    	$beginDate = str_replace('T',' ',$date->DT);
    	$endDate = $tmpStartDate[0] . ' ' . $tmpEndDate[1];
    	
    	$note = $date->PROTOP . ' / ' . $date->{'PRO-1'} . ' / '. $date->{'PROINR-1'} . ' / ' . $date->PROINR;
    	
    	if($installation != null)
    	$aAttributes = [
    							["entityName" => "Evénement-Date de début", "entityValue" => $beginDate],
    							["entityName" => "Evénement-Date de fin", "entityValue" => $endDate],
    							["entityName" => "COLLABORATEURS-No", "entityValue" => $employee],
    							["entityName" => "PROJETS-No", "entityValue" => $installation->PRONR],
    							["entityName" => "Relevé compteurs-Sujet", "entityValue" => $date->PRONAS],
    							["entityName" => "Evénement-Note", "entityValue" => $note],
    							["entityName" => "Relevé compteurs-CAPNR", "entityValue" => $date->CAPNR],
    							["entityName" => "Relevé compteurs-DFDPK", "entityValue" => $field->DFDPK],
    							["entityName" => "Relevé compteurs-Ancienne valeur", "entityValue" => $field->CONTROLLASTVALUE],
    					];
    	else
    		$aAttributes = [
    				["entityName" => "Evénement-Date de début", "entityValue" => $beginDate],
    				["entityName" => "Evénement-Date de fin", "entityValue" => $endDate],
    				["entityName" => "COLLABORATEURS-No", "entityValue" => $employee],
    				["entityName" => "Relevé compteurs-Sujet", "entityValue" => $date->PRONAS],
    				["entityName" => "Evénement-Note", "entityValue" => $note],
    				["entityName" => "Relevé compteurs-CAPNR", "entityValue" => $date->CAPNR],
    		];
    	
    	$response = $client->post('/demovp6/ws/rest/event/add', [
    			'headers' => ["apikey"=>"eccb85a1-a9b0-9919-97a2-a98820c98e4f", "Content-Type"=>"application/json"],
    			'json' => 	[
    					'attributes' => $aAttributes
    			]
    
    	]);
    
    	return new JsonResponse(json_decode($response->getBody(),true));
    }
    
    
    
    
    
    
    
    /*
     * XFLEX
     */
    
    public function getProjects($level)
    {
    	$client   = $this->get('eight_points_guzzle.client.api_xflex');
    	
    	$response = $client->post('/api/project', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	[	"ACCDAT" => ["CID" => "DEMO2", "UID" => "lme", "UPW" => "Lbsp3DRn"],
    								"PRTLEV" => $level
    							]
    	]);
    
    	return json_decode($response->getBody());
    }
    
    
    public function getEmployees()
    {
    	$client   = $this->get('eight_points_guzzle.client.api_xflex');
    	 
    	$response = $client->post('/api/employee', [
    				'headers' 	=> 	[	"Content-Type"=>"application/json"],
    				'json' 		=> 	[	"ACCDAT" => ["CID" => "DEMO2", "UID" => "lme", "UPW" => "Lbsp3DRn"],
    			]
    	]);
    
    	return json_decode($response->getBody());
    }
    
    
    public function getAppointments()
    {
    	$client   = $this->get('eight_points_guzzle.client.api_xflex');
    
    	$response = $client->post('/api/caqread', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	[	"ACCDAT" => ["CID" => "DEMO2", "UID" => "lme", "UPW" => "Lbsp3DRn"],
    			]
    	]);
    
    	return json_decode($response->getBody());
    }
    
    /**
     * @Route("/xcas", name="xcas")
     */
    public function getControlOrderDates($employee)
    {
    	$client   = $this->get('eight_points_guzzle.client.api_xflex');
    
    	$response = $client->post('/api/cas', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	[	"ACCDAT" => ["CID" => "DEMO2", "UID" => "lme", "UPW" => "Lbsp3DRn"],
    								"DTB"	=> "2018-10-01",
    								"DTE"	=> "2018-12-31",
    								"EMPNR" => $employee,
    			]
    	]);
    
    	return json_decode($response->getBody());
    	//return new JsonResponse(json_decode($response->getBody(),true));
    }
    
    public function getControlOrderInst($capnr)
    {
    	$client   = $this->get('eight_points_guzzle.client.api_xflex');
    
    	$response = $client->post('/api/coins', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	[	"ACCDAT" => ["CID" => "DEMO2", "UID" => "lme", "UPW" => "Lbsp3DRn"],
    								"CAPNR" => $capnr,
    			]
    	]);
    
    	return json_decode($response->getBody());
    }
    
    public function getControlOrderFields($capnr, $pronr)
    {
    	$client   = $this->get('eight_points_guzzle.client.api_xflex');
    	 
    	$response = $client->post('/api/cofld', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	[	"ACCDAT" => ["CID" => "DEMO2", "UID" => "lme", "UPW" => "Lbsp3DRn"],
    								"CAPNR" => $capnr,
    								"PRONR" => $pronr,
    			]
    	]);
    
    	return json_decode($response->getBody());
    }
    
    /**
     * @Route("/xcoadd", name="xcoadd")
     */
    public function getControlOrderValueAdd()
    {
    	$client   = $this->get('eight_points_guzzle.client.api_xflex');
    
    	$response = $client->post('/api/coadd', [
    			'headers' 	=> 	[	"Content-Type"=>"application/json"],
    			'json' 		=> 	[	"ACCDAT" => ["CID" => "DEMO2", "UID" => "lme", "UPW" => "Lbsp3DRn"],
    					"PRONR" => "146",
    					"DFDPK" => "10",
    					"NEWVAL" => "5500",
    			]
    	]);
    
    	//return json_decode($response->getBody());
    	return new JsonResponse(json_decode($response->getBody(),true));
    }
}
