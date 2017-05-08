<?php
require_once 'teamwork.php';
    

class Group {}
class Resource {}
class Event {}
class Tasks{}

$d = new Data();
$d->addGroups();
$d->generateFiles();

class Data {

  private $scheduler_groups;
  private $projects;
  private $groups;
  private $events;

  public function __construct(){
    $this->groups = array();
    $this->events = array();
    $teamwork = new Teamwork();
    $this->scheduler_groups = $teamwork->getCompanies();
    $this->projects = $teamwork->getProyectsPerCompany();
    $this->tasks = $teamwork->getTasksPerProyect();
    //echo json_encode($teamwork->getTasksPerProyect());
  }

  public function addGroups(){
    
    foreach($this->scheduler_groups as $group) {
      $g = new Group();
      $g->id = "group_".$group['id'];
      $g->name = $group['name'];
      $g->expanded = false;
      $g->children = array();
      $this->addProjects($group, $g);      
    }  
  }

  public function addProjects($group,$obj){
    $projects = $this->projects[$group['id']];
    if(!empty($projects)){
        
        foreach($projects as $resource) {
          $r = new Resource();
          $r->id = $resource['id'];
          $r->name = $resource['name'];
          $this->addEvents($resource);
          $this->addTasks($r);          
          $obj->children[] = $r;
        }
        $this->groups[] = $obj;
    }  
  }

  public function addEvents($resource){
    //Añadir elemento a los eventos!
    $time = strtotime($resource["startDate"]);
    $startDate = date('Y-m-d',$time);
    $time = strtotime($resource["endDate"]);
    $endDate = date('Y-m-d',$time);
    $e = new Event();
    $e->start = $startDate;
    $e->end = $endDate;
    $e->id = "evento_".$resource["id"];
    $e->resource = $resource['id'];
    $e->text = $resource['name'];
    $e->backColor = "#000000";
    $this->events[] = $e;       
  }

  public function addTasks($r){
    
    if(!empty($this->tasks[$r->id])){

      foreach($this->tasks[$r->id] as $key => $tasklist) {
       
        if($tasklist['startDate'] != "" && $tasklist['endDate'] != ""){
          $t = new Tasks();
          $t->id = $tasklist['id'];
          $t->name = $key;
          $r->children[]= $t;
        }               
      }
    }
  }

  // public function addTasksEvents($tasks){
  //    //Añadir elemento a los eventos!
  //   $time = strtotime($resource["startDate"]);
  //   $startDate = date('Y-m-d',$time);
  //   $time = strtotime($resource["endDate"]);
  //   $endDate = date('Y-m-d',$time);
  //   $e = new Event();
  //   $e->start = $startDate;
  //   $e->end = $endDate;
  //   $e->id = "evento_".$resource["id"];
  //   $e->resource = $resource['id'];
  //   $e->text = $resource['name'];
  //   $e->backColor = "#000000";
  //   $this->events[] = $e;    
  //}

  public function generateFiles(){
    $this->createJsonFile("data", $this->groups);
    $this->createJsonFile("eventos", $this->events);    
  }

  public function createJsonFile($fileName, $array){
    $handle = fopen($fileName.".json", "w");
    fwrite($handle, json_encode($array));
    fclose($handle);
  }  

}
?>