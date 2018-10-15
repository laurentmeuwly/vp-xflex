<?php 

namespace AppBundle\Service;

class WSVisualPlanning
{
	private $client;
	private $apikey;
	
	public function __construct($client, $apikey) {
		$this->client 	= $client;
		$this->apikey 	= $apikey;
	}
	
	public function getAttributes()
	{
		$response = $this->client->get('attributes?apikey=' . $this->apikey);
		
		return json_decode($response->getBody());
	}
	
	public function getEventCompteur()
	{
		$aAttributes = [
				"Relevé compteurs-Nouvelle valeur",
				"PROJETS-No",
				"Relevé compteurs-DFDPK",
				"Relevé compteurs-CAPNR"
		];
		 
		$response = $client->post('event/get', [
				'headers' => ["apikey"=>$this->apikey, "Content-Type"=>"application/json"],
				'json' => 	[
						'attributes' => $aAttributes
				]
				 
		]);
		 
		return json_decode($response->getBody());
	}
	
	
	public function setEmployee($employee)
	{	
		$response = $this->client->put('resource/modify', [
				'headers' => ["apikey"=>$this->apikey, "Content-Type"=>"application/json"],
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
	
		return json_decode($response->getBody());
	}
	
	public function setProject($project, $level)
	{
		$response = $this->client->put('resource/modify', [
				'headers' => ["apikey"=>$this->apikey, "Content-Type"=>"application/json"],
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
	
		return json_decode($response->getBody());
	}
	
	public function setAppointment($appointment)
	{
		$response = $client->post('event/add', [
				'headers' => ["apikey"=>$this->apikey, "Content-Type"=>"application/json"],
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
	
		return json_decode($response->getBody());
	}
	
	public function setControlOrderDate($date, $installation, $field, $employee)
	{
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
					["entityName" => "Relevé compteurs-Trigger", "entityValue" => $field->TRIGGERNAME],
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
			 
		$response = $client->post('event/add', [
				'headers' => ["apikey"=>$this->apikey, "Content-Type"=>"application/json"],
				'json' => 	[
						'attributes' => $aAttributes
				]

		]);

		return json_decode($response->getBody());
	}
}