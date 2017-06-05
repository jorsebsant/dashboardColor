function Dashboard(){

	this.config = {
      startDate: new DayPilot.Date("2017-04-01").firstDayOfMonth(),
      days: 650,
      timeHeaders: [
          { groupBy: "Month", format: "MMMM/yy" },
          { groupBy: "Day", format: "d" }
        ],
      scale: "Day",
      resources:[],
      treeEnabled: true,
      events: [],
      dynamicEventRendering: "Disabled",
      cellWidth : 30,
      locale: "es-mx"
  }

  this.Scheduler = {};

}

Dashboard.prototype.init = function(){
	this.getInitialData();
}

Dashboard.prototype.getInitialData = function(){
	var _this = this;
	$.get("./controller/data.json").done(function(resources){		
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
  this.createNavigation();
}

Dashboard.prototype.changeCellScale = function(obj,$button){
  var _this = obj;
  var type = $button.data("type");
  if(type == "Day"){

    _this.config.timeHeaders = [
      { groupBy: "Month", format: "MMMM/yy" },
      { groupBy: "Day", format: "d" }
    ];

    _this.config.scale = type;
    _this.config.cellWidth = 30;

  }else if(type == "Week"){
     _this.config.timeHeaders = [
      { groupBy: "Month", format: "MMMM/yy" },
      { groupBy: "Week"}
    ];

    _this.config.scale = type;
    _this.config.cellWidth = 30;

  }else{


    _this.config.timeHeaders = [
      { groupBy: "Month", format: "M/yy" }
    ];

    _this.config.scale = type;
    _this.config.cellWidth = 60;
  }

  _this.updateScheduler();

}

Dashboard.prototype.createNavigation = function(){
  var nav = new DayPilot.Navigator("nav");
  _this = this;
  nav.selectMode = "week";
  nav.onTimeRangeSelected = function(args) {
      _this.Scheduler.startDate = args.start;
      // load events
      _this.Scheduler.update();
  };
  nav.init();
}

Dashboard.prototype.updateScheduler = function(){
  this.cleanScheduler();
  this.createCheduler();
}

Dashboard.prototype.cleanScheduler = function(){

  this.Scheduler.dispose();

}

Dashboard.prototype.updateDate = function(){
  var ini = $("#fecha_inicio").val();
  var fin = $("#fecha_fin").val();
  this.Scheduler.startDate = ini;
  if(fin !=""){
    this.Scheduler.days = this.daysBetween(ini,fin);
  }
  this.Scheduler.update();
}

Dashboard.prototype.updateData = function(){
  $.get("./controller/backend_resources.php").done(function(){
    window.location = location;
  });    
}

Dashboard.prototype.treatAsUTC = function(date) {
    var result = new Date(date);
    result.setMinutes(result.getMinutes() - result.getTimezoneOffset());
    return result;
}

Dashboard.prototype.daysBetween = function(startDate, endDate) {
    var millisecondsPerDay = 24 * 60 * 60 * 1000;
    return (this.treatAsUTC(endDate) - this.treatAsUTC(startDate)) / millisecondsPerDay;
}
