function Dashboard(){

		this.config = {
	      startDate: new DayPilot.Date("2017-04-01").firstDayOfMonth(),	     
	      days: 800,
	      //bubble: new DayPilot.Bubble(),
	      timeHeaders: [
	          { groupBy: "Month", format: "MMMM yyyy" },
	          { groupBy: "Day", format: "d" }
	        ],
	      scale: "Day",
	      resources:[],
	      treeEnabled: true,
	      events: [],
	      dynamicEventRendering: "Disabled"
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
	$.ajax({
        url: './controller/eventos.json',
        success: function (events) {
           _this.config.events = events;
		   _this.createCheduler();
        },
        async: false
    });
	// $.get("./controller/eventos.json").done(function(events){
		
	// });
}

Dashboard.prototype.createCheduler= function(){
	
	var config = this.config;
	var dp = $("#dp").daypilotScheduler(config);
	
	// dp.onEventClick = function(args) {
	// 	console.log(args);
 //        var modal = new DayPilot.Modal();
 //        modal.onClosed = function(args) {
 //            // reload all events
 //            var result = args.result;
 //            if (result && result.status === "OK") {
 //                loadEvents();
 //            }
 //        };
 //        modal.showHtml("<h1>HOLA WORLD</h1>");
 //    };
}

 