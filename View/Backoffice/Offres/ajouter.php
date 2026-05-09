<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une offre - Admin</title>
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="container header-inner">
            <a href="index.php?controller=offre&action=lister" class="logo">
    <img src="/ProjettWeb/assets/css/logo.png" alt="Logo InnoGov" style="height: 40px;">
</a>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        <div class="card grid-1">
            <h1>Ajouter une offre</h1>
            <form id="offreForm">
                <div class="form-group">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" required></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Entité</label>
                    <input type="text" name="entite" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date limite</label>
                    <input type="date" name="date_limite" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre de postes</label>
                    <input type="number" name="nombre_postes" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-control">
                        <option value="Ouvert">Ouvert</option>
                        <option value="Fermer">Fermé</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('offreForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const res = await fetch('index.php?controller=offre&action=ajouter', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) window.location.href = 'index.php?controller=offre&action=admin-lister';
            else alert('Erreur lors de l\'ajout');
        };
    </script>
</body>
</html>