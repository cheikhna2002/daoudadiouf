
<?php
require_once 'fonctions.php';

$projets = [
    [
        'titre'        => 'SenShop',
        'description'  => 'Une boutique en ligne pour artisans sénégalais.',
        'technologies' => ['HTML', 'CSS', 'JavaScript', 'PHP'],
        'image'        => 'images/projets/senshop.png',
    ],
    [
        'titre'        => 'Calculatrice',
        'description'  => 'Calculatrice interactive développée en JavaScript.',
        'technologies' => ['HTML', 'CSS', 'JavaScript'],
        'image'        => 'images/projets/calculatrice.png',
    ],
    [
        'titre'        => 'Gestion d\'École',
        'description'  => 'Application de suivi des notes en Langage C.',
        'technologies' => ['LangageC'],
        'image'        => 'images/projets/ecole.png',
    ],
    [
        'titre'        => 'Automatisation',
        'description'  => 'Scripts d\'automatisation de tâches de fichiers en Python.',
        'technologies' => ['Python'],
        'image'        => 'images/projets/python.png',
    ]
];


$erreurs        = [];
$recapitulatif  = null;

$nom      = '';
$numero   = '';
$email    = '';
$demandes = '';
$projet_select = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom           = nettoyer($_POST['nom'] ?? '');
    $numero        = nettoyer($_POST['numero'] ?? '');
    $email         = nettoyer($_POST['email'] ?? '');
    $demandes      = nettoyer($_POST['demandes'] ?? '');
    $projet_select = nettoyer($_POST['projet'] ?? '');

  
    if (!champ_requis($nom)) {
        $erreurs['nom'] = 'Le nom complet est obligatoire.';
    }
    if (!champ_requis($numero)) {
        $erreurs['numero'] = 'Le numéro de téléphone est obligatoire.';
    }
    if (empty($email)) {
        $erreurs['email'] = 'L\'adresse e-mail est obligatoire.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = 'L\'adresse e-mail saisie n\'est pas valide.';
    }
    if (!champ_requis($demandes)) {
        $erreurs['demandes'] = 'Veuillez décrire ce que vous recherchez.';
    }

    
    if (empty($erreurs)) {
        $recapitulatif = [
            'nom'         => $nom,
            'numero'      => $numero,
            'email'       => $email,
            'description' => $demandes,
            'type_projet' => $projet_select,
        ];
        
     
        $nom = $numero = $email = $demandes = $projet_select = '';
    }
}


$mot_cle   = nettoyer($_GET['q'] ?? '');
$resultats = [];

