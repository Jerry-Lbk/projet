<?php
session_start();

if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: login.php");
    exit();
}

$erreur = "";
$message = "";

try {
    $db = new PDO("sqlite:db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les infos actuelles
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION["utilisateur_id"]);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nouveau_nom = $_POST["nom"];
        $nouvel_email = $_POST["email"];
        $nouveau_mdp = $_POST["mot_de_passe"];

        if (!empty($nouveau_nom) && !empty($nouvel_email)) {
            if (!empty($nouveau_mdp)) {
                $nouveau_mdp = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE utilisateurs SET nom = :nom, email = :email, mot_de_passe = :mdp WHERE id = :id");
                $stmt->bindParam(':mdp', $nouveau_mdp);
            } else {
                $stmt = $db->prepare("UPDATE utilisateurs SET nom = :nom, email = :email WHERE id = :id");
            }

            $stmt->bindParam(':nom', $nouveau_nom);
            $stmt->bindParam(':email', $nouvel_email);
            $stmt->bindParam(':id', $_SESSION["utilisateur_id"]);
            $stmt->execute();

            $_SESSION["nom"] = $nouveau_nom;
            $message = "✅ Profil mis à jour avec succès.";
        } else {
            $erreur = "Tous les champs sauf le mot de passe sont obligatoires.";
        }
    }
} catch (PDOException $e) {
    $erreur = "Erreur de base de données : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>Mon profil</h2>

    <?php if ($erreur): ?>
        <p style="color:red"><?= $erreur ?></p>
    <?php endif; ?>
    <?php if ($message): ?>
        <p style="color:green"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST" enctype="">
        <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" placeholder="Nom"><br>
        <input type="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" placeholder="Email"><br>
        <input type="password" name="mot_de_passe" placeholder="Nouveau mot de passe (laisser vide si inchangé)"><br>
        <input type="submit" value="Mettre à jour">
    </form>

    <p><a href="dashboard.php">⬅ Retour au dashboard</a></p>
</body>
</html>
