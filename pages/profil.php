<?php
session_start();

if (!isset($_SESSION["utilisateur_id"])) {
    header("Location: login.php");
    exit();
}

$erreur = "";
$message = "";

try {
    $db = new PDO("sqlite:../db/ma_base.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les infos actuelles
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = :id");
    $stmt->bindParam(':id', $_SESSION["utilisateur_id"]);
    $stmt->execute();
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nom'], $_POST['email'])) {
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

    // Gestion de l'upload de photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../images/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $filename = "user_" . $_SESSION["utilisateur_id"] . "." . $ext;
        $target_file = $target_dir . $filename;
        move_uploaded_file($_FILES['photo']['tmp_name'], $target_file);

        // Enregistre le chemin relatif dans la base
        $stmt = $db->prepare("UPDATE utilisateurs SET photo = ? WHERE id = ?");
        $stmt->execute(["images/" . $filename, $_SESSION["utilisateur_id"]]);
        header("Location: profil.php");
        exit();
    }

    $theme = [
        'primary_color' => '#007bff',
        'custom_css' => ''
    ];
    if (!empty($utilisateur['theme_id'])) {
        $stmt = $db->prepare("SELECT * FROM themes WHERE id = ?");
        $stmt->execute([$utilisateur['theme_id']]);
        $theme_db = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($theme_db) $theme = $theme_db;
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
    <?php if (!empty($theme['custom_css'])): ?>
        <?php if (strpos($theme['custom_css'], 'theme_css/') === 0): ?>
            <link rel="stylesheet" href="/<?= htmlspecialchars($theme['custom_css']) ?>">
        <?php else: ?>
            <style><?= $theme['custom_css'] ?></style>
        <?php endif; ?>
    <?php endif; ?>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #eaf4ff 0%, #f8f8f8 100%);
            color: #222831;
            margin: 0;
            padding: 0;
        }
        .container {
            background: #fff;
            max-width: 420px;
            margin: 48px auto 0 auto;
            padding: 32px 28px 28px 28px;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.10);
            text-align: center;
        }
        .profile-img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #007bff;
            margin-bottom: 18px;
            background: #fff;
            box-shadow: 0 2px 8px #eaf4ff;
        }
        h2 {
            margin-top: 0;
            font-size: 2em;
            color: #007bff;
            letter-spacing: 1px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 90%;
            padding: 8px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
        }
        input[type="file"] {
            margin: 10px 0;
        }
        input[type="submit"] {
            background: #007bff;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            font-size: 1em;
            margin-top: 10px;
            cursor: pointer;
            transition: background 0.2s;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        p a {
            color: #007bff;
            text-decoration: none;
        }
        p a:hover {
            text-decoration: underline;
        }
        @media (max-width: 600px) {
            .container { padding: 18px 6px; }
            .profile-img { width: 80px; height: 80px; }
            h2 { font-size: 1.3em; }
            input[type="text"], input[type="email"], input[type="password"] { font-size: 0.95em; }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Mon profil</h2>

    <?php if ($erreur): ?>
        <p style="color:red"><?= $erreur ?></p>
    <?php endif; ?>
    <?php if ($message): ?>
        <p style="color:green"><?= $message ?></p>
    <?php endif; ?>

    <?php if (!empty($utilisateur['photo'])): ?>
        <img src="/<?= htmlspecialchars($utilisateur['photo']) ?>" alt="Photo de profil" class="profile-img"><br>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom']) ?>" placeholder="Nom"><br>
        <input type="email" name="email" value="<?= htmlspecialchars($utilisateur['email']) ?>" placeholder="Email"><br>
        <input type="password" name="mot_de_passe" placeholder="Nouveau mot de passe (laisser vide si inchangé)"><br>
        <input type="file" name="photo" accept="image/*"><br>
        <input type="submit" value="Mettre à jour">
    </form>

    <p><a href="dashboard.php">⬅ Retour au dashboard</a></p>
</div>
</body>
</html>
