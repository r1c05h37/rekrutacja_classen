<?php
    include("php/session.php");

    $roczniki = array();
    $dane = array();

    if ($_SERVER['REQUEST_METHOD'] == "POST"){
        $query = "SELECT zamowienia.id, EXTRACT(year FROM zamowienia.data) AS rok, SUM(zamowienia.ilosc) AS ilosc, produkty.cena_netto, produkty.vat, grupy_produktow.nazwa 
        FROM zamowienia RIGHT OUTER JOIN produkty ON zamowienia.id_produkt=produkty.id 
        RIGHT OUTER JOIN grupy_produktow ON produkty.id_grupa=grupy_produktow.id 
        GROUP BY grupy_produktow.nazwa, EXTRACT(year FROM zamowienia.data);";

        $result = mysqli_query($db, $query);
        
        while ($row = mysqli_fetch_array($result)){
            $rownetto = $row['ilosc']*$row['cena_netto'];
            $rowbrutto = $rownetto*((100+$row['vat'])/100);
            $nazwa = $row['nazwa'];
            $rok = $row['rok'];
            if(!(in_array($rok, $roczniki))){
                $roczniki[] = $rok;
            }

            $dane[$nazwa][$rok] = array("netto"=>$rownetto, "brutto"=>$rowbrutto);
            $chart[$nazwa][] = array("label"=>$rok, "y"=>$rownetto);
        }
    }
?>

<html>
    <head>

    <title>Some cool title</title>

    <script>
        window.onload = function () {
 
        var chart = new CanvasJS.Chart("chartContainer", {
	        animationEnabled: true,
	        theme: "light2",
	        title:{
	        	text: "Wykres roczny netto"
	        },
            legend:{
		        cursor: "pointer",
		        verticalAlign: "center",
		        horizontalAlign: "right",
		        itemclick: toggleDataSeries
	        },
	        data: [
            <?php foreach($chart as $nazwa => $id){echo "{
	        	type: 'line',
                name: '".$nazwa."',
                showInLegend: true,
	        	dataPoints: ".json_encode($chart[$nazwa], JSON_NUMERIC_CHECK).",
	        },
            ";}?>]
        });
        chart.render();
 
        function toggleDataSeries(e){
	        if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
	        	e.dataSeries.visible = false;
	        }
	        else{
	        	e.dataSeries.visible = true;
	        }
	        chart.render();
        }
 
        }
    </script>

    </head>
    <body>

    <div>

        <h2><a href="1.php">Zmienić hasło</a></h2>
        <h2><a href="2.php">Tabela 1</a><h2>
        <h2>Tabela 3</h2>

        <table border="1px solid">
            <tr><th rowspan="3">Grupa</th><th colspan=<?php echo '"'.(count($roczniki)*2).'"'; ?>>Lata</th></tr>
            
            <?php
            echo "<tr>";
                foreach($roczniki as $rok){
                    echo "<td colspan='2'>".$rok."</td>";
                }
            echo "</tr><tr>";
                foreach($roczniki as $rok){
                    echo "<td>Netto</td><td>Brutto</td>";
                }
            echo "</tr>";
                foreach($dane as $nazwa => $dane){
                    echo "<tr><td>".$nazwa."</td>";

                    foreach($dane as $rok){
                        echo "<td>".$rok['netto']."</td><td>".$rok['brutto']."</td>";
                    }

                    echo "</tr>";
                }
            ?>
        </table>
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    </div>
    
    </body>
</html>