if ($mot_cle !== '') {
    foreach ($projets as $p) {
       
        if (stripos($p['titre'], $mot_cle) !== false || 
            stripos($p['description'], $mot_cle) !== false ||
            in_array(strtolower($mot_cle), array_map('strtolower', $p['technologies']))) {
            $resultats[] = $p;
        }
    }
} else {

    $resultats = $projets;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portfolio - Contact & Projets</title>
    <link rel="stylesheet" href="./style.css">
    <style>
        .erreur-texte { color: red; font-size: 0.9em; font-weight: bold; display: block; margin-top: 5px; }
        .success-box { background-color: #e6ffe6; color: green; padding: 15px; border: 1px solid green; border-radius: 5px; margin-bottom: 20px; }
        .carte-projet { border: 1px solid #ccc; padding: 15px; margin: 10px 0; border-radius: 8px; display: inline-block; width: 300px; vertical-align: top; }
        .badge { background: #007BFF; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.8em; margin-right: 5px; }
    </style>
</head>
<body class="contacter">

    <h1><strong>Bienvenue mes clients !</strong></h1>

    <?php if ($recapitulatif !== null) : ?>
        <div class="success-box">
            <h3> Demande soumise avec succès !</h3>
            <p><strong>Récapitulatif de votre demande :</strong></p>
            <ul>
                <li><strong>Nom :</strong> <?= $recapitulatif['nom'] ?></li>
                <li><strong>Téléphone :</strong> <?= $recapitulatif['numero'] ?></li>
                <li><strong>Email :</strong> <?= $recapitulatif['email'] ?></li>
                <li><strong>Technologie ciblée :</strong> <?= $recapitulatif['type_projet'] ?></li>
                <li><strong>Description :</strong> <?= nl2br($recapitulatif['description']) ?></li>
            </ul>
        </div>
    <?php endif; ?>

    <h2>Voici les moyens de me contacter :</h2>

   
    <form method="POST" action="">
        
        <div class="nom">
            <label for="nom">Entrez votre nom complet :</label><br>
            <input type="text" name="nom" id="nom" value="<?= $nom ?>" autofocus size="50">
            <?php if (isset($erreurs['nom'])) : ?>
                <span class="erreur-texte"><?= $erreurs['nom'] ?></span>
            <?php endif; ?>
        </div><br>

        <div class="numero">
            <label for="numero">Entrez votre numéro :</label><br>
            <input type="tel" name="numero" id="numero" value="<?= $numero ?>" placeholder="+221">
            <?php if (isset($erreurs['numero'])) : ?>
                <span class="erreur-texte"><?= $erreurs['numero'] ?></span>
            <?php endif; ?>
        </div><br>

        <div class="mail">
            <label for="email">Saisissez votre Adresse email :</label><br>
            <input type="email" name="email" id="email" value="<?= $email ?>" placeholder="email@gmail.com" size="25">
            <?php if (isset($erreurs['email'])) : ?>
                <span class="erreur-texte"><?= $erreurs['email'] ?></span>
            <?php endif; ?>
        </div><br>

        <div class="Demande">
            <label for="demandes">Demandez ici ce que vous cherchez :</label><br>
            <textarea name="demandes" id="demandes" rows="4" cols="40"><?= $demandes ?></textarea>
            <?php if (isset($erreurs['demandes'])) : ?>
                <span class="erreur-texte"><?= $erreurs['demandes'] ?></span>
            <?php endif; ?>
        </div><br>

        <div class="recherche">
            <label for="projet">Quel type de projet recherchez-vous ?</label><br>
            <select name="projet" id="projet">
                <option value="Général" <?= $projet_select === 'Général' ? 'selected' : '' ?>>Projets</option>
                <option value="Python" <?= $projet_select === 'Python' ? 'selected' : '' ?>>Python</option>
                <option value="LangageC" <?= $projet_select === 'LangageC' ? 'selected' : '' ?>>Langage C</option>
                <option value="Javascript" <?= $projet_select === 'Javascript' ? 'selected' : '' ?>>Javascript</option>
                <option value="HTML-CSS" <?= $projet_select === 'HTML-CSS' ? 'selected' : '' ?>>HTML-CSS</option>
                <option value="PHP" <?= $projet_select === 'PHP' ? 'selected' : '' ?>>PHP</option>
            </select>
        </div><br>

        <button type="submit">Envoyer la demande</button>
    </form>

    <br><hr><br>

  
    <h2>Rechercher dans mes réalisations :</h2>
    <form method="GET" action="">
        <label for="q">Entrez un mot-clé (ex: Python, SenShop...) :</label><br>
        <input type="text" name="q" id="q" value="<?= $mot_cle ?>" placeholder="Rechercher...">
        <button type="submit">Filtrer</button>
        <?php if ($mot_cle !== '') : ?>
            <a href="index.php">Réinitialiser</a>
        <?php endif; ?>
    </form>

    <br>

    <
    <div class="liste-projets">
        <?php foreach ($resultats as $projet) : ?>
            <div class="carte-projet">
                
                <img src="<?= htmlspecialchars($projet['image']) ?>" alt="<?= htmlspecialchars($projet['titre']) ?>" style="width:100%; max-height:150px; background:#eee; display:block;">
                <h3><?= htmlspecialchars($projet['titre']) ?></h3>
                <p><?= htmlspecialchars($projet['description']) ?></p>
                <div class="technologies">
                    <?php foreach ($projet['technologies'] as $tech) : ?>
                        <span class="badge"><?= htmlspecialchars($tech) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($resultats)) : ?>
            <p>Aucun projet ne correspond à votre recherche "<strong><?= $mot_cle ?></strong>".</p>
        <?php endif; ?>
    </div>

    <br><hr><br>

    <p class="info">
        Mon contact : +221 77 509 55 82 <br>
        Mon adresse mail : pape294@gmail.com <br>
    </p>

</body>
</html>
