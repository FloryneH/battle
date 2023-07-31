<html lang="fr">
<head>
    <title>Battle</title>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
            integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V"
            crossorigin="anonymous">
    </script>
    <script src="index.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="public/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<?php
    session_start();

    if (!isset($_SESSION['player']) || !isset($_SESSION['adversaire'])) {
        // Rediriger vers la page d'accueil si les données des joueurs ne sont pas disponibles
        header('Location: index.php');
        exit;
    }

    $player = $_SESSION['player'];
    $adversaire = $_SESSION['adversaire'];

?>

<body>
    <div id="match" class = "row gx-5">
        <h2>Match</h2>
        <div class="col-6 ">
            <div class="position-relative float-end">
                <img id="player"
                    src="https://api.dicebear.com/6.x/lorelei/svg?flip=false&seed=test"
                    alt="Avatar"
                    class="avatar float-end">
                
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo $player['sante']; ?>
                </span>

                <ul>
                    <li>Name : <?php echo $player['name']; ?> </li>
                    <li>Attaque : <?php echo $player['attaque']; ?> </li>
                    <li>Mana :  <?php echo $player['mana']; ?> </li>
                </ul>

            </div>
        </div>
        
        <div class="col-6" id="adversaire">
            <div class="position-relative float-start">
                <img src="https://api.dicebear.com/6.x/lorelei/svg?flip=true&seed=test2"
                    alt="Avatar"
                    class="avatar">

                <span class="position-absolute top-0 start-0 translate-middle badge rounded-pill bg-danger">
                    <?php echo $adversaire['sante']; ?>
                </span>

                <ul>
                    <li>Name : <?php echo $adversaire['name']; ?> </li>
                    <li>Attaque : <?php echo $adversaire['attaque']; ?> </li>
                    <li>Mana : <?php echo $adversaire['mana']; ?> </li>
                </ul>

            </div>
        </div>

        <div id="combats">
            <h2>Combat</h2>

            <ul>
                <li>
                    <i class="fa-solid fa-khanda p-1"></i> test
                </li>
            </ul>

            <form id='actionForm' action="index.php" method="post">

                <div class="d-flex justify-content-center">
                    <input id="attaque" name="attaque" type="submit" value="Attaquer">
                    <input name="soin" type="submit" value="Se soigner">
                </div>

                <div class="d-flex justify-content-center">
                    <input id="restart" name="restart" type="submit" value="Stopper le combat">
                </div>

            </form>
        </div>
    </div>

</body>

</html>