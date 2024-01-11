<?php
    include("php/session.php");

    $sumanetto = 0;
    $sumabrutto = 0;

    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        if(!(isset($_POST['date-start'])) or !(isset($_POST['date-end']))){
            $data_od = $_SESSION['date-start'];
            $data_do = $_SESSION['date-end'];
        } else {
            $data_od = date('Y-m-d', strtotime($_POST['date-start']));
            $data_do = date('Y-m-d', strtotime($_POST['date-end']));
            $_SESSION['date-start'] = $data_od;
            $_SESSION['date-end'] = $data_do;
        }

        $query2 = "SELECT zamowienia.id, zamowienia.data, SUM(zamowienia.ilosc) AS ilosc, produkty.cena_netto, produkty.vat, grupy_produktow.nazwa 
                    FROM zamowienia RIGHT OUTER JOIN produkty ON zamowienia.id_produkt=produkty.id 
                    RIGHT OUTER JOIN grupy_produktow ON produkty.id_grupa=grupy_produktow.id 
                    WHERE (zamowienia.data BETWEEN '$data_od' AND '$data_do')
                    GROUP BY grupy_produktow.nazwa;";

        $result2 = mysqli_query($db, $query2);

        while ($row = mysqli_fetch_array($result2)){
            $datapoints1[] = array("label"=>$row['nazwa'], "y"=>$row['ilosc']*$row['cena_netto']);
            $datapoints2[] = array("label"=>$row['nazwa'], "y"=>$row['ilosc']*$row['cena_netto']*((100+$row['vat'])/100));
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
	        	text: ""
	        },
	        axisY:{
	        	includeZero: true
	        },
	        legend:{
	        	cursor: "pointer",
	        	verticalAlign: "center",
	        	horizontalAlign: "right",
	        	itemclick: toggleDataSeries
	        },
	        data: [{
	        	type: "column",
	        	name: "Suma z Kwota Netto",
	        	indexLabel: "{y}",
	        	yValueFormatString: "$#0.##",
	        	showInLegend: true,
	        	dataPoints: <?php echo json_encode($datapoints1, JSON_NUMERIC_CHECK); ?>
	        },{
	        	type: "column",
	        	name: "Suma z Kwota Brutto",
	        	indexLabel: "{y}",
	        	yValueFormatString: "$#0.##",
	        	showInLegend: true,
	        	dataPoints: <?php echo json_encode($datapoints2, JSON_NUMERIC_CHECK); ?>
	        }]
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
        <h2>Tabela 1<h2>
        <h2><a href="3.php">Tabela 2</a><h2>


        <form name="frmUser" method="post" action="">
            <input type="date" name="date-start" value="<?php if(isset($data_od)){echo $data_od;}else{echo date('Y-m-d');} ?>" /> <br />
            <input type="date" name="date-end" value="<?php if(isset($data_do)){echo $data_do;}else{echo date('Y-m-d');} ?>" /> <br />

            <input type="submit" name="go" value="Odśwież tabelę">
        </form>

        <table border="1px solid">
            <tr>
                <th>Gruppa</th>
                <th>Dzień</th>
                <th>Kwota Netto</th>
                <th>Kwota Brutto</th>
            </tr>

            <?php
                if (isset($_POST['date-start']) && isset($_POST['date-end'])){
                    $query = "SELECT zamowienia.id, zamowienia.data, SUM(zamowienia.ilosc) AS ilosc, produkty.cena_netto, produkty.vat, grupy_produktow.nazwa 
                    FROM zamowienia RIGHT OUTER JOIN produkty ON zamowienia.id_produkt=produkty.id 
                    RIGHT OUTER JOIN grupy_produktow ON produkty.id_grupa=grupy_produktow.id 
                    WHERE (zamowienia.data BETWEEN '$data_od' AND '$data_do')
                    GROUP BY grupy_produktow.nazwa, zamowienia.data
                    ORDER BY zamowienia.data ASC, grupy_produktow.nazwa DESC;";

                    $result = mysqli_query($db, $query);
                    
                    while ($row = mysqli_fetch_array($result)){
                        $rownetto = $row['ilosc']*$row['cena_netto'];
                        $sumanetto = $sumanetto + $rownetto;
                        $rowbrutto = $rownetto*((100+$row['vat'])/100);
                        $sumabrutto = $sumabrutto + $rowbrutto;
                        $exportTable[] = array("Gruppa"=>$row['nazwa'], "Dzień"=>$row['data'], "Kwota Netto"=>$rownetto, "Kwota Brutto"=>$rowbrutto);
                        echo "<tr>";
                        echo "<td>".$row['nazwa']."</td>";
                        echo "<td>".$row['data']."</td>";
                        echo "<td>".$rownetto."</td>";
                        echo "<td>".$rowbrutto."</td>";
                        echo "</tr>";
                    }
                    $exportTable[] = array("Gruppa"=>'', "Dzień"=>"Summa:", "Kwota Netto"=>$sumanetto, "Kwota Brutto"=>$sumabrutto);

                    if(array_key_exists('download', $_GET)) { 
                        $_SESSION['exportTable'] = $exportTable;
                        $_SESSION['filename'] = 'zd1Table';
                    } 
                    
                }
            ?>

            <tr>
                <th colspan="2">SUMA</th>
                <th><?php echo $sumanetto; ?></th>
                <th><?php echo $sumabrutto; ?></th>
            </tr>
        </table>
        <?php if($_SERVER["REQUEST_METHOD"] == "POST"){ echo '<form name="dwnld" method="GET" action="download.php">
            <input type="submit" name="download" value="download"/>
        </form>'; }?>
        <div id="chartContainer" style="height: 370px; width: 100%;"></div>
        <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    </div>
    
    </body>
</html>