<?php
    require_once __DIR__ . '/vendor/autoload.php';
    require 'fonction.php';

    // Démarre une nouvelle session ou restaure une session existante
    session_start();
    DataBase();

    // Gestion du formulaire de création de personnage
    if ($_SERVER["REQUEST_METHOD"] === 'POST' && isset($_POST["fight"])) {
        list($formErrors, $player, $adversaire) = checkErrorsForm();
        if (empty($formErrors)) {
            setInfoInSession($player, $adversaire, $recap);
            header('Location: match.php');
        }
    }
    list($player, $adversaire) = getInfoInSession();
?>

<html lang="fr">
<head>
    <title>Battle</title>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
            integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V"
            crossorigin="anonymous">
    </script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <link rel="stylesheet" href="public/bootstrap.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <div class="container">
        <h1 class="animate__animated animate__rubberBand">Battle</h1>

        <div id="prematch">
            <form id='formFight' action="index.php" method="post">

                <div>
                    Joueur <br>
                    <div class="errors">
                        <ul>
                            <?php foreach ($formErrors["player"] ?? [] as $error) { ?>
                                <li class="text-danger"><?php echo $error ?></li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="row">

                        <div class="col-6">
                            <label class="form-label">Name</label>
                            <input required 
                                   type="text" 
                                   class="form-control" 
                                   name="player[name]"
                                   value="<?php echo $_POST["player"]["name"] ?? "" ?>">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Attaque</label>
                            <input required 
                                   type="number" 
                                   class="form-control <?php echo isset($formErrors["player"]["attaque"]) ? "is-invalid" : "" ?>" 
                                   name="player[attaque]"
                                   value="<?php echo $_POST["player"]["attaque"] ?? "100" ?>">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Mana</label>
                            <input required 
                                   type="number" 
                                   class="form-control <?php echo isset($formErrors["player"]["mana"]) ? "is-invalid" : "" ?>" 
                                   name="player[mana]"
                                   value="<?php echo $_POST["player"]["mana"] ?? "100" ?>">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Santé</label>
                            <input required 
                                   type="number" 
                                   class="form-control <?php echo isset($formErrors["player"]["sante"]) ? "is-invalid" : "" ?>" 
                                   name="player[sante]"
                                   value="<?php echo $_POST["player"]["sante"] ?? "100" ?>">
                        </div>

                    </div>
                </div>

                <hr>

                <div>
                    Adversaire <br>
                    <div class="errors">
                        <ul>
                            <?php foreach ($formErrors["adversaire"] ?? [] as $error) { ?>
                                <li class="text-danger"><?php echo $error ?></li>
                            <?php } ?>
                        </ul>
                    </div>

                    <div class="row">

                        <div class="col-6">
                            <label class="form-label">Name</label>
                            <input required 
                                   type="text" 
                                   class="form-control" 
                                   name="adversaire[name]"
                                   value="<?php echo $_POST["adversaire"]["name"] ?? "" ?>">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Attaque</label>
                            <input required 
                                   type="number" 
                                   class="form-control <?php echo isset($formErrors["adversaire"]["attaque"]) ? "is-invalid" : "" ?>" 
                                   name="adversaire[attaque]"
                                   value="<?php echo $_POST["adversaire"]["attaque"] ?? "100" ?>">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Mana</label>
                            <input required 
                                   type="number" 
                                   class="form-control <?php echo isset($formErrors["adversaire"]["mana"]) ? "is-invalid" : "" ?>" 
                                   name="adversaire[mana]"
                                   value="<?php echo $_POST["adversaire"]["mana"] ?? "100" ?>">
                        </div>

                        <div class="col-6">
                            <label class="form-label">Santé</label>
                            <input required 
                                   type="number" 
                                   class="form-control <?php echo isset($formErrors["adversaire"]["sante"]) ? "is-invalid" : "" ?>" 
                                   name="adversaire[sante]"
                                   value="<?php echo $_POST["adversaire"]["sante"] ?? "100" ?>">
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
