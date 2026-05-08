-- ============================================================
-- SCRIPT D'INITIALISATION DES UTILISATEURS DE TEST INNOGOV
-- ============================================================
-- Utilisez ce script pour initialiser la base de données avec des utilisateurs d'exemple
-- Les mots de passe sont hachés avec PASSWORD_DEFAULT (BCRYPT)

-- IMPORTANT: Les valeurs de password_hash() doivent être générées en PHP
-- Vous pouvez utiliser le script PHP initUsers.php fourni pour cela

-- ============================================================
-- 1. ADMINISTRATEUR
-- ============================================================
INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, statut, date_creation
) VALUES (
    'Admin', 'InnoGov', 'admin@innogov.tn',
    -- Mot de passe: 'admin123456' (à générer via PHP)
    '$2y$10$N9qo8uLOickgx2ZMRZoHyeIjZAgcg7b3XeKeUxWdeS86E36P4/eIW',
    'admin', 'N/A',
    '00000000', '29000000', 'Tunis', 'Homme', '1990-01-01',
    'actif', NOW()
);

-- ============================================================
-- 2. AGENTS PUBLICS
-- ============================================================
INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, nom_organisation, statut, date_creation
) VALUES (
    'Jean', 'Agent', 'agent@innogov.tn',
    -- Mot de passe: 'agent123456' (à générer via PHP)
    '$2y$10$N9qo8uLOickgx2ZMRZoHyeIjZAgcg7b3XeKeUxWdeS86E36P4/eIW',
    'agent', 'agent_public',
    '12345678', '22000000', 'Tunis', 'Homme', '1985-05-10',
    'Ministère de l\'Intérieur', 'actif', NOW()
);

INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, nom_organisation, statut, date_creation
) VALUES (
    'Fatima', 'Agent', 'fatima@innogov.tn',
    -- Mot de passe: 'agent123456' (à générer via PHP)
    '$2y$10$N9qo8uLOickgx2ZMRZoHyeIjZAgcg7b3XeKeUxWdeS86E36P4/eIW',
    'agent', 'agent_public',
    '87654321', '21000000', 'Ariana', 'Femme', '1988-03-22',
    'Ministère des Affaires Sociales', 'actif', NOW()
);

-- ============================================================
-- 3. CLIENTS - CITOYENS
-- ============================================================
INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, statut, date_creation
) VALUES (
    'Ahmed', 'Citoyen', 'ahmed@mail.tn',
    -- Mot de passe: 'user123456' (à générer via PHP)
    '$2y$10$N9qo8uLOickgx2ZMRZoHyeIjZAgcg7b3XeKeUxWdeS86E36P4/eIW',
    'user', 'citoyen',
    '12345670', '99000000', 'Sfax', 'Homme', '1995-03-15',
    'actif', NOW()
);

INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, statut, date_creation
) VALUES (
    'Leila', 'Citoyenne', 'leila@mail.tn',
    -- Mot de passe: 'user123456' (à générer via PHP)
    '$2y$10$N9qo8uLOickgx2ZMRZoHyeIjZAgcg7b3XeKeUxWdeS86E36P4/eIW',
    'user', 'citoyen',
    '98765432', '98999999', 'Sousse', 'Femme', '1992-07-20',
    'actif', NOW()
);

-- ============================================================
-- 4. CLIENTS - PROFESSIONNELS
-- ============================================================
INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, nom_organisation, profession, statut, date_creation
) VALUES (
    'Mohamed', 'Entreprise', 'contact@entreprise.tn',
    -- Mot de passe: 'user123456' (à générer via PHP)
    '$2y$10$N9qo8uLOickgx2ZMRZoHyeIjZAgcg7b3XeKeUxWdeS86E36P4/eIW',
    'user', 'professionnel',
    '11223344', '71555555', 'Tunis', 'Homme', '1980-06-01',
    'TechStartup Tunisia', 'PDG', 'actif', NOW()
);

INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance, nom_organisation, profession, statut, date_creation
) VALUES (
    'Sophia', 'Consultant', 'sophia@consulting.tn',
    -- Mot de passe: 'user123456' (à générer via PHP)
    '$2y$10$N9qo8uLOickgx2ZMRZoHyeIjZAgcg7b3XeKeUxWdeS86E36P4/eIW',
    'user', 'professionnel',
    '55667788', '72999999', 'Monastir', 'Femme', '1988-09-12',
    'Digital Consulting Group', 'Consultante', 'actif', NOW()
);

-- ============================================================
-- NOTES IMPORTANTES
-- ============================================================
-- 1. Les hashes de mot de passe fournis ici sont des exemples
-- 2. Pour générer les vrais hashes, utilisez le script PHP: initUsers.php
-- 3. Format du hash: $2y$10$... (BCRYPT)
-- 4. Mots de passe d'exemple:
--    - Admin: admin123456
--    - Agents: agent123456
--    - Clients: user123456

-- Pour tester:
-- SELECT * FROM utilisateurs;
-- SELECT COUNT(*) as total FROM utilisateurs;
-- SELECT role, type_compte, COUNT(*) FROM utilisateurs GROUP BY role, type_compte;
