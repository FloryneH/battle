<?php

    class Database
    {
        private static $instance;

        private $connection;
        private $servername = 'localhost';
        private $username = 'root';
        private $dbname = 'Battle';

        private function __construct()
        {
            try {
                $this->connection = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo 'Connexion réussie';
            } catch (PDOException $e) {
                echo "Erreur : " . $e->getMessage();
            }
        }

        public static function getInstance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function getConnection()
        {
            return $this->connection;
        }
    }

    function getConnection()
    {
        $db = Database::getInstance();
        return $db->getConnection();
    }

    function getPlayer($con, string $name): bool|array
    {
        $sth = $con->prepare("SELECT * FROM personnages WHERE name = :playerName");
        $sth->execute(array(':playerName' => $name));
        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    function insertPlayers($connection): void
    {
        $player = $_POST['player'];
        $adversaire = $_POST['adversaire'];

        $existingPlayer = getPlayer($connection, $player["name"]);
        if (!$existingPlayer) {
            $player['created_at'] = date("Y-m-d");

            $sth = $connection->prepare("
                    INSERT INTO 
                    personnages(name, created_at, mana, attaque, initial_life)
                    VALUES (:name, :created_at, :mana, :attaque, :sante)
                ");
            $sth->execute($player);
            $player["id"] = $connection->lastInsertId();
            $player["initial_life"] = $player['sante'];
        } else {
            $player = $existingPlayer;
        }

        $existingAdversaire = getPlayer($connection, $adversaire["name"]);
        if (!$existingAdversaire) {
            $adversaire['created_at'] = date("Y-m-d");
            $sth = $connection->prepare("
                    INSERT INTO personnages (name, created_at, mana, attaque, initial_life)
                    VALUES (:name, :created_at, :mana, :attaque, :sante)
                ");
            $sth->execute($adversaire);
            $adversaire["id"] = $connection->lastInsertId();
            $adversaire["initial_life"] = $adversaire['sante'];
        } else {
            $adversaire = $existingAdversaire;
        }

        setInfoInSession($player, $adversaire, null);
    }

    function getInfoInSession(): array
    {
        $player = $_SESSION["player"] ?? null;
        $adversaire = $_SESSION["adversaire"] ?? null;
        $recap = $_SESSION["recap"] ?? null;
        return [$player, $adversaire, $recap];
    }

    function setInfoInSession(?array $player, ?array $adversaire, ?array $recap): void
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

    function insertCombat($connection, $winner) {
        list($player, $adversaire, $recap) = getInfoInSession();
        $logCombat = json_encode($recap);

        $sth = $connection->prepare("INSERT INTO `combats` (id_personnage1, id_personnage2, id_winner, log_combat) VALUES (:idJoueur, :idAdversaire, :id_winner, :log_combat)");
        $sth->execute(array(
            ':idJoueur' => $player["id"],
            ':idAdversaire' => $adversaire["id"],
            ':id_winner' => (int)$winner,
            ':log_combat' => $logCombat,
        ));
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
        $adversaire['initial_life'] -= $player['attaque'];

        if ($player['initial_life'] <= 0 || $adversaire['initial_life'] <= 0) {
            $recap[] = $player['name'] . " à tué " . $adversaire['name'] . ".";
            setInfoInSession($player, $adversaire, $recap);
            header('Location: resultats.php');
        }

        $recap[] = $player['name'] . " a attaqué " . $adversaire['name'] . ". " . $adversaire['name'] . " a perdu " . $player['attaque'] . "PV.";

        setInfoInSession($player, $adversaire, $recap);
        adversaireAction();
    }

    function soin()
    {
        list($player, $adversaire, $recap) = getInfoInSession();


        $player['soins_disabled'] = false;

        if ($player['initial_life'] >= 100 || $player['mana'] <= 0) {
            $player['soins_disabled'] = true;
        } else {
            $player['soins_disabled'] = false;
            $player['mana'] -= 20;
            $player['initial_life'] += 10;
            $recap[] = $player['name'] . " à utilisé Soins il à récupéré 10PV";
        }

        setInfoInSession($player, $adversaire, $recap);
        adversaireAction();
    }

    function adversaireAction()
    {
        list($player, $adversaire, $recap) = getInfoInSession();
        $action = rand(0, 1);

        if ($action === 0) {
            $player['initial_life'] -= $adversaire['attaque'];
            $recap[] = $adversaire['name'] . " a attaqué " . $player['name'] . ". " . $player['name'] . " a perdu " . $adversaire['attaque'] . "PV.";

            if ($player['initial_life'] <= 0 || $adversaire['initial_life'] <= 0) {
                $recap[] = $adversaire['name'] . " à tué " . $player['name'] . ".";
                header('Location: resultats.php');
                exit();
            }
        } else {
            if ($adversaire['initial_life'] >= 100 || $adversaire['mana'] <= 0) {
            } else {
                $adversaire['mana'] -= 20;
                $adversaire['initial_life'] += 10;
                $recap[] = $adversaire['name'] . " à utilisé Soins il à récupéré 10PV.";
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

    function afficherVainqueur()
    {
        list($player, $adversaire) = getInfoInSession();
        $winner = null;
        if ($player['initial_life'] <= 0 && $adversaire['initial_life'] <= 0) {
            echo "<p>Match nul ! Les deux combattants sont à terre.</p>";
            $winner = null;
        } elseif ($player['initial_life'] <= 0) {
            echo "<p>" . $adversaire['name'] . " est le vainqueur !</p>";
            $winner = $adversaire["id"];
        } elseif ($adversaire['initial_life'] <= 0) {
            echo "<p>" . $player['name'] . " est le vainqueur !</p>";
            $winner = $player["id"];
        }
        return $winner;
    }
?>
