<?php
session_start();
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST["email"]) && !empty($_POST["mot_de_passe"])) {
        $email = $_POST["email"];
        $mot_de_passe = $_POST["mot_de_passe"];

        try {
            $db = new PDO("sqlite:../db/ma_base.db");
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($utilisateur && password_verify($mot_de_passe, $utilisateur["mot_de_passe"])) {
                $_SESSION["utilisateur_id"] = $utilisateur["id"];
                $_SESSION["nom"] = $utilisateur["nom"];
                header("Location: dashboard.php");
                exit();
            } else {
                $erreur = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $erreur = "Erreur de base de données : " . $e->getMessage();
        }
    } else {
        $erreur = "Tous les champs sont obligatoires.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="../css/style.css">
       
</head>
<body>
    <div class="formulaire">
    <h2 style="text-decoration: underline;">Connection</h2>
    <?php if ($erreur): ?>
        <p style="color:red"><?= $erreur ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="email">Email</label><br>
        <input type="email" name="email" placeholder=" Email"><br>
        <label for="pwd">Mot de passe</label><br>
        <input type="password" name="mot_de_passe" placeholder=" Mot de passe"><br>
        <input type="submit" value="Se connecter">
        <p>Vous n'avez pas de compte ? <a href="register.php">INSCRIVEZ-VOUS</a></p>
    </form>
    </div>
</body>
</html>