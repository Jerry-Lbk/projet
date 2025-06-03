<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CREON</title>
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
    <form action="traitement.php" method="get">
        <label for="nom">entrez votre nom</label><br>
        <input type="text" name="nom" id="nom" required><br>
        <label for="prenom">entrez votre prenom</label><br>
        <input type="text" name="prenom" id="prenom" required><br>
        <label for="email">entrez votre email</label><br>
        <input type="email" name="" id="email" required><br>
        <label for="text">description</label><br>
        <textarea name="" id="text" rows="15" required></textarea><br>
        <button type="submit" >soumettre</button><br>
    </form>
    
</body>

</html>