function Dashboard(){this.config={startDate:new DayPilot.Date("2017-04-01").firstDayOfMonth(),days:800,timeHeaders:[{groupBy:"Month",format:"MMMM/yy"},{groupBy:"Day",format:"d"}],scale:"Day",resources:[],treeEnabled:!0,events:[],dynamicEventRendering:"Disabled",cellWidth:30,locale:"es-mx"},this.Scheduler={}}Dashboard.prototype.init=function(){this.getInitialData()},Dashboard.prototype.getInitialData=function(){var e=this;$.get("./controller/data.json").done(function(t){console.log(t),e.config.resources=t,e.getEvents()})},Dashboard.prototype.getEvents=function(){var e=this;$.get("./controller/eventos.json").done(function(t){e.config.events=t,e.createCheduler()})},Dashboard.prototype.createCheduler=function(){var e=this.config;this.Scheduler=$("#dp").daypilotScheduler(e),this.createNavigation()},Dashboard.prototype.changeCellScale=function(e,t){var a=e,o=t.data("type");"Day"==o?(a.config.timeHeaders=[{groupBy:"Month",format:"MMMM/yy"},{groupBy:"Day",format:"d"}],a.config.scale=o,a.config.cellWidth=30):"Week"==o?(a.config.timeHeaders=[{groupBy:"Month",format:"MMMM/yy"},{groupBy:"Week"}],a.config.scale=o,a.config.cellWidth=30):(a.config.timeHeaders=[{groupBy:"Month",format:"M/yy"}],a.config.scale=o,a.config.cellWidth=60),a.updateScheduler()},Dashboard.prototype.createNavigation=function(){var e=new DayPilot.Navigator("nav");_this=this,e.selectMode="week",e.onTimeRangeSelected=function(e){_this.Scheduler.startDate=e.start,_this.Scheduler.update()},e.init()},Dashboard.prototype.updateScheduler=function(){this.cleanScheduler(),this.createCheduler()},Dashboard.prototype.cleanScheduler=function(){this.Scheduler.dispose()},Dashboard.prototype.updateDate=function(){var e=$("#fecha_inicio").val(),t=$("#fecha_fin").val();this.Scheduler.startDate=e,this.Scheduler.update()};