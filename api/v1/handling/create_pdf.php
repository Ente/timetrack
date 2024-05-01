<html>
            <body style="text-align:center; font-family: Arial;">
                <h1>Arbeitszeiten von <b><?php echo $_GET["mitarbeiter"]; ?></b></h1>
                <style>
.box {
    
    width: auto;
    max-width: 800px;
    height: auto;
    border: 5px solid;
    padding: auto;
    margin: auto;
    border-radius: 5px;
    margin-left: auto;
    margin-right: auto;
    opacity: 1;
    border-color: rgba(255, 255, 255, 0.64);
    transition: all 0.5s;
}
                </style>
                <div class="box">
                <table style="width:100%;border:solid;">
                    <tr>
                        <th>Tag</th>
                        <th>Uhrzeit Anfang</th>
                        <th>Uhrzeit Ende</th>
                        <th>Ort</th>
                    </tr>



<?php
#ini_set("display_errors", 1);
require "../inc/db.inc.php";

$mitarbeiter = $_GET["mitarbeiter"];

$sql = "SELECT * FROM arbeitszeiten WHERE name = '$mitarbeiter' ORDER BY schicht_tag AND id DESC;";

$res = mysqli_query($conn, $sql);
if(mysqli_error($conn)){
    die(mysqli_error($conn));
}

if(mysqli_num_rows($res) > 0) {
    while($row = mysqli_fetch_assoc($res)) {
        $rnw = $row["name"];
        $raw = strftime("%d.%m.%Y", strtotime($row["schicht_tag"]));
        $rew = $row["schicht_anfang"];
        $rol = $row["schicht_ende"];
        $ral = $row["ort"];
        $data = <<< DATA

                    <tr>
                        <td>{$raw}</td>
                        <td>{$rew}</td>
                        <td>{$rol}</td>
                        <td>{$ral}</td>
                    </tr>
DATA;
echo $data;
        
    }
}


?>

        </table>
</div>
    </body>
</html>