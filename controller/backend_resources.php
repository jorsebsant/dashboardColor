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
    $this->tasksLists = $teamwork->getTaskLists();  
    $this->colors = array(
                      "26432" => "#263238", //COLOR
                      "88563" => "#252525", //ARISTAS
                      "26515" => "#FFC107", //ASTURIAS
                      "78424" => "#1A237E", //GEOMATRIX
                      "75705" => "#039BE5", //ULTRA
                      "82068" => "#B71C1C", //ULTRACK
                      "27329" => "#3F51B5", //ILUMNO
                      "83584" => "#D50000", //INNOVATE
                      "27328" => "#64B5F6" //SMART
                    );  
  }

  public function addGroups(){
    
    foreach($this->scheduler_groups as $group) {
      $g = new Group();
      $g->id = "group_".$group['id'];
      $g->name = $group['name'];
      $g->expanded = true;
      $g->children = array();
      $this->addProjects($group, $g);      
    }  
  }

  public function addProjects($group,$obj){
    $id_cliente = $group['id'];
    $projects = $this->projects[$id_cliente];
    if(!empty($projects)){
        
        foreach($projects as $resource) {
          $r = new Resource();
          $r->id = $resource['id'];
          $r->name = $resource['name'];
          $this->addEvents($resource,$id_cliente);
          $this->addTasksLists($r,$id_cliente);          
          $obj->children[] = $r;
        }
        $this->groups[] = $obj;
    }  
  }

  public function addEvents($resource, $id_cliente){
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
    $e->text = "";
    if(isset($this->colors[$id_cliente])){
      $e->backColor = $this->colors[$id_cliente];
      $e->cssClass = "white_text";
    }
    
    $e->barHidden = true;    
    $this->events[] = $e;       
  }

  public function addTasksLists($r,$id_cliente){
    
    if(!empty($this->tasksLists[$r->id])){

      foreach($this->tasksLists[$r->id] as $key => $tasklist) {
       
        if($tasklist['startDate'] != "" && $tasklist['endDate'] != ""){
          $t = new Tasks();
          $t->id = $tasklist['id'];
          $t->name = $key;
          $tasklist["name"] = $key;
          $this->addEvents($tasklist,$id_cliente);
          $r->children[]= $t;
        }               
      }
    }
  }

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