function Dashboard(){

		this.config = {
	      startDate: new DayPilot.Date("2017-04-01").firstDayOfMonth(),	     
	      days: 800,
	      timeHeaders: [
	          { groupBy: "Month", format: "MMMM yyyy" },
	          { groupBy: "Day", format: "d" }
	        ],
	      scale: "Day",
	      resources:[],
	      treeEnabled: true,
	      events: []
	    }		
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

Dashboard.prototype.createCheduler= function(){
	console.log(this.config);
	$("#dp").daypilotScheduler(this.config);
}

 