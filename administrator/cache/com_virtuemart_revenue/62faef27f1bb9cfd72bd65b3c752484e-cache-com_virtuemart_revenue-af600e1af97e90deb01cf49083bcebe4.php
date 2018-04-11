<?php die("Access Denied"); ?>#x#a:2:{s:6:"output";s:0:"";s:6:"result";a:2:{s:6:"report";a:2:{i:0;a:6:{s:9:"intervals";s:10:"2016-11-23";s:10:"created_on";s:10:"2016-11-23";s:20:"order_subtotal_netto";s:11:"11513.30000";s:21:"order_subtotal_brutto";s:11:"11513.30000";s:14:"count_order_id";s:1:"2";s:16:"product_quantity";s:4:"1713";}i:1;a:6:{s:9:"intervals";s:10:"2016-11-21";s:10:"created_on";s:10:"2016-11-21";s:20:"order_subtotal_netto";s:10:"6411.10000";s:21:"order_subtotal_brutto";s:10:"6425.00000";s:14:"count_order_id";s:1:"3";s:16:"product_quantity";s:3:"434";}}s:2:"js";s:1453:"
  google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Jour', 'Commandes', 'Nombre d'article vendu', 'Revenu net'], ['2016-11-20', 0,0,0], ['2016-11-21', 3,434,6411.10000], ['2016-11-22', 0,0,0], ['2016-11-23', 2,1713,11513.30000], ['2016-11-24', 0,0,0], ['2016-11-25', 0,0,0], ['2016-11-26', 0,0,0], ['2016-11-27', 0,0,0], ['2016-11-28', 0,0,0], ['2016-11-29', 0,0,0], ['2016-11-30', 0,0,0], ['2016-12-01', 0,0,0], ['2016-12-02', 0,0,0], ['2016-12-03', 0,0,0], ['2016-12-04', 0,0,0], ['2016-12-05', 0,0,0], ['2016-12-06', 0,0,0], ['2016-12-07', 0,0,0], ['2016-12-08', 0,0,0], ['2016-12-09', 0,0,0], ['2016-12-10', 0,0,0], ['2016-12-11', 0,0,0], ['2016-12-12', 0,0,0], ['2016-12-13', 0,0,0], ['2016-12-14', 0,0,0], ['2016-12-15', 0,0,0], ['2016-12-16', 0,0,0], ['2016-12-17', 0,0,0], ['2016-12-18', 0,0,0]  ]);
        var options = {
          title: 'Rapport pour la période du dimanche 20 novembre 2016 au lundi 19 décembre 2016',
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