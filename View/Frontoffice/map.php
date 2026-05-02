<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carte des Municipalités - INNOC@V</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/ProjettWeb/assets/js/script.js?v=<?= time() ?>" defer></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--gray-100); }
        #map {
            width: 100%;
            height: calc(100vh - 80px);
            z-index: 1;
        }
        .map-panel {
            position: absolute;
            top: 100px;
            left: 20px;
            z-index: 1000;
            background: var(--white);
            border-radius: 1.2rem;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,0.18);
            min-width: 280px;
            max-width: 320px;
            backdrop-filter: blur(10px);
            border: 1px solid var(--gray-300);
        }
        .map-panel h3 {
            font-size: 1rem; font-weight: 700;
            color: var(--primary); margin-bottom: 0.8rem;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .btn-locate {
            width: 100%; background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white; border: none; border-radius: 0.8rem;
            padding: 0.8rem; font-weight: 700; cursor: pointer;
            font-size: 0.95rem; display: flex; align-items: center;
            justify-content: center; gap: 0.6rem; transition: 0.3s;
            margin-bottom: 1rem;
        }
        .btn-locate:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,109,91,0.4); }
        .btn-locate:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

        .nearby-list { list-style: none; max-height: 300px; overflow-y: auto; }
        .nearby-list li {
            padding: 0.6rem 0.8rem; border-radius: 0.6rem;
            margin-bottom: 0.4rem; cursor: pointer;
            display: flex; align-items: center; justify-content: space-between;
            transition: 0.2s; font-size: 0.9rem;
        }
        .nearby-list li:hover { background: var(--primary-light); }
        .nearby-list li .dist { font-size: 0.75rem; color: var(--gray-500); font-weight: 600; }
        .nearby-list li .city-name { font-weight: 600; color: var(--dark); }

        .legend {
            display: flex; flex-direction: column; gap: 0.4rem;
            margin-top: 1rem; padding-top: 1rem;
            border-top: 1px solid var(--gray-300); font-size: 0.8rem;
        }
        .legend-item { display: flex; align-items: center; gap: 0.6rem; color: var(--gray-700); }
        .legend-dot { width: 12px; height: 12px; border-radius: 50%; flex-shrink: 0; }
        .map-toast {
            position: fixed; bottom: 30px; right: 30px;
            background: var(--primary); color: white;
            padding: 1rem 1.5rem; border-radius: 0.8rem;
            z-index: 9999; font-weight: 600; font-size: 0.9rem;
            display: flex; align-items: center; gap: 0.6rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="logo">
            <img src="/ProjettWeb/assets/images/logo.png" alt="INNOGOV" style="height: 60px; object-fit: contain;">
        </a>
        <div class="nav-menu">
            <a href="index.php" class="nav-link" data-i18n="home">Accueil</a>
            <a href="index.php?controller=offre&action=lister" class="nav-link" data-i18n="offers">Offres</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
                <button id="theme-toggle" class="lang-btn" title="Mode sombre"><i class="fas fa-moon"></i></button>
            </div>
        </div>
    </div>
</nav>
<div class="map-panel" id="mapPanel">
    <h3><i class="fas fa-map-marked-alt"></i> Municipalités Tunisiennes</h3>
    <p style="font-size: 0.8rem; color: var(--gray-500); margin-bottom: 1rem;">
        <i class="fas fa-info-circle"></i> Localisez-vous pour voir les municipalités proches en surbrillance.
    </p>
    <button class="btn-locate" id="locateBtn" onclick="locateMe()">
        <i class="fas fa-location-arrow"></i> Me localiser
    </button>
    <div id="nearbySection" style="display:none;">
        <h3 style="margin-bottom: 0.6rem;"><i class="fas fa-sort-amount-up"></i> Les plus proches</h3>
        <ul class="nearby-list" id="nearbyList"></ul>
    </div>
    <div class="legend">
        <div class="legend-item"><div class="legend-dot" style="background:#006D5B;"></div> Municipalité avec offres</div>
        <div class="legend-item"><div class="legend-dot" style="background:#FFB800;"></div> Près de vous (&lt; 50 km)</div>
        <div class="legend-item"><div class="legend-dot" style="background:#dc3545;"></div> Votre position</div>
    </div>
</div>

<div id="map"></div>

<script>
const municipalites = [
    { nom: "Tunis",       lat: 36.8188,  lng: 10.1658 },
    { nom: "Sfax",        lat: 34.7406,  lng: 10.7603 },
    { nom: "Sousse",      lat: 35.8245,  lng: 10.6346 },
    { nom: "Kairouan",    lat: 35.6781,  lng: 10.0963 },
    { nom: "Bizerte",     lat: 37.2744,  lng: 9.8739  },
    { nom: "Gabès",       lat: 33.8833,  lng: 10.0972 },
    { nom: "Ariana",      lat: 36.8625,  lng: 10.1956 },
    { nom: "Gafsa",       lat: 34.425,   lng: 8.7842  },
    { nom: "La Marsa",    lat: 36.8764,  lng: 10.3233 },
    { nom: "Monastir",    lat: 35.7643,  lng: 10.8113 },
    { nom: "Ben Arous",   lat: 36.7531,  lng: 10.2278 },
    { nom: "Nabeul",      lat: 36.4561,  lng: 10.7375 },
    { nom: "Hammamet",    lat: 36.4,     lng: 10.6167 },
    { nom: "Kasserine",   lat: 35.1681,  lng: 8.8306  },
    { nom: "Médenine",    lat: 33.3547,  lng: 10.5053 },
    { nom: "Tataouine",   lat: 32.9211,  lng: 10.4503 },
    { nom: "Tozeur",      lat: 33.9197,  lng: 8.1336  },
    { nom: "Kebili",      lat: 33.7042,  lng: 8.9692  },
    { nom: "Siliana",     lat: 36.0831,  lng: 9.3722  },
    { nom: "Le Kef",      lat: 36.1675,  lng: 8.7147  },
    { nom: "Jendouba",    lat: 36.5011,  lng: 8.7803  },
    { nom: "Béja",        lat: 36.7256,  lng: 9.1817  },
    { nom: "Zaghouan",    lat: 36.4028,  lng: 10.1433 },
    { nom: "Mahdia",      lat: 35.5047,  lng: 11.0622 },
    { nom: "Sidi Bouzid", lat: 35.0381,  lng: 9.4858  },
    { nom: "Manouba",     lat: 36.8,     lng: 10.1    },
    { nom: "Djerba",      lat: 33.8075,  lng: 10.855  },
    { nom: "Zarzis",      lat: 33.5033,  lng: 11.1122 },
    { nom: "Kerkennah",   lat: 34.7167,  lng: 11.2167 },
    { nom: "Tabarka",     lat: 36.9542,  lng: 8.7583  },
];
const carte = L.map('map').setView([33.8869, 9.5375], 7);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19
}).addTo(carte);
function creerIcone(couleur, taille = 12) {
    return L.divIcon({
        className: '',
        html: `<div style="
            width:${taille}px; height:${taille}px; 
            background:${couleur}; border-radius:50%; 
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.4);
        "></div>`,
        iconSize: [taille, taille],
        iconAnchor: [taille/2, taille/2]
    });
}

