<?php
// $offre est passé par le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une offre - Admin</title>
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
            <h1>Modifier l'offre</h1>
            <form id="offreForm">
                <input type="hidden" name="id" value="<?= $offre['id_offre'] ?>">
                <div class="form-group">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" value="<?= htmlspecialchars($offre['titre']) ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" required><?= htmlspecialchars($offre['description']) ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Entité</label>
                    <input type="text" name="entite" value="<?= htmlspecialchars($offre['entite']) ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Date limite</label>
                    <input type="date" name="date_limite" value="<?= $offre['date_limite'] ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre de postes</label>
                    <input type="number" name="nombre_postes" value="<?= $offre['nombre_postes'] ?>" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-control">
                        <option value="Ouvert" <?= $offre['statut'] == 'Ouvert' ? 'selected' : '' ?>>Ouvert</option>
                        <option value="Fermer" <?= $offre['statut'] == 'Fermer' ? 'selected' : '' ?>>Fermé</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Mettre à jour</button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('offreForm').onsubmit = async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const id = formData.get('id');
            const res = await fetch(`index.php?controller=offre&action=modifier&id=${id}`, { method: 'POST', body: formData });
            const data = await res.json();
            if (data.success) window.location.href = 'index.php?controller=offre&action=admin-lister';
            else alert('Erreur lors de la modification');
        };
    </script>
</body>
</html>