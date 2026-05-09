<?php
// $offres disponible depuis OffreController
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres - INNOC@V</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css?v=<?= time() ?>">
    <script src="/ProjettWeb/assets/js/script.js?v=<?= time() ?>" defer></script>
    <style>
        .search-wrapper {
            display: flex; gap: 1rem; background: var(--white); border-radius: 3rem;
            padding: 0.3rem 0.3rem 0.3rem 1.5rem; box-shadow: var(--shadow-md);
            border: 1px solid var(--gray-300); margin-bottom: 2rem; align-items: center;
        }
        .search-wrapper input { flex: 1; border: none; padding: 0.8rem 0; background: transparent; color: var(--dark); outline: none; }
        .search-wrapper button { background: var(--primary); border: none; border-radius: 2rem; padding: 0.7rem 1.8rem; color: white; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .offers-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 2rem; margin-top: 1rem; }
        .offer-card { background: var(--white); border-radius: 1.5rem; overflow: hidden; transition: 0.3s; box-shadow: var(--shadow-md); border: 1px solid var(--gray-300); display: flex; flex-direction: column; }
        .offer-card:hover { transform: translateY(-8px); box-shadow: var(--shadow-lg); border-color: var(--primary-light); }
        .offer-card-header { background: linear-gradient(135deg, var(--primary-light), var(--white)); padding: 1.5rem; border-bottom: 3px solid var(--primary); }
        .offer-card-header h3 { font-size: 1.5rem; margin-bottom: 0.5rem; color: var(--primary); }
        .offer-entity { display: inline-block; background: rgba(0,109,91,0.1); padding: 0.2rem 0.8rem; border-radius: 2rem; font-size: 0.8rem; font-weight: 600; color: var(--primary); }
        .offer-card-body { padding: 1.5rem; flex: 1; }
        .offer-description { color: var(--gray-700); margin-bottom: 1.2rem; }
        .offer-meta { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.2rem; font-size: 0.85rem; color: var(--gray-700); }
        .offer-meta i { width: 1.2rem; color: var(--primary); }
        .badge-deadline { background: #fff3cd; color: #856404; padding: 0.2rem 0.6rem; border-radius: 2rem; font-size: 0.7rem; }
        .badge-urgent { background: #f8d7da; color: #842029; }
        .offer-footer { background: var(--gray-100); padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--gray-300); }
        .btn-offer { background: var(--primary); color: white; padding: 0.5rem 1.2rem; border-radius: 2rem; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .btn-offer:hover { background: var(--primary-dark); }
        .no-results { text-align: center; padding: 3rem; background: var(--white); border-radius: 1.5rem; color: var(--gray-500); }
        @media (max-width: 768px) { .offers-grid { grid-template-columns: 1fr; } }

        /* ===== GÉOLOCALISATION ===== */
        .geo-section {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 1.5rem; padding: 2rem; margin-bottom: 2rem;
            color: white; display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 1rem; box-shadow: var(--shadow-primary);
        }
        .geo-section-left { display: flex; align-items: center; gap: 1.2rem; }
        .geo-icon { font-size: 2.5rem; animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.15); } }
        .geo-section h3 { font-size: 1.2rem; margin-bottom: 0.3rem; }
        .geo-section p { font-size: 0.9rem; opacity: 0.85; }
        .btn-geo {
            background: white; color: var(--primary); border: none; padding: 0.8rem 1.8rem;
            border-radius: 2rem; font-weight: 700; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; gap: 0.6rem; font-size: 0.95rem;
        }
        .btn-geo:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .btn-geo:disabled { opacity: 0.7; cursor: not-allowed; transform: none; }
        .geo-results { margin-top: 1.5rem; padding: 1.5rem; background: var(--white); border-radius: 1.2rem; box-shadow: var(--shadow-md); }
        .geo-results h4 { color: var(--primary); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .geo-municipality-list { display: flex; flex-wrap: wrap; gap: 0.8rem; }
        .geo-mun-btn {
            background: var(--primary-light); color: var(--primary); border: 2px solid var(--primary);
            padding: 0.4rem 1rem; border-radius: 2rem; font-weight: 600; cursor: pointer;
            transition: 0.3s; font-size: 0.9rem;
        }
        .geo-mun-btn:hover, .geo-mun-btn.active { background: var(--primary); color: white; }
    </style>
</head>
<body>
<div class="loader"><div class="spinner"></div></div>

<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="logo">
            <img src="/ProjettWeb/assets/images/logo.png" alt="INNOGOV" style="height: 60px; object-fit: contain;">
        </a>
        <div class="nav-menu">
            <a href="index.php" class="nav-link">Accueil</a>
            <a href="index.php?controller=offre&action=lister" class="nav-link active">Offres</a>
            <a href="index.php?controller=candidature&action=admin-lister" class="nav-link">Candidatures</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
                <button id="theme-toggle" class="lang-btn" title="Mode sombre"><i class="fas fa-moon"></i></button>
                <a href="index.php?controller=offre&action=carte" class="lang-btn" title="Carte des municipalités" style="text-decoration:none; display:flex; align-items:center;">
                    <i class="fas fa-map-marked-alt"></i>
                </a>
            </div>
            <a href="index.php?controller=offre&action=lister" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> <span data-i18n="seeOffers">Voir les offres</span></a>
        </div>
    </div>
</nav>

<main class="container" style="padding-top: 2rem;">
    <form class="search-wrapper" method="GET" action="index.php" style="align-items: center;">
        <input type="hidden" name="controller" value="offre">
        <input type="hidden" name="action" value="lister">
        <i class="fas fa-search" style="color: var(--gray-500);"></i>
        <input type="text" name="search" placeholder="Rechercher par titre, entité..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        
        <select name="sort" style="border: none; background: transparent; color: var(--dark); outline: none; margin-right: 1rem; cursor: pointer; font-family: inherit; font-weight: 500;">
            <option value="id_offre DESC" <?= ($_GET['sort'] ?? '') == 'id_offre DESC' ? 'selected' : '' ?>>Récentes d'abord</option>
            <option value="titre ASC" <?= ($_GET['sort'] ?? '') == 'titre ASC' ? 'selected' : '' ?>>Titre (A-Z)</option>
            <option value="date_limite ASC" <?= ($_GET['sort'] ?? '') == 'date_limite ASC' ? 'selected' : '' ?>>Date limite (proche)</option>
        </select>

        <button type="submit"><i class="fas fa-filter"></i> Appliquer</button>
    </form>
    <div class="geo-section">
        <div class="geo-section-left">
            <div class="geo-icon">📍</div>
            <div>
                <h3>Offres près de chez vous</h3>
                <p>Trouvez les offres des municipalités autour de votre position</p>
            </div>
        </div>
        <button class="btn-geo" id="geoBtn" onclick="lancerGeolocalisation()">
            <i class="fas fa-location-arrow"></i> Me localiser
        </button>
    </div>
    <div id="geoResults" style="display:none;" class="geo-results">
        <h4><i class="fas fa-map-marker-alt"></i> Municipalités proches trouvées :</h4>
        <div class="geo-municipality-list" id="geoMunList"></div>
        <p style="margin-top: 1rem; font-size: 0.85rem; color: var(--gray-500);"><i class="fas fa-info-circle"></i> Cliquez sur une municipalité pour filtrer les offres.</p>
    </div>

    <div id="offersContainer" class="offers-grid">
        <?php if (empty($offres)): ?>
            <div class="no-results">Aucune offre disponible.</div>
        <?php else: ?>
            <?php foreach ($offres as $offre): ?>
            <div class="offer-card" data-title="<?= htmlspecialchars(strtolower($offre['titre'])) ?>" data-entity="<?= htmlspecialchars(strtolower($offre['entite'])) ?>">
                <div class="offer-card-header">
                    <h3><?= htmlspecialchars($offre['titre']) ?></h3>
                    <span class="offer-entity"><i class="fas fa-building"></i> <?= htmlspecialchars($offre['entite']) ?></span>
                </div>
                <div class="offer-card-body">
                    <div class="offer-description"><?= nl2br(htmlspecialchars(substr($offre['description'], 0, 120))) ?>...</div>
                    <div class="offer-meta">
                        <span><i class="fas fa-users"></i> <?= $offre['nombre_postes'] ?> poste(s)</span>
                        <span><i class="fas fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($offre['date_limite'])) ?></span>
                        <?php if ((strtotime($offre['date_limite']) - time()) / 86400 < 7 && (strtotime($offre['date_limite']) - time()) > 0): ?>
                        <span class="badge-deadline badge-urgent"><i class="fas fa-hourglass-half"></i> Urgent</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="offer-footer">
                    <?php 
                    $isFull = ($offre['nb_candidats'] ?? 0) >= ($offre['nombre_postes'] ?? 0);
                    if ($isFull): ?>
                        <span class="badge badge-danger" style="background: #dc3545; color: white;">
                            <i class="fas fa-lock"></i> Complet
                        </span>
                        <button class="btn-offer" disabled style="background: #ccc; cursor: not-allowed; border:none; opacity: 0.7;">
                            Fermé <i class="fas fa-ban"></i>
                        </button>
                    <?php else: ?>
                        <span class="badge <?= $offre['statut'] == 'Ouvert' ? 'badge-success' : 'badge-danger' ?>"><?= $offre['statut'] ?></span>
                        <a href="index.php?controller=offre&action=detail&id=<?= $offre['id_offre'] ?>" class="btn-offer">Postuler <i class="fas fa-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section"><h4>INNOC@V</h4><p>Solutions numériques pour les citoyens</p></div>
        <div class="footer-section"><h4>Contact</h4><p><i class="fas fa-envelope"></i> contact@innocv.gov.ma</p></div>
        <div class="footer-section"><h4>Horaires</h4><p>Lun - Ven: 8h30-15h30</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2025 INNOC@V - Tous droits réservés</p></div>
</footer>

</body>
</html>

<script>
function lancerGeolocalisation() {
    const btn = document.getElementById('geoBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localisation...';
    if (!navigator.geolocation) {
        alert('Votre navigateur ne supporte pas la géolocalisation.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-location-arrow"></i> Me localiser';
        return;
    }
    navigator.geolocation.getCurrentPosition(
        (position) => trouverMunicipalites(position.coords.latitude, position.coords.longitude, btn),
        (error) => {
            alert('Impossible d\'obtenir votre position. Vérifiez vos permissions GPS.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-arrow"></i> Me localiser';
        }
    );
}
async function trouverMunicipalites(lat, lng, btn) {
    try {
        const url = `https://nominatim.openstreetmap.org/search?format=json&limit=10&q=municipality&viewbox=${lng-0.5},${lat+0.5},${lng+0.5},${lat-0.5}&bounded=1`;
        const fallbackUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=10`;

        const res = await fetch(fallbackUrl, { headers: { 'Accept-Language': 'fr' } });
        const data = await res.json();
        const address = data.address || {};
        const villeActuelle = address.city || address.town || address.village || address.county || address.state || 'Inconnue';
        const resProches = await fetch(
            `https://nominatim.openstreetmap.org/search?format=json&limit=6&city=${encodeURIComponent(villeActuelle)}&country=Tunisia&addressdetails=1`,
            { headers: { 'Accept-Language': 'fr' } }
        );
        const voisins = await resProches.json();
        const municipalites = new Set();
        municipalites.add(villeActuelle); 
        voisins.forEach(v => {
            const a = v.address || {};
            const nom = a.city || a.town || a.village || a.municipality;
            if (nom) municipalites.add(nom);
        });

        afficherMunicipalites([...municipalites], lat, lng, btn);
    } catch (e) {
        alert('Erreur lors de la recherche des municipalités. Connexion internet requise.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-location-arrow"></i> Me localiser';
    }
}
function afficherMunicipalites(municipalites, lat, lng, btn) {
    btn.innerHTML = '<i class="fas fa-check"></i> Localisé !';

    const geoResults = document.getElementById('geoResults');
    const munList = document.getElementById('geoMunList');
    munList.innerHTML = '';
    geoResults.style.display = 'block';
    municipalites.forEach(nom => {
        const btn = document.createElement('button');
        btn.className = 'geo-mun-btn';
        btn.textContent = nom;
        btn.onclick = () => filtrerParMunicipalite(nom, btn);
        munList.appendChild(btn);
    });
    geoResults.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}
function filtrerParMunicipalite(nomVille, btnClique) {
    document.querySelectorAll('.geo-mun-btn').forEach(b => b.classList.remove('active'));
    btnClique.classList.add('active');

    const terme = nomVille.toLowerCase();
    const cartes = document.querySelectorAll('.offer-card');
    let trouve = 0;

    cartes.forEach(card => {
        const entite = card.getAttribute('data-entity') || '';
        if (entite.includes(terme) || terme.includes(entite.split(' ')[0])) {
            card.style.display = '';
            card.style.border = '2px solid var(--primary)';
            trouve++;
        } else {
            card.style.display = 'none';
            card.style.border = '';
        }
    });
    const noResults = document.querySelector('.no-results');
    if (trouve === 0) {
        const msg = document.createElement('div');
        msg.className = 'no-results';
        msg.id = 'geo-no-result';
        msg.innerHTML = `<i class="fas fa-map-marker-alt" style="font-size:2rem;color:var(--primary);display:block;margin-bottom:1rem;"></i>Aucune offre trouvée pour <strong>${nomVille}</strong>.<br><small>Essayez une autre municipalité ou consultez toutes les offres.</small>`;
        const container = document.getElementById('offersContainer');
        const old = document.getElementById('geo-no-result');
        if (old) old.remove();
        if (trouve === 0) container.appendChild(msg);
    } else {
        const old = document.getElementById('geo-no-result');
        if (old) old.remove();
    }
}
</script>