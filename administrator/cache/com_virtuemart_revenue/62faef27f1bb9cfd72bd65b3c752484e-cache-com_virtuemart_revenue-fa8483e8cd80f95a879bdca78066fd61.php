<?php die("Access Denied"); ?>#x#a:2:{s:6:"output";s:0:"";s:6:"result";a:2:{s:6:"report";a:0:{}s:2:"js";s:1422:"
  google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Jour', 'Commandes', 'Nombre d'article vendu', 'Revenu net'], ['2017-02-05', 0,0,0], ['2017-02-06', 0,0,0], ['2017-02-07', 0,0,0], ['2017-02-08', 0,0,0], ['2017-02-09', 0,0,0], ['2017-02-10', 0,0,0], ['2017-02-11', 0,0,0], ['2017-02-12', 0,0,0], ['2017-02-13', 0,0,0], ['2017-02-14', 0,0,0], ['2017-02-15', 0,0,0], ['2017-02-16', 0,0,0], ['2017-02-17', 0,0,0], ['2017-02-18', 0,0,0], ['2017-02-19', 0,0,0], ['2017-02-20', 0,0,0], ['2017-02-21', 0,0,0], ['2017-02-22', 0,0,0], ['2017-02-23', 0,0,0], ['2017-02-24', 0,0,0], ['2017-02-25', 0,0,0], ['2017-02-26', 0,0,0], ['2017-02-27', 0,0,0], ['2017-02-28', 0,0,0], ['2017-03-01', 0,0,0], ['2017-03-02', 0,0,0], ['2017-03-03', 0,0,0], ['2017-03-04', 0,0,0], ['2017-03-05', 0,0,0]  ]);
        var options = {
          title: 'Rapport pour la période du dimanche 5 février 2017 au lundi 6 mars 2017',
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