document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('avisForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        const note = document.querySelector('input[name="note"]') ? .value;
        const satisfaction = document.getElementById('satisfaction') ? .value;

        if (!note) {
            e.preventDefault();
            alert('Veuillez sélectionner une note');
            return false;
        }

        if (!satisfaction) {
            e.preventDefault();
            alert('Veuillez sélectionner un niveau de satisfaction');
            return false;
        }
    });
});