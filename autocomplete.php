<?php
if (isset($_GET['search'])) {
    $search_term = $_GET['search'];
    // Connexion à la base de données avec PDO
    $dsn = 'mysql:host=localhost;dbname=animaux';
    $username = 'root';
    $password = 'Laplateforme.06!';

    try {
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête pour obtenir les résultats commençant par le terme de recherche
        $autocomplete_query = "SELECT * FROM animal WHERE nom LIKE :search_term ORDER BY nom LIMIT 10";
        $autocomplete_stmt = $conn->prepare($autocomplete_query);
        $autocomplete_stmt->execute(['search_term' => "$search_term%"]);
        $autocomplete_result = $autocomplete_stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($autocomplete_result);
        exit();
    } catch (PDOException $e) {
        echo "Erreur de connexion : " . $e->getMessage();
    }
}
?>
