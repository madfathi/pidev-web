// public/js/search_commande.js
$(document).ready(function() {
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val();

        $.ajax({
            url: '{{ path (search_commande) }}',
            method: 'POST',
            data: {
                searchTerm: searchTerm
            },
            success: function(data) {
                // Afficher les résultats dans la section spécifiée
                $('#searchResults').html(data);
            }
        });
    });
});