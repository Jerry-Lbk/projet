<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="./favicon.png" />
  <title>Accueil</title>
  <style>
    .visit {
      width: 250px;
      height: 250px;
      margin: 20px;
      object-fit: cover;
      display: inline-block;
      /* Align sections horizontally */
      transition: transform 0.3s ease;
      /* Added transition for scaling */
    }

    .visit:hover {
      transform: scale(1.1);
      /* Adjusted scale for hover effect */
    }

    a {
      text-decoration: none;
      color: black;
    }

    a:hover {
      color: blue;
    }

    body {
      font-family: sans-serif;
      margin: 0;
      padding: 20px;
      background: linear-gradient(45deg,
          #f1f1f1 0%,
          #e6e6e6 50%,
          #f1f1f1 100%);
      text-align: center;
    }

    h1 {
      color: #333;
    }

    p {
      color: #666;
      margin-bottom: 30px;
    }

    img {
      width: 180px;
      /* Increased size */
      height: auto;
      border: 2px solid #ddd;
      border-radius: 8px;
      transition: transform 0.3s ease, border-color 0.3s ease;
      /* Adjusted transition */
    }

    img:hover {
      transform: scale(1.1);
      /* Adjusted scale for hover effect */
      border-color: #007bff;
    }
  </style>
</head>

<body>
  <h1>Bienvenue sur CREON</h1>
  <p>Quel action souhaitez-vous realiser ?</p>
  <section class="visit">
    <a href="liste.php" target="_blank">
      <img src="../favicon.png" alt="visitez les portfolio" />
    </a>
    <h5>visite</h5>
  </section>

  <section class="visit">
    <a href="register.php" target="_blank">
      <img src="../creer.png" alt="cree votre propre portfolio" />
    </a>
    <h5>creer votre portfolio</h5>
  </section>
</body>

</html>