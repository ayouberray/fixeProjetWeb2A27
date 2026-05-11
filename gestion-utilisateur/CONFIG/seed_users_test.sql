TRUNCATE TABLE utilisateurs;

INSERT INTO utilisateurs (
    nom, prenom, email, password, role, type_compte,
    cin, telephone, ville, sexe, date_naissance,
    nom_organisation, profession, statut, date_creation, email_verifie
) VALUES
('Admin', 'InnoGov', 'admin@innogov.tn', '$2y$10$RR7hDNj.d.fCR3/JyXSsceSYUmHwToP7gj.3oRC5pPH5SFP/M0KUa', 'admin', 'N/A', '00000000', '29000000', 'Tunis', 'Homme', '1990-01-01', NULL, NULL, 'actif', NOW(), 1),
('Jean', 'Agent', 'agent@innogov.tn', '$2y$10$JVHgRfJJ2CSsI4SbJmjqa.DIqx50WOecjpBwLx9hq/T6pqtjplUja', 'agent', 'agent_public', '12345678', '22000000', 'Tunis', 'Homme', '1985-05-10', 'Ministere de l''Interieur', NULL, 'actif', NOW(), 1),
('Ahmed', 'Citoyen', 'ahmed@mail.tn', '$2y$10$92K/qrwCej1BBE6b8xZdQOS1lXA.APTW4mtLFqQbB2/Pb1BH03DF2', 'user', 'citoyen', '12345670', '99000000', 'Sfax', 'Homme', '1995-03-15', NULL, NULL, 'actif', NOW(), 1),
('Leila', 'Citoyenne', 'leila@mail.tn', '$2y$10$92K/qrwCej1BBE6b8xZdQOS1lXA.APTW4mtLFqQbB2/Pb1BH03DF2', 'user', 'citoyen', '98765432', '98999999', 'Sousse', 'Femme', '1992-07-20', NULL, NULL, 'actif', NOW(), 1);
