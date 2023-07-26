<!DOCTYPE html>
<html>
<head>
    <title>Autocompletion Search</title>
</head>
<body>
    <form action="recherche.php" method="GET">
        <input type="text" name="search" id="searchInput" placeholder="Search...">
        <input type="submit" value="Search">
    </form>

    <div id="autocompleteResults"></div>

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
            fetch(`autocomplete.php?search=${encodeURIComponent(searchTerm)}`)
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
