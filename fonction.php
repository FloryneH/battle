<?php
    function getInfoInSession(): array
    {
        $player = $_SESSION["player"] ?? null;
        $adversaire = $_SESSION["adversaire"] ?? null;
        return [$player, $adversaire];
    }
    
    function setInfoInSession(?array $player, ?array $adversaire): void
    {
        $_SESSION["player"] = $player;
        $_SESSION["adversaire"] = $adversaire;
    }
    
    function removeInfoInSession(): void
    {
        unset($_SESSION["player"]);
        unset($_SESSION["adversaire"]);
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
        list($player, $adversaire) = getInfoInSession();

        $adversaire['sante'] -= $player['attaque'];
        
        if($player['sante'] <= 0 || $adversaire['sante'] <= 0){
            header('Location: resultats.php');
        }

        setInfoInSession($player, $adversaire);
        adversaireAction();
    }

    function adversaireAction()
    {
        list($player, $adversaire) = getInfoInSession();
        
        setInfoInSession($player, $adversaire);
    }

    function soin()
    {
        list($player, $adversaire) = getInfoInSession();

        setInfoInSession($player, $adversaire);
        adversaireAction();
    }

    function restart()
    {
        removeInfoInSession();
        session_destroy();
    }

?>