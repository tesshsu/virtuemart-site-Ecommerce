<?php die("Access Denied"); ?>#x#a:2:{s:6:"output";s:0:"";s:6:"result";a:2:{s:6:"report";a:0:{}s:2:"js";s:1426:"
  google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Jour', 'Commandes', 'Nombre d'article vendu', 'Revenu net'], ['2016-12-06', 0,0,0], ['2016-12-07', 0,0,0], ['2016-12-08', 0,0,0], ['2016-12-09', 0,0,0], ['2016-12-10', 0,0,0], ['2016-12-11', 0,0,0], ['2016-12-12', 0,0,0], ['2016-12-13', 0,0,0], ['2016-12-14', 0,0,0], ['2016-12-15', 0,0,0], ['2016-12-16', 0,0,0], ['2016-12-17', 0,0,0], ['2016-12-18', 0,0,0], ['2016-12-19', 0,0,0], ['2016-12-20', 0,0,0], ['2016-12-21', 0,0,0], ['2016-12-22', 0,0,0], ['2016-12-23', 0,0,0], ['2016-12-24', 0,0,0], ['2016-12-25', 0,0,0], ['2016-12-26', 0,0,0], ['2016-12-27', 0,0,0], ['2016-12-28', 0,0,0], ['2016-12-29', 0,0,0], ['2016-12-30', 0,0,0], ['2016-12-31', 0,0,0], ['2017-01-01', 0,0,0], ['2017-01-02', 0,0,0], ['2017-01-03', 0,0,0]  ]);
        var options = {
          title: 'Rapport pour la période du mardi 6 décembre 2016 au mercredi 4 janvier 2017',
            series: {0: {targetAxisIndex:0},
                   1:{targetAxisIndex:0},
                   2:{targetAxisIndex:1},
                  },
                  colors: ["#00A1DF", "#A4CA37","#E66A0A"],
        };

        var chart = new google.visualization.LineChart(document.getElementById('vm_stats_chart'));

        chart.draw(data, options);
      }
";}}