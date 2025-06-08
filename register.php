<?php
$db=new PDO (' sqlite:db/ma_base.db') ;
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($_SERVER['REQUEST_METHOD']== 'POST'){
    $nom=$_POST['nom'];
    $email=$_POST['email'];
    $password=$_POST['password'];
if($nom&&$email&&$password){
$hash=password_hash($password,PASSWORD_DEFAULT);
$stmt = $db->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES (?, ?, ?)");
try{
$stmt->execute([$nom, $email, $hash]);
header("Location: login.php");
exit;
}catch(PDOException $e){
    $erreur="Email déjà utilisé";
}
}
else {
    $erreur= "tout les champs sont obligatoires";
}
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>inscription</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.html">Accueil</a></li>
                <li><a href="contact.html">Contact</a></li>
                <li><a href="FAQ.html">FAQ</a></li>
            </ul>
        </nav>
    </header>
    <h1>JCVDEMS</h1>
    <form action="traitement.php" method="post">
        <label for="nom">entrez votre nom</label><br>
        <input type="text" name="nom" id="nom" required><br>
        <label for="prenom">entrez votre prenom</label><br>
        <input type="text" name="prenom" id="prenom" required><br>
        <label for="email">entrez votre email</label><br>
        <input type="email" name="email" id="email" required><br>
        <label for="pwd">entrez votre mot de passe</label><br>
        <input type="password" name="password" id="pwd">
       
        <button type="submit">soumettre</button><br>
    </form>

</body>

</html>