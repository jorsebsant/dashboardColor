<?php
class Teamwork{
	
	private static $key="udder452cache";				
	private static $company = "coloralcuadrado";
	
	public $companies;

	public function __construct(){
		$this->companies = [];
		$this->projects = [];
		$this->tasks = [];
		$this->taskList = [];
	}

	private function curlApi($action){

		$channel = curl_init();	

		curl_setopt( $channel, CURLOPT_URL, "https://". self::$company .".teamwork.com/". $action );
		curl_setopt( $channel, CURLOPT_RETURNTRANSFER, 1 ); 				
		curl_setopt( $channel, CURLOPT_HTTPHEADER, 
		    array( "Authorization: BASIC ". base64_encode( self::$key .":udder452cache" ))
		);
		curl_setopt($channel, CURLOPT_ENCODING, '');
		//Windows
		curl_setopt($channel, CURLOPT_SSL_VERIFYPEER, false);	
		$json = curl_exec ( $channel );	
		$obj = json_decode($json, true );
		curl_close ( $channel );
		return $obj;
	}

	public function getCompanies(){	
		$action = "companies.json";
		$companies = $this->curlApi($action)["companies"];
		$this->companies = $companies;
		return $companies;
	}

	public function getProyectsPerCompany(){
		$projects = [];
		foreach ($this->companies as $key => $company) {
			$action = "companies/{$company['id']}/projects.json";
			$projects[$company['id']] = $this->curlApi($action)["projects"];
		}
		$this->projects = $projects;
		return $projects;
	}

	public function getTasksPerProyect(){
		
		foreach ($this->projects as $key => $value) {
			if(!empty($value)){
				$action = "projects/{$value[0]['id']}/tasks.json?getSubTasks=false";
				$todoitems = $this->curlApi($action)["todo-items"];				
				$this->tasks = array_merge($this->tasks,$todoitems);
				$this->orderTasksPerTasklist($todoitems);				
			}
			
		}
		//echo json_encode($this->tasks);		
		return $this->orderTasks($this->tasks);
	}

	public function orderTasksPerTaskList($todoitems){
		foreach ($todoitems as $item) {
			$this->taskList[$item['project-id']][$item['todo-list-name']][] =$item;
		}
	}

	public function getTaskLists(){
		
		return $this->orderTasksLists($this->taskList);
	}

	public function orderTasks($tasks){
		$sortTask = [];
		foreach ($tasks as $task) {
			$sortTask[$task['todo-list-id']][] = $task;
		}
		return $sortTask;
	}

	public function orderTasksLists($projects){
		$sortTask = [];
		foreach ($projects as $key => $project) {
			
			foreach ($project as $name => $todolist) {

				foreach ($todolist as $todoitem) {
					$sortTask[$key][$name]["id"] = $todoitem["todo-list-id"];
					if(!isset($sortTask[$key][$name]["startDate"])){
						$sortTask[$key][$name]["startDate"] = $todoitem['start-date'];
					}else{
						if($todoitem["start-date"] < $sortTask[$key][$name]["startDate"] || $sortTask[$key][$name]["startDate"]  == ""){

							$sortTask[$key][$name]["startDate"] = $todoitem["start-date"];
						}
					}
					if(!isset($sortTask[$key][$name]["endDate"])){
						$sortTask[$key][$name]["endDate"] = $todoitem['due-date'];
					}else{
						if($todoitem["due-date"] > $sortTask[$key][$name]["endDate"] || $sortTask[$key][$name]["endDate"]  == ""){

							$sortTask[$key][$name]["endDate"] = $todoitem["due-date"];
						}
					}
				}							
			}
		}
		return $sortTask;
	}

}
?>