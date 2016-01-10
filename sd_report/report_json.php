<?php
$jsonTable_load = file_get_contents("load.json");
$jsonTable_network = file_get_contents("network.json");
$jsonTable_diskusage = file_get_contents("diskusage.json");
$jsonTable_cpu = file_get_contents("cpu.json");
$jsonTable_memory = file_get_contents("memory.json");
?>
<html>
  <head>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
    <script type="text/javascript">

    google.load('visualization', '1', {'packages':['corechart']});
    google.setOnLoadCallback(drawChart);

    function drawChart() {

      var data = new google.visualization.DataTable(<?=$jsonTable_load?>);
      var chart = new google.visualization.LineChart(document.getElementById('chart_div_load'));
      var options = {
        title: 'Load average',
        width: 1200,
        height: 300,
      };

      chart.draw(data, options);

      var data = new google.visualization.DataTable(<?=$jsonTable_network?>);
      var chart = new google.visualization.LineChart(document.getElementById('chart_div_network'));
      var options = {
        title: 'Network traffic',
        width: 1200,
        height: 300,
      };

      chart.draw(data, options);

      var data = new google.visualization.DataTable(<?=$jsonTable_diskusage?>);
      var chart = new google.visualization.LineChart(document.getElementById('chart_div_diskusage'));
      var options = {
        title: 'Disk usage /',
        width: 1200,
        height: 300,
      };

      chart.draw(data, options);

      var data = new google.visualization.DataTable(<?=$jsonTable_cpu?>);
      var chart = new google.visualization.LineChart(document.getElementById('chart_div_cpu'));
      var options = {
        title: 'CPU (ALL) usage',
        width: 1200,
        height: 450,
      };

      chart.draw(data, options);

      var data = new google.visualization.DataTable(<?=$jsonTable_memory?>);
      var chart = new google.visualization.LineChart(document.getElementById('chart_div_memory'));
      var options = {
        title: 'Memory usage',
        width: 1200,
        height: 450,
      };

      chart.draw(data, options);
    }
    </script>
  </head>

  <body>
    <!--this is the div that will hold the pie chart-->
    <div id="chart_div_load"></div>
    <div id="chart_div_network"></div>
    <div id="chart_div_diskusage"></div>
    <div id="chart_div_cpu"></div>
    <div id="chart_div_memory"></div>
  </body>
</html>

