<?php die("Access Denied"); ?>#x#a:2:{s:6:"output";s:0:"";s:6:"result";a:2:{s:6:"report";a:15:{i:0;a:6:{s:9:"intervals";s:10:"2017-05-04";s:10:"created_on";s:10:"2017-05-04";s:20:"order_subtotal_netto";s:8:"57.52500";s:21:"order_subtotal_brutto";s:8:"69.03000";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:2:"10";}i:1;a:6:{s:9:"intervals";s:10:"2017-05-02";s:10:"created_on";s:10:"2017-05-02";s:20:"order_subtotal_netto";s:10:"6959.73050";s:21:"order_subtotal_brutto";s:10:"6959.73050";s:14:"count_order_id";s:1:"2";s:16:"product_quantity";s:3:"815";}i:2;a:6:{s:9:"intervals";s:10:"2017-05-01";s:10:"created_on";s:10:"2017-05-01";s:20:"order_subtotal_netto";s:10:"1096.53300";s:21:"order_subtotal_brutto";s:10:"1115.95800";s:14:"count_order_id";s:1:"2";s:16:"product_quantity";s:3:"128";}i:3;a:6:{s:9:"intervals";s:10:"2017-04-28";s:10:"created_on";s:10:"2017-04-28";s:20:"order_subtotal_netto";s:9:"469.14750";s:21:"order_subtotal_brutto";s:9:"469.14750";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:2:"45";}i:4;a:6:{s:9:"intervals";s:10:"2017-04-27";s:10:"created_on";s:10:"2017-04-27";s:20:"order_subtotal_netto";s:10:"2929.33200";s:21:"order_subtotal_brutto";s:10:"2946.95930";s:14:"count_order_id";s:1:"2";s:16:"product_quantity";s:3:"399";}i:5;a:6:{s:9:"intervals";s:10:"2017-04-26";s:10:"created_on";s:10:"2017-04-26";s:20:"order_subtotal_netto";s:10:"2935.49400";s:21:"order_subtotal_brutto";s:10:"2935.49400";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:3:"378";}i:6;a:6:{s:9:"intervals";s:10:"2017-04-25";s:10:"created_on";s:10:"2017-04-25";s:20:"order_subtotal_netto";s:10:"5107.20000";s:21:"order_subtotal_brutto";s:10:"6128.64000";s:14:"count_order_id";s:1:"2";s:16:"product_quantity";s:3:"910";}i:7;a:6:{s:9:"intervals";s:10:"2017-04-21";s:10:"created_on";s:10:"2017-04-21";s:20:"order_subtotal_netto";s:10:"8869.83240";s:21:"order_subtotal_brutto";s:10:"8869.83240";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:4:"1131";}i:8;a:6:{s:9:"intervals";s:10:"2017-04-20";s:10:"created_on";s:10:"2017-04-20";s:20:"order_subtotal_netto";s:10:"1001.16400";s:21:"order_subtotal_brutto";s:10:"1001.16400";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:3:"168";}i:9;a:6:{s:9:"intervals";s:10:"2017-04-19";s:10:"created_on";s:10:"2017-04-19";s:20:"order_subtotal_netto";s:10:"1706.97670";s:21:"order_subtotal_brutto";s:10:"1706.97670";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:3:"181";}i:10;a:6:{s:9:"intervals";s:10:"2017-04-18";s:10:"created_on";s:10:"2017-04-18";s:20:"order_subtotal_netto";s:11:"99464.95845";s:21:"order_subtotal_brutto";s:11:"99464.95845";s:14:"count_order_id";s:1:"2";s:16:"product_quantity";s:5:"18275";}i:11;a:6:{s:9:"intervals";s:10:"2017-04-11";s:10:"created_on";s:10:"2017-04-11";s:20:"order_subtotal_netto";s:9:"839.44300";s:21:"order_subtotal_brutto";s:10:"1007.33160";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:2:"65";}i:12;a:6:{s:9:"intervals";s:10:"2017-04-07";s:10:"created_on";s:10:"2017-04-07";s:20:"order_subtotal_netto";s:10:"3024.12000";s:21:"order_subtotal_brutto";s:10:"3258.10000";s:14:"count_order_id";s:1:"3";s:16:"product_quantity";s:3:"257";}i:13;a:6:{s:9:"intervals";s:10:"2017-04-06";s:10:"created_on";s:10:"2017-04-06";s:20:"order_subtotal_netto";s:9:"198.55000";s:21:"order_subtotal_brutto";s:9:"238.26000";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:2:"36";}i:14;a:6:{s:9:"intervals";s:10:"2017-04-05";s:10:"created_on";s:10:"2017-04-05";s:20:"order_subtotal_netto";s:9:"212.40000";s:21:"order_subtotal_brutto";s:9:"254.88000";s:14:"count_order_id";s:1:"1";s:16:"product_quantity";s:2:"13";}}s:2:"js";s:1587:"
  google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Jour', 'Commandes', 'Nombre d'article vendu', 'Revenu net'], ['2017-04-05', 1,13,212.40000], ['2017-04-06', 1,36,198.55000], ['2017-04-07', 3,257,3024.12000], ['2017-04-08', 0,0,0], ['2017-04-09', 0,0,0], ['2017-04-10', 0,0,0], ['2017-04-11', 1,65,839.44300], ['2017-04-12', 0,0,0], ['2017-04-13', 0,0,0], ['2017-04-14', 0,0,0], ['2017-04-15', 0,0,0], ['2017-04-16', 0,0,0], ['2017-04-17', 0,0,0], ['2017-04-18', 2,18275,99464.95845], ['2017-04-19', 1,181,1706.97670], ['2017-04-20', 1,168,1001.16400], ['2017-04-21', 1,1131,8869.83240], ['2017-04-22', 0,0,0], ['2017-04-23', 0,0,0], ['2017-04-24', 0,0,0], ['2017-04-25', 2,910,5107.20000], ['2017-04-26', 1,378,2935.49400], ['2017-04-27', 2,399,2929.33200], ['2017-04-28', 1,45,469.14750], ['2017-04-29', 0,0,0], ['2017-04-30', 0,0,0], ['2017-05-01', 2,128,1096.53300], ['2017-05-02', 2,815,6959.73050], ['2017-05-03', 0,0,0]  ]);
        var options = {
          title: 'Rapport pour la période du mercredi 5 avril 2017 au jeudi 4 mai 2017',
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