const iconeVerte  = creerIcone('#006D5B', 14);
const iconeOrange = creerIcone('#FFB800', 18);
const iconeRouge  = creerIcone('#dc3545', 20);

const marqueurs = [];
municipalites.forEach(mun => {
    const marqueur = L.marker([mun.lat, mun.lng], { icon: iconeVerte })
        .addTo(carte)
        .bindPopup(`
            <div style="text-align:center; font-family:Inter,sans-serif;">
                <strong style="font-size:1rem;">${mun.nom}</strong><br>
                <small style="color:#006D5B;">Municipalité de Tunisie</small><br><br>
                <a href="index.php?controller=offre&action=lister&search=${encodeURIComponent(mun.nom)}"
                   style="background:#006D5B;color:white;padding:5px 12px;border-radius:20px;text-decoration:none;font-size:0.85rem;">
                   🔍 Voir les offres
                </a>
            </div>
        `);
    marqueurs.push({ mun, marqueur });
});

let marqueurUser = null;

function locateMe() {
    const btn = document.getElementById('locateBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Localisation...';

    if (!navigator.geolocation) {
        showToast('Géolocalisation non supportée par votre navigateur', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-location-arrow"></i> Me localiser';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => afficherPosition(pos.coords.latitude, pos.coords.longitude, btn),
        err => {
            showToast('Permission GPS refusée. Vérifiez vos paramètres.', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-location-arrow"></i> Me localiser';
        }
    );
}
function afficherPosition(lat, lng, btn) {
    btn.innerHTML = '<i class="fas fa-check"></i> Localisé !';
    if (marqueurUser) carte.removeLayer(marqueurUser);

    marqueurUser = L.marker([lat, lng], { icon: iconeRouge })
        .addTo(carte)
        .bindPopup('<strong>📍 Votre position</strong>')
        .openPopup();
    carte.flyTo([lat, lng], 9, { duration: 1.5 });
    const avecDistance = municipalites.map(mun => ({
        ...mun,
        distance: calculerDistance(lat, lng, mun.lat, mun.lng)
    })).sort((a, b) => a.distance - b.distance);
    avecDistance.forEach((mun, i) => {
        const m = marqueurs.find(x => x.mun.nom === mun.nom);
        if (!m) return;
        if (i < 5) {
            m.marqueur.setIcon(iconeOrange);
            m.marqueur.setZIndexOffset(1000);
        } else {
            m.marqueur.setIcon(iconeVerte);
            m.marqueur.setZIndexOffset(0);
        }
    });
    const nearbySection = document.getElementById('nearbySection');
    const nearbyList = document.getElementById('nearbyList');
    nearbySection.style.display = 'block';
    nearbyList.innerHTML = '';

    avecDistance.slice(0, 8).forEach((mun, i) => {
        const li = document.createElement('li');
        li.style.background = i < 5 ? 'rgba(255,184,0,0.1)' : '';
        li.innerHTML = `
            <span class="city-name">${i < 5 ? '🟡' : '🟢'} ${mun.nom}</span>
            <span class="dist">${mun.distance.toFixed(0)} km</span>
        `;
        li.onclick = () => {
            carte.flyTo([mun.lat, mun.lng], 12);
            const m = marqueurs.find(x => x.mun.nom === mun.nom);
            if (m) m.marqueur.openPopup();
        };
        nearbyList.appendChild(li);
    });

    showToast('✅ ' + avecDistance[0].nom + ' est la municipalité la plus proche !', 'success');
}

function calculerDistance(lat1, lng1, lat2, lng2) {
    const R = 6371; // Rayon de la Terre en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2)
            + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180)
            * Math.sin(dLng/2) * Math.sin(dLng/2);
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
}
function showToast(msg, type = 'success') {
    const old = document.querySelector('.map-toast');
    if (old) old.remove();
    const toast = document.createElement('div');
    toast.className = 'map-toast';
    toast.style.background = type === 'error' ? '#dc3545' : '#006D5B';
    toast.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i> ${msg}`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}
</script>
</body>
</html>
