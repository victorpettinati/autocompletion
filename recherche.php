<?php
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    // Connexion à la base de données avec PDO (Remplacez 'nom_utilisateur', 'mot_de_passe' et 'nom_base_de_donnees' par vos identifiants réels)
    $dsn = 'mysql:host=localhost;dbname=animaux';
    $username = 'root';
    $password = 'Laplateforme.06!';

    try {
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête pour obtenir les résultats commençant par le terme de recherche
        $exact_query = "SELECT * FROM animal WHERE nom LIKE :search_term ORDER BY nom";
        $exact_stmt = $conn->prepare($exact_query);
        $exact_stmt->execute(['search_term' => "$search_term%"]);
        $exact_result = $exact_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Requête pour obtenir les résultats contenant le terme de recherche
        $contain_query = "SELECT * FROM animal WHERE nom LIKE :search_term AND nom NOT LIKE :exact_term ORDER BY nom";
        $contain_stmt = $conn->prepare($contain_query);
        $contain_stmt->execute(['search_term' => "%$search_term%", 'exact_term' => "$search_term%"]);
        $contain_result = $contain_stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Résultats de la recherche</title>
</head>
<body>
    <form action="recherche.php" method="GET">
        <input type="text" name="search" id="searchInput" value="<?php echo $search_term; ?>" placeholder="Rechercher...">
        <input type="submit" value="Rechercher">
    </form>

    <div id="autocompleteResults"></div>

    <h2>Résultats :</h2>
    <ul>
        <?php
        // Affichage des correspondances exactes
        if ($exact_result) {
            foreach ($exact_result as $row) {
                echo "<li><a href='element.php?id={$row['id']}'>{$row['nom']}</a></li>";
            }
            if ($contain_result) {
                echo '<hr>'; // Ajout d'une séparation entre les correspondances exactes et les correspondances contenant le terme de recherche
            }
        }

        // Affichage des correspondances contenant le terme de recherche
        if ($contain_result) {
            foreach ($contain_result as $row) {
                echo "<li><a href='element.php?id={$row['id']}'>{$row['nom']}</a></li>";
            }
        } else {
            echo "<li>Aucun résultat trouvé.</li>";
        }
        ?>
    </ul>

    <script>
        // JavaScript code for handling autocomplete
        const searchInput = document.getElementById('searchInput');
        const autocompleteResults = document.getElementById('autocompleteResults');

        searchInput.addEventListener('input', function() {
            const searchTerm = searchInput.value;
            if (searchTerm.length >= 2) {
                fetchAutocompleteResults(searchTerm);
            } else {
                autocompleteResults.innerHTML = '';
            }
        });

        function fetchAutocompleteResults(searchTerm) {
            fetch(`autocomplete_recherche.php?search=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    let resultsHtml = '';
                    data.forEach(row => {
                        resultsHtml += `<div><a href='element.php?id=${row.id}'>${row.nom}</a></div>`;
                    });
                    autocompleteResults.innerHTML = resultsHtml;
                })
                .catch(error => {
                    console.error('Error fetching autocomplete results:', error);
                });
        }
    </script>
</body>
</html>
