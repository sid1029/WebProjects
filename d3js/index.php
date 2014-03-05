<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link rel="shortcut icon" href="favicon.ico" />

    <title>CPU Load</title>
    <style type="text/css">
      .y-axis path,
      .x-axis path,
      .axis line {
        fill: none;
        stroke: #000;
        shape-rendering: crispEdges;
      }

    </style>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.0/css/bootstrap.min.css" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.6.0/underscore-min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/d3/3.4.1/d3.js"></script>
  </head>
  <body>
    <div class="container" style="width:65%">
      <h1>CPU Load</h1>
      <div class="row">
        <div class="chart">
      </div>
      <div class="row" style="margin-top:30px">
        <div class="col-md-6">
          <div class="panel panel-info">
            <div class="panel-heading">
              <h3 class="panel-title">Stats</h3>
            </div>
            <div id="stats" class="panel-body">
              Average CPU load for last 2 minutes : <span id="avg-load" /><br />
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="panel panel-danger">
            <div class="panel-heading">
              <h3 class="panel-title">
              <span class="pull-left">Alerts</span>
              <div class="text-right">
                <button type="button" class="btn btn-primary" onclick="ClearAlerts();">Clear Alerts</button>
              </div>
              </h3>
            </div>
            <div id="alert-feed" class="panel-body">
              
            </div>
          </div>
        </div>
      </div>
    </div>
    <script type="text/template" id="alert-template">
      <% if (dataobj.alertType === "danger") { %>
        <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        High load alert : load = <strong><%= dataobj.value %></strong>, triggered at <strong><%= dataobj.timestamp %></strong>
      <% } else if (dataobj.alertType === "safe") { %>
        <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        Load returned to normal : load = <strong><%= dataobj.value %></strong>, at <strong><%= dataobj.timestamp %></strong>
      <% } %>
      </div>
    </script>
    <script type="text/javascript">
      // D3 chart manager
      var w = 20, h = 250, numSamples = 60;
      var margin = {top: 20, right: 20, bottom: 30, left: 40};
      var chartWidth = numSamples * w + margin.left;
      var chartHeight = h + margin.top + margin.bottom;
      var animInterval = 1000;

      function next() {
        return {
          timestamp: ++t,
          value: v = Math.random()
        };
      }

      var now = Math.round((new Date()).getTime() / 1000);
      var timeFormat = d3.time.format("%c");
      var floatFormat = d3.format(".2f");

      var t = now, v = 70, data = d3.range(numSamples).map(next);
      
      var x, y, barX, barY, colorScale, chart, xAxis, yAxis, axis, chartInited = false;

      function initChart()
      {
        x = d3.time.scale()
           .domain([data[0].timestamp * 1000, data[numSamples - 1].timestamp * 1000])
           .range([0, chartWidth]);
        
        barX = d3.time.scale()
           .domain([0,numSamples])
           .range([0, chartWidth]);

        y = d3.scale.linear()
           .domain([1.5, 0])
           .range([h, 0]);

        barY = d3.scale.linear()
           .domain([1.5, 0])
           .range([0, h]);

        colorScale = d3.scale.linear()
           .domain([0, 1])
           .interpolate(d3.interpolateRgb)
           .range([d3.rgb(159, 224, 237), d3.rgb(49, 102, 112)]);

        chart = d3.select(".chart").append("svg")
           .attr("class", "chart")
           .attr("width", chartWidth - 2)
           .attr("height", chartHeight)
          .append("g")
            .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

        chart.selectAll("rect")
           .data(data)
         .enter().append("rect")
           .attr("x", function(d, i) { return barX(i) - .5; })
           .attr("y", function(d) { return h - y(d.value) - .5; })
           .attr("width", w)
           .attr("class", "cpuval")
           .attr("height", function(d) { return y(d.value); })
           .attr("fill", function(d) { return colorScale(d.value); });

        xAxis = d3.svg.axis()
           .scale(x)
           .orient("bottom")
           .tickFormat(d3.time.format("%X"));

        yAxis = d3.svg.axis()
            .scale(barY)
            .orient("left");

        // Draw the X axis.
        axis = chart.append("g")
            .attr("class", "x-axis")
            .attr("transform", "translate(0," + h + ")")
            .call(xAxis);

        // Draw the Y axis.
        chart.append("g")
            .attr("class", "y-axis")
            .call(yAxis)
          .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", 6)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("CPU Load");
      }

      function redraw()
      {

        var rect = chart.selectAll(".cpuval")
            .data(data, function(d) { return d.timestamp; });
      
        rect.enter().insert("rect", "g")
            .attr("x", function(d, i) { return barX(i + 1) - .5; })
            .attr("y", function(d) { return h - y(d.value) - .5; })
            .attr("width", w)
            .attr("class", "cpuval")
            .attr("fill", function(d) { return d.value > 1.0 ? "#FF0000" : colorScale(d.value); })
            .attr("height", function(d) { return y(d.value); })
          .transition()
            .duration(animInterval)
            .attr("x", function(d, i) { return barX(i) - .5; });
      
        rect.transition()
            .duration(animInterval)
            .attr("x", function(d, i) { return barX(i) - .5; });
      
        rect.exit().transition()
            .duration(animInterval)
            .attr("x", function(d, i) { return barX(i - 1) - .5; })
            .style("opacity", 0)
            .remove();

        x.domain([data[0].timestamp * 1000, data[numSamples - 1].timestamp * 1000]);

        axis.transition()
            .duration(animInterval)
            .call(xAxis);
      }
    </script>

    <script type="text/javascript">

      $.ajaxPrefilter( function( options, originalOptions, jqXHR ) {
        options.url = "http://localhost/dd" + options.url;
      });

      var lastAlert = 0, currSum = 0, dangerAlerted = false;

      function DataUpdate()
      {
        $.get( "/load.php",
          function( resp ) // Success callback
          {
            if (!chartInited)
              {
                initChart();
                chartInited = true;

                for (var i = data.length - 1; i > data.length - 12; --i)
                {
                  currSum += data[i].value;
                }
              }
              data.shift();
              data.push({
                value: resp.value,
                timestamp: resp.timestamp
              });
              redraw();
              AnalyzeData(data);
              
          }, "json" ).fail(function() {
            data.shift();
            data.push({
              value: 0,
              timestamp: Math.round((new Date()).getTime() / 1000)
            });
            redraw();
            AnalyzeData(data);
        });
      }

      function AnalyzeData(data, seed)
      {
        // Need to analyze the last 2 minutes i.e. 12 samples.
        // Check if we've already alerted sometime in the last 12 samples.
        
        var len = data.length - 1;
        if (len < 12)
          return;

        currSum = currSum - data[len - 12].value + data[len].value;
        var avg = currSum/12;
        $("#avg-load").html(floatFormat(avg));

        if (lastAlert == 0)
        {
          if (avg > 1.0)
          {
            AddAlert({
              value: avg,
              timestamp: data[len].timestamp,
              alertType: "danger"
            });
            lastAlert = 12;
            dangerAlerted = true;
          }
        }
        else
        {
          --lastAlert;
          if (dangerAlerted && avg < 1.0)
          {
            AddAlert({
              value: avg,
              timestamp: data[len].timestamp,
              alertType: "safe"
            });
            dangerAlerted = false;
          }
        }
      }

      function AddAlert(data)
      {
        data.value = floatFormat(data.value);
        data.timestamp = timeFormat(new Date(data.timestamp * 1000));
        var temp = _.template($('#alert-template').html(), {dataobj: data});
        $("#alert-feed").append(temp);
      }

      function ClearAlerts()
      {
        $("#alert-feed div").each(function(i){
          $(this).delay(200*i).fadeOut(1000);

          $(this).animate({
              "opacity" : "0",
              },{
              "complete" : function() {
                $(this).remove();
              }
          });
        });
      }

      $('document').ready(function () {
        setInterval(DataUpdate, 1500);
      });
    </script>
  </body>
</html>
