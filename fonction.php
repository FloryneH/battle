<?php

class Database
{
    // Variable statique pour stocker l'instance unique de la classe
    private static $instance;

    // Propriétés de la connexion à la base de données
    private $connection;
    private $servername = 'localhost';
    private $username = 'root';
    private $dbname = 'Battle';

    // Constructeur privé pour empêcher l'instanciation directe depuis l'extérieur
    private function __construct()
    {
        try {
            $this->connection = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username);
            //On définit le mode d'erreur de PDO sur Exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo 'Connexion réussie';
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }

    // Méthode statique pour récupérer l'instance unique de la classe
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Méthode pour récupérer la connexion à la base de données
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
    // Récupérer les données du formulaire
    $player = $_POST['player'];
    $adversaire = $_POST['adversaire'];

    // Vérifier si le joueur existe déjà dans la base de données
    $existingPlayer = getPlayer($connection, $player["name"]);
    if (!$existingPlayer) {
        // Sinon, insérer un nouveau joueur dans la base de données
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

    // Vérifier si l'adversaire existe déjà dans la base de données
    $existingAdversaire = getPlayer($connection, $adversaire["name"]);
    if (!$existingAdversaire) {
        // Sinon, insérer un nouvel adversaire dans la base de données
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

    // Mettre à jour les données dans la session avec les valeurs associées
    setInfoInSession($player, $adversaire, null);
}

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

    $adversaire['initial_life'] -= $player['attaque'];

    if ($player['initial_life'] <= 0 || $adversaire['initial_life'] <= 0) {
        $recap .= $player['name'] . " a attaqué " . $adversaire['name'] . ". " . $adversaire['name'] . " a perdu " . $player['attaque'] . "PV. <br>";
        setInfoInSession($player, $adversaire, $recap);
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

    if ($player['initial_life'] >= 100 || $player['mana'] <= 0) {
        $player['soins_disabled'] = true;
    } else {
        $player['soins_disabled'] = false;
        $player['mana'] -= 20;
        $player['initial_life'] += 10;
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
        $player['initial_life'] -= $adversaire['attaque'];
        $recap .= $adversaire['name'] . " a attaqué " . $player['name'] . ". " . $player['name'] . " a perdu " . $adversaire['attaque'] . "PV. <br>";
        if ($player['initial_life'] <= 0 || $adversaire['initial_life'] <= 0) {
            header('Location: resultats.php');
            exit();
        }
    } else {
        if ($adversaire['initial_life'] >= 100 || $adversaire['mana'] <= 0) {
        } else {
            $adversaire['mana'] -= 20;
            $adversaire['initial_life'] += 10;
            $recap .= $adversaire['name'] . " à utilisé Soins il à récupéré 10PV. <br>";
        }
    }

    setInfoInSession($player, $adversaire, $recap);
}

function recap()
{
    list($player, $adversaire, $recap) = getInfoInSession();

    if (isset($recap)) {
        $lines = explode("<br>", $recap);
        echo "<ul>";
        foreach ($lines as $line) {
            if (!empty($line)) {
                echo "<li>" . $line . "</li>";
            }
        }
        echo "</ul>";
    }
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

    if ($player['initial_life'] <= 0 && $adversaire['initial_life'] <= 0) {
        echo "<p>Match nul ! Les deux combattants sont à terre.</p>";
    } elseif ($player['initial_life'] <= 0) {
        echo "<p>" . $adversaire['name'] . " est le vainqueur !</p>";
    } elseif ($adversaire['initial_life'] <= 0) {
        echo "<p>" . $player['name'] . " est le vainqueur !</p>";
    }
}
