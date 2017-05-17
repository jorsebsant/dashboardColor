function Dashboard(){

	this.config = {
      startDate: new DayPilot.Date("2017-04-01").firstDayOfMonth(),	     
      days: 800,
      timeHeaders: [
          { groupBy: "Month", format: "MMMM/yy" },
          { groupBy: "Day", format: "d" }
        ],
      scale: "Day",
      resources:[],
      treeEnabled: true,
      events: [],
      dynamicEventRendering: "Disabled"
  }

  this.Scheduler = {};

}

Dashboard.prototype.init= function(){	
	this.getInitialData();  
}

Dashboard.prototype.getInitialData = function(){
	var _this = this;
	$.get("./controller/data.json").done(function(resources){
		console.log(resources);
		_this.config.resources = resources;
		_this.getEvents();
	});
}

Dashboard.prototype.getEvents = function(){
	var _this = this;	
	$.get("./controller/eventos.json").done(function(events){
		_this.config.events = events;
		_this.createCheduler();	
	});
}

Dashboard.prototype.createCheduler = function(){

	var config = this.config;
	this.Scheduler = $("#dp").daypilotScheduler(config);
  console.log(this.Scheduler)
}

Dashboard.prototype.changeCellScale = function(obj){
  var _this = obj;
  var type = $(this).data('type');
  if(type == "day"){

    _this.config.timeHeaders = [
      { groupBy: "Month", format: "MMMM/yy" },
      { groupBy: "Day", format: "d" }
    ];

    _this.config.scale = "Day";

  }else{

    _this.config.timeHeaders = [
      { groupBy: "Month", format: "MMMM/yy" }
    ];

    _this.config.scale = "Month";
  }

  _this.updateScheduler(); 

}

Dashboard.prototype.updateScheduler = function(){
  this.cleanScheduler(); 
  this.createCheduler();
}

Dashboard.prototype.cleanScheduler = function(){
  
  this.Scheduler.dispose();
  
}

 