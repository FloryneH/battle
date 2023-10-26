<?php
    require_once __DIR__ . '/vendor/autoload.php';
    require 'fonction.php';

    session_start();
    $connection = getConnection();

    $winner = afficherVainqueur();
    insertCombat($connection, $winner);

    if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST["restart"])) {
        restart();
    }
    dump($GLOBALS);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>
<body>
   
    <div id="Resultats">
        <h2>Résultat</h2>
        <form class="d-flex justify-content-center" action="" method="post">
            <input name="restart" type="submit" value="Nouveau combat">
        </form>
    </div>

</body>
</html>