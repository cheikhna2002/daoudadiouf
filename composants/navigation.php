<?php
$page_courante = basename($_SERVER['PHP_SELF']);
?>

<nav>
    <ul class="monportfolio">
        <li>
            <a href="./index.php"
                <?php if ($page_courante === 'index.php') echo 'class="actif"'; ?>>
                Sommaire
            </a>
        </li>
        <li>
            <a href="./presentation.php"
                <?php if ($page_courante === 'presentation.php') echo 'class="actif"'; ?>>
                Présentation
            </a>
        </li>
        <li>
            <a href="./projet.php"
                <?php if ($page_courante === 'projet.php') echo 'class="actif"'; ?>>
                Mes projets
            </a>
        </li>
        <li>
            <a href="./formulaire.php"
                <?php if ($page_courante === 'formulaire.php') echo 'class="actif"'; ?>>
                Me contacter
            </a>
        </li>
    </ul>
</nav>
