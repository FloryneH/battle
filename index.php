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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $playerForm = $_POST['player'];
        $player = [
            'name' => $playerForm['name'],
            'attaque' => $playerForm['attaque'],
            'mana' => $playerForm['mana'],
            'sante' => $playerForm['sante'],
        ];
    
        $adversaireForm = $_POST['adversaire'];
        $adversaire = [
            'name' => $adversaireForm['name'],
            'attaque' => $adversaireForm['attaque'],
            'mana' => $adversaireForm['mana'],
            'sante' => $adversaireForm['sante'],
        ];
    
        $_SESSION['player'] = $player;
        $_SESSION['adversaire'] = $adversaire;
    
        // Rediriger vers la page de combat une fois les données enregistrées
        header('Location: match.php');
        exit;
    }
?>

<body>
    <div class="container">
        <h1 class="animate__animated animate__rubberBand">Battle</h1>

        <div id="prematch">
            <form id='formFight' action="index.php" method="post">

                <div>
                    Joueur <br>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Name</label>
                            <input required type="text" class="form-control" name="player[name]">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Attaque</label>
                            <input required type="number" class="form-control" value="100" name="player[attaque]">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Mana</label>
                            <input required type="number" class="form-control" value="100" name="player[mana]">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Santé</label>
                            <input required type="number" class="form-control" value="100" name="player[sante]">
                        </div>
                    </div>
                </div>

                <hr>

                <div>
                    Adversaire <br>
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">Name</label>
                            <input required type="text" class="form-control" name="adversaire[name]">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Attaque</label>
                            <input required type="number" class="form-control" value="100" name="adversaire[attaque]">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Mana</label>
                            <input required type="number" class="form-control" value="100" name="adversaire[mana]">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Santé</label>
                            <input required type="number" class="form-control" value="100" name="adversaire[sante]">
                        </div>
                    </div>
                </div>

                <div class="row mt-2">
                    <div class="d-flex justify-content-center">
                        <input id="fight" type="submit" value="FIGHT" name="fight">
                    </div>
                </div>
            </form>
        </div>

</body>

</html>
