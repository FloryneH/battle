<?php  
    
    function getInfoInSession(): array
    {
        $player = $_SESSION["player"] ?? null;
        $adversaire = $_SESSION["adversaire"] ?? null;
        $recap = $_SESSION["recap"] ?? null;
        return [$player, $adversaire, $recap];
    }
    
    function setInfoInSession(?array $player, ?array $adversaire, ?string $recap): void
    {
        $_SESSION["player"] = $player;
        $_SESSION["adversaire"] = $adversaire;
        $_SESSION["recap"] = $recap;
    }
    
    function removeInfoInSession(): void
    {
        unset($_SESSION["player"]);
        unset($_SESSION["adversaire"]);
        unset($_SESSION["recap"]);
    }


    function checkErrorsForm(): array
    {
        $formErrors = [];
        $player = $_POST['player'];
        $adversaire = $_POST['adversaire'];
        $player["name"] = trim($player["name"]);
        $player["sante"] = intval($player["sante"]);
        $player["mana"] = intval($player["mana"]);
        $player["attaque"] = intval($player["attaque"]);
        $adversaire["name"] = trim($adversaire["name"]);
        $adversaire["sante"] = intval($adversaire["sante"]);
        $adversaire["mana"] = intval($adversaire["mana"]);
        $adversaire["attaque"] = intval($adversaire["attaque"]);

        $format = '%s %s doit être superieur à %d.';
        if ($player["attaque"] <= 0) {
            $formErrors['player']['attaque'] = sprintf($format, "L'attaque", "du joueur", 0);
        }
        if ($player["mana"] <= 0) {
            $formErrors['player']["mana"] = sprintf($format, "Le mana", "du joueur", 0);
        }
        if ($player["sante"] <= 0) {
            $formErrors['player']["sante"] = sprintf($format, "La santé", "du joueur", 0);
        }

        if ($adversaire["attaque"] <= 0) {
            $formErrors['adversaire']["attaque"] = sprintf($format, "L'attaque", "de l'adversaire", 0);
        }
        if ($adversaire["mana"] <= 0) {
            $formErrors['adversaire']["mana"] = sprintf($format, "Le mana", "de l'adversaire", 0);
        }
        if ($adversaire["sante"] <= 0) {
            $formErrors['adversaire']["sante"] = sprintf($format, "La santé", "de l'adversaire", 0);
        }

        return [$formErrors, $player, $adversaire];
    }

    function attaque()
    {
        list($player, $adversaire, $recap) = getInfoInSession();

        $adversaire['sante'] -= $player['attaque'];
        
        if($player['sante'] <= 0 || $adversaire['sante'] <= 0){
            $recap .= $player['name'] . " a attaqué " . $adversaire['name'] . ". " . $adversaire['name'] . " a perdu " . $player['attaque'] . "PV. <br>";
            header('Location: resultats.php');
        }
        $recap .= $player['name'] . " a attaqué " . $adversaire['name'] . ". " . $adversaire['name'] . " a perdu " . $player['attaque'] . "PV. <br>";

        setInfoInSession($player, $adversaire, $recap);
        adversaireAction();
    }

    function soin()
    {
        list($player, $adversaire, $recap) = getInfoInSession();

        
        $player['soins_disabled'] = false;

        if ($player['sante'] >= 100 || $player['mana'] <= 0) {
            $player['soins_disabled'] = true;
        } else {
            $player['soins_disabled'] = false;
            $player['mana'] -= 20;
            $player['sante'] += 10;
            $recap .= $player['name'] . " à utilisé Soins il à récupéré 10PV. <br>";
        }
       
        setInfoInSession($player, $adversaire, $recap);
        adversaireAction();
    }

    function adversaireAction()
    {
        list($player, $adversaire, $recap) = getInfoInSession();
    
        $action = rand(0, 1);

        if ($action === 0) {
            $player['sante'] -= $adversaire['attaque'];
            $recap .= $adversaire['name'] . " a attaqué " . $player['name'] . ". " . $player['name'] . " a perdu " . $adversaire['attaque'] . "PV. <br>";
            if ($player['sante'] <= 0 || $adversaire['sante'] <= 0) {
                header('Location: resultats.php');
                exit();
            }
        } else {
            if ($adversaire['sante'] >= 100 || $adversaire['mana'] <= 0) {
               
            } else {
                $adversaire['mana'] -= 20;
                $adversaire['sante'] += 10;
                $recap .= $adversaire['name'] . " à utilisé Soins il à récupéré 10PV. <br>";
            }
        }

        setInfoInSession($player, $adversaire, $recap);
    }

    function restart()
    {
        removeInfoInSession();
        session_destroy();
        header('Location: index.php');
    }

?>