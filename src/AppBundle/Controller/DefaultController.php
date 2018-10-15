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
     * @Route("/tstsrv", name="tstsrv")
     */
    public function tstService()
    {
    	$myservice = $this->get('WSXFLEX');
    	$result = $myservice->getEmployees();
    	var_dump($result);
    	//return $this->render('base.html.twig');
    	
    	$myservice = $this->get('WSVP');
    	$result = $myservice->getAttributes();
    	var_dump($result);
    	return $this->render('base.html.twig');
    	
    }
    
    /**
     * @Route("/sync_emp", name="sync_emp")
     */
    public function syncEmployees()
    {
    	$employees = $this->get('WSXFLEX')->getEmployees();
    	
    	foreach($employees as $employee)
    	{
    		$this->get('WSVP')->setEmployee($employee);
    	}
    	
    	return $this->render('base.html.twig');
    }
    
    /**
     * @Route("/sync_proj", name="sync_proj")
     */
    public function syncProjects()
    {
    	// level 1 Xflex projects
    	$projects = $this->get('WSXFLEX')->getProjects(1);
    	 
    	foreach($projects as $project)
    	{
    		$this->get('WSVP')->setProject($project, 1);
    	}
    	
    	// level 2 Xflex projects
    	$projects = $this->get('WSXFLEX')->getProjects(2);
    	
    	foreach($projects as $project)
    	{
    		$this->get('WSVP')->setProject($project, 2);
    	}
    	
    	// level 3 Xflex projects
    	$projects = $this->get('WSXFLEX')->getProjects(3);
    	
    	foreach($projects as $project)
    	{
    		$this->get('WSVP')->setProject($project, 3);
    	}
    	
    	// level 4 Xflex projects
    	$projects = $this->get('WSXFLEX')->getProjects(4);
    	
    	foreach($projects as $project)
    	{
    		$this->get('WSVP')->setProject($project, 4);
    	}
    	
    	return $this->render('base.html.twig');
    }
    
    /**
     * @Route("/sync_rdv", name="sync_rdv")
     */
    public function syncAppointments()
    {
    	$appointments = $this->get('WSXFLEX')->getAppointments();
    	
    	foreach($appointments as $appointment)
    	{
    		$this->get('WSVP')->setAppointment($appointment);
    	}
    	
    	return $this->render('base.html.twig');
    }
    
    /**
     * @Route("/sync_cas", name="sync_cas")
     */
    public function syncControlOrderDates()
    {
    	$dates = $this->get('WSXFLEX')->getControlOrderDates(4);
    	 
    	foreach($dates as $date)
    	{
    		$installation = null;
    		$field = null;
    		
    		$installations = $this->get('WSXFLEX')->getControlOrderInst($date->CAPNR);
    		if(!empty($installations)) {
    			$installation = $installations[0];
    			$fields = $this->get('WSXFLEX')->getControlOrderFields($date->CAPNR, $installation->PRONR);
    			if(!empty($fields)) {
    				$field = $fields[0];
    			}
    		}
    			
    		$this->get('WSVP')->setControlOrderDate($date, $installation, $field, 4); // 4 = static employee! Use a variable!!
    	}
    	 
    	return $this->render('base.html.twig');
    }
    
    /**
     * @Route("/newval", name="newval")
     */
    public function addNewValue()
    {   	
    	$result = $this->get('WSVP')->getEventCompteur();
    	
    	foreach($result->entities as $releve)
    	{
    		$value		= $releve[0]->entityValue;
    		$project 	= $releve[1]->entityValue;
    		$field		= $releve[2]->entityValue;
    		
    		if($value!='' && $project!='' && $field!='') {
    			
    			$this->get('WSXFLEX')->setControlOrderValue($project, $field, $value);
    		}
    		
    	}
    	
    	return $this->render('base.html.twig');
    }
    
}
