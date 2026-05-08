CREATE OR REPLACE VIEW vue_emplois_shifts AS
SELECT
    e.id_emploi,
    e.id_agent,
    e.id_service,
    e.id_shift,
    e.date_travail,
    e.statut,
    e.qr_token,
    e.date_creation,
    e.date_modification,
    u.nom AS agent_nom,
    u.prenom AS agent_prenom,
    s.nom_service,
    sh.nom_shift,
    sh.heure_debut,
    sh.heure_fin
FROM emplois e
INNER JOIN shifts sh ON e.id_shift = sh.id_shift
LEFT JOIN users u ON e.id_agent = u.id
LEFT JOIN services s ON e.id_service = s.id_service;
