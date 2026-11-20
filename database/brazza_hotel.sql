-- =============================================================================
-- BRAZZA HOTEL — DATABASE INITIALIZATION
-- MySQL 8.0+ / MariaDB 10.4+ · utf8mb4 · Africa/Brazzaville (UTC+1)
-- -----------------------------------------------------------------------------
-- IMPORTANT
-- The editorial rows in this file are clearly marked `content_status = 'demo'`
-- and are illustrative only. They are NOT official hotel information. Prices
-- are NULL on purpose and must be supplied by the hotel before publication.
-- =============================================================================

SET NAMES utf8mb4;
SET time_zone = '+01:00';
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS brazza_hotel
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE brazza_hotel;

DROP TABLE IF EXISTS admin_activity_log;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS room_amenities;
DROP TABLE IF EXISTS amenities;
DROP TABLE IF EXISTS room_images;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS gallery_images;
DROP TABLE IF EXISTS gallery_categories;
DROP TABLE IF EXISTS hotel_services;
DROP TABLE IF EXISTS hotel_settings;
DROP TABLE IF EXISTS admins;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- ADMINISTRATORS
-- -----------------------------------------------------------------------------
CREATE TABLE admins (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(120)    NOT NULL,
    email           VARCHAR(190)    NOT NULL,
    password_hash   VARCHAR(255)    NOT NULL,
    role            ENUM('owner', 'manager', 'editor') NOT NULL DEFAULT 'manager',
    is_active       TINYINT(1)      NOT NULL DEFAULT 1,
    last_login_at   DATETIME        NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admins_email (email),
    KEY idx_admins_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- ROOM CATEGORIES
-- -----------------------------------------------------------------------------
CREATE TABLE rooms (
    id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug                VARCHAR(160)    NOT NULL,
    name                VARCHAR(120)    NOT NULL,
    tagline             VARCHAR(180)    NULL,
    short_description   VARCHAR(400)    NOT NULL,
    description         TEXT            NOT NULL,
    price_per_night     DECIMAL(12, 2)  NULL COMMENT 'NULL = price not officially supplied',
    currency            CHAR(3)         NOT NULL DEFAULT 'XAF',
    max_occupancy       TINYINT UNSIGNED NOT NULL DEFAULT 2,
    bed_type            VARCHAR(80)     NULL,
    size_sqm            SMALLINT UNSIGNED NULL,
    is_featured         TINYINT(1)      NOT NULL DEFAULT 0,
    is_active           TINYINT(1)      NOT NULL DEFAULT 1,
    content_status      ENUM('demo', 'verified') NOT NULL DEFAULT 'demo',
    sort_order          SMALLINT        NOT NULL DEFAULT 0,
    meta_title          VARCHAR(160)    NULL,
    meta_description    VARCHAR(300)    NULL,
    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_rooms_slug (slug),
    KEY idx_rooms_public (is_active, is_featured, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE room_images (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    room_id         BIGINT UNSIGNED NOT NULL,
    file_path       VARCHAR(255)    NOT NULL,
    alt_text        VARCHAR(180)    NOT NULL,
    is_primary      TINYINT(1)      NOT NULL DEFAULT 0,
    sort_order      SMALLINT        NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_room_images_room (room_id, sort_order),
    CONSTRAINT fk_room_images_room
        FOREIGN KEY (room_id) REFERENCES rooms (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- AMENITIES (normalized many-to-many)
-- -----------------------------------------------------------------------------
CREATE TABLE amenities (
    id          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(100)    NOT NULL,
    icon        VARCHAR(80)     NOT NULL DEFAULT 'fa-circle-check',
    sort_order  SMALLINT        NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_amenities_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE room_amenities (
    room_id     BIGINT UNSIGNED NOT NULL,
    amenity_id  SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (room_id, amenity_id),
    CONSTRAINT fk_room_amenities_room
        FOREIGN KEY (room_id) REFERENCES rooms (id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_room_amenities_amenity
        FOREIGN KEY (amenity_id) REFERENCES amenities (id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- RESERVATION REQUESTS (never an automatic availability confirmation)
-- -----------------------------------------------------------------------------
CREATE TABLE reservations (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    reference       VARCHAR(20)     NOT NULL,
    room_id         BIGINT UNSIGNED NULL,
    guest_name      VARCHAR(120)    NOT NULL,
    phone           VARCHAR(40)     NOT NULL,
    email           VARCHAR(190)    NULL,
    check_in        DATE            NOT NULL,
    check_out       DATE            NOT NULL,
    guests          TINYINT UNSIGNED NOT NULL,
    message         TEXT            NULL,
    status          ENUM('new', 'contacted', 'confirmed', 'declined', 'cancelled')
                    NOT NULL DEFAULT 'new',
    source          ENUM('website', 'whatsapp', 'phone', 'walk_in') NOT NULL DEFAULT 'website',
    admin_notes     TEXT            NULL,
    ip_hash         CHAR(64)        NULL,
    user_agent      VARCHAR(255)    NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_reservations_reference (reference),
    KEY idx_reservations_status_date (status, created_at),
    KEY idx_reservations_stay (check_in, check_out),
    KEY idx_reservations_room (room_id),
    CONSTRAINT fk_reservations_room
        FOREIGN KEY (room_id) REFERENCES rooms (id)
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT chk_reservations_dates CHECK (check_out > check_in),
    CONSTRAINT chk_reservations_guests CHECK (guests BETWEEN 1 AND 20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- GALLERY
-- -----------------------------------------------------------------------------
CREATE TABLE gallery_categories (
    id          SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug        VARCHAR(120)    NOT NULL,
    name        VARCHAR(120)    NOT NULL,
    sort_order  SMALLINT        NOT NULL DEFAULT 0,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_gallery_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE gallery_images (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id     SMALLINT UNSIGNED NULL,
    file_path       VARCHAR(255)    NOT NULL,
    alt_text        VARCHAR(180)    NOT NULL,
    caption         VARCHAR(200)    NULL,
    is_featured     TINYINT(1)      NOT NULL DEFAULT 0,
    is_active       TINYINT(1)      NOT NULL DEFAULT 1,
    content_status  ENUM('demo', 'verified') NOT NULL DEFAULT 'demo',
    sort_order      SMALLINT        NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_gallery_public (is_active, sort_order),
    KEY idx_gallery_category (category_id),
    CONSTRAINT fk_gallery_images_category
        FOREIGN KEY (category_id) REFERENCES gallery_categories (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- SERVICES / EDITORIAL BLOCKS
-- -----------------------------------------------------------------------------
CREATE TABLE hotel_services (
    id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title           VARCHAR(120)    NOT NULL,
    description     VARCHAR(400)    NOT NULL,
    icon            VARCHAR(80)     NOT NULL DEFAULT 'fa-bell-concierge',
    is_active       TINYINT(1)      NOT NULL DEFAULT 1,
    content_status  ENUM('demo', 'verified') NOT NULL DEFAULT 'demo',
    sort_order      SMALLINT        NOT NULL DEFAULT 0,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_services_public (is_active, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- CONTACT MESSAGES
-- -----------------------------------------------------------------------------
CREATE TABLE contact_messages (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name        VARCHAR(120)    NOT NULL,
    email       VARCHAR(190)    NOT NULL,
    phone       VARCHAR(40)     NULL,
    subject     VARCHAR(160)    NOT NULL,
    message     TEXT            NOT NULL,
    status      ENUM('new', 'read', 'replied', 'archived') NOT NULL DEFAULT 'new',
    ip_hash     CHAR(64)        NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_contact_status_date (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- HOTEL SETTINGS
-- `is_verified = 0` means the value is missing or awaiting official validation.
-- -----------------------------------------------------------------------------
CREATE TABLE hotel_settings (
    setting_key     VARCHAR(100)    NOT NULL,
    setting_value   TEXT            NOT NULL,
    setting_group   VARCHAR(50)     NOT NULL DEFAULT 'general',
    input_type      ENUM('text', 'textarea', 'email', 'phone', 'url', 'time', 'boolean')
                    NOT NULL DEFAULT 'text',
    is_public       TINYINT(1)      NOT NULL DEFAULT 1,
    is_verified     TINYINT(1)      NOT NULL DEFAULT 0,
    updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (setting_key),
    KEY idx_settings_group (setting_group)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- AUDIT TRAIL FOR ADMIN ACTIONS (no sensitive values are stored)
-- -----------------------------------------------------------------------------
CREATE TABLE admin_activity_log (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    admin_id    BIGINT UNSIGNED NULL,
    action      VARCHAR(80)     NOT NULL,
    target_type VARCHAR(60)     NULL,
    target_id   BIGINT UNSIGNED NULL,
    context     VARCHAR(255)    NULL,
    ip_hash     CHAR(64)        NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_activity_admin_date (admin_id, created_at),
    CONSTRAINT fk_activity_admin
        FOREIGN KEY (admin_id) REFERENCES admins (id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- SEED DATA — DEMONSTRATION CONTENT
-- =============================================================================

INSERT INTO hotel_settings
    (setting_key, setting_value, setting_group, input_type, is_public, is_verified)
VALUES
    ('hotel_name', 'Brazza Hotel', 'identity', 'text', 1, 1),
    ('hotel_city', 'Brazzaville', 'identity', 'text', 1, 1),
    ('hotel_country', 'République du Congo', 'identity', 'text', 1, 1),
    ('hotel_address', '', 'contact', 'text', 1, 0),
    ('hotel_phone', '', 'contact', 'phone', 1, 0),
    ('hotel_whatsapp', '', 'contact', 'phone', 1, 0),
    ('hotel_email', '', 'contact', 'email', 1, 0),
    ('hotel_maps_query', 'Brazzaville, République du Congo', 'contact', 'text', 1, 1),
    ('check_in_time', '', 'stay', 'time', 1, 0),
    ('check_out_time', '', 'stay', 'time', 1, 0),
    ('restaurant_name', '', 'restaurant', 'text', 1, 0),
    ('restaurant_intro', 'Un aperçu de la table du Brazza Hotel. La carte, les horaires et les services du restaurant seront publiés après validation par l’établissement.', 'restaurant', 'textarea', 1, 0),
    ('about_intro', 'Brazza Hotel est présenté ici comme une adresse contemporaine à Brazzaville. Les éléments historiques et les engagements officiels de l’établissement restent à confirmer.', 'identity', 'textarea', 1, 0),
    ('booking_notice', 'Votre demande est enregistrée puis vérifiée par notre équipe. Elle ne constitue pas une confirmation de disponibilité tant qu’un membre de l’hôtel ne vous a pas répondu.', 'booking', 'textarea', 1, 1),
    ('instagram_url', '', 'social', 'url', 1, 0),
    ('facebook_url', '', 'social', 'url', 1, 0);

INSERT INTO amenities (name, icon, sort_order) VALUES
    ('Climatisation', 'fa-snowflake', 10),
    ('Wi-Fi', 'fa-wifi', 20),
    ('Salle de bain privative', 'fa-bath', 30),
    ('Coffre-fort', 'fa-vault', 40),
    ('Télévision', 'fa-tv', 50),
    ('Petit-déjeuner', 'fa-mug-saucer', 60),
    ('Espace de travail', 'fa-laptop', 70),
    ('Salon privatif', 'fa-couch', 80);

INSERT INTO rooms
    (slug, name, tagline, short_description, description, price_per_night, currency,
     max_occupancy, bed_type, size_sqm, is_featured, is_active, content_status,
     sort_order, meta_title, meta_description)
VALUES
    (
        'chambre-classique',
        'Chambre Classique',
        'Une parenthèse essentielle',
        'Une chambre lumineuse et apaisante, pensée pour les séjours courts à Brazzaville. Contenu de démonstration à valider avec l’hôtel.',
        'Cet aperçu de la chambre Classique présente un espace intime et fonctionnel. Les dimensions, l’agencement, les équipements et le descriptif officiel seront renseignés par l’hôtel. Aucune disponibilité ni aucun tarif ne sont confirmés sur cette page.',
        NULL, 'XAF', 2, 'Lit double', NULL, 1, 1, 'demo', 10,
        'Chambre Classique — Brazza Hotel',
        'Aperçu de démonstration de la catégorie Classique du Brazza Hotel à Brazzaville.'
    ),
    (
        'chambre-superieure',
        'Chambre Supérieure',
        'Plus d’espace, plus de calme',
        'Un aperçu de catégorie supérieure avec un espace de repos généreux. Fiche de démonstration en attente de validation.',
        'Cette fiche illustre la future présentation de la catégorie Supérieure. Le contenu officiel, les équipements, les vues et la capacité exacte seront fournis par l’établissement avant publication. Aucune disponibilité n’est garantie.',
        NULL, 'XAF', 2, 'Lit king size', NULL, 1, 1, 'demo', 20,
        'Chambre Supérieure — Brazza Hotel',
        'Aperçu de démonstration de la catégorie Supérieure du Brazza Hotel à Brazzaville.'
    ),
    (
        'suite-brazza',
        'Suite Brazza',
        'L’art de prolonger le séjour',
        'Une suite de démonstration avec espace salon, imaginée comme une adresse intime au cœur de la ville. À confirmer.',
        'Cette suite est un exemple éditorial. Sa superficie, son ameublement, ses prestations et son tarif officiel ne sont pas encore communiqués. L’équipe de l’hôtel validera chaque information avant la mise en ligne définitive.',
        NULL, 'XAF', 3, 'Lit king size + canapé-lit', NULL, 1, 1, 'demo', 30,
        'Suite Brazza — Brazza Hotel',
        'Aperçu de démonstration de la Suite Brazza. Informations à confirmer.'
    );

INSERT INTO room_images (room_id, file_path, alt_text, is_primary, sort_order) VALUES
    (1, 'assets/images/room-classic.jpg', 'Illustration d’une chambre contemporaine — visuel non contractuel', 1, 10),
    (2, 'assets/images/room-superior.jpg', 'Illustration d’une chambre supérieure — visuel non contractuel', 1, 10),
    (3, 'assets/images/room-suite.jpg', 'Illustration d’une suite avec espace lounge — visuel non contractuel', 1, 10),
    (3, 'assets/images/gallery-detail.jpg', 'Illustration d’un détail d’aménagement — visuel non contractuel', 0, 20);

INSERT INTO room_amenities (room_id, amenity_id) VALUES
    (1, 1), (1, 2), (1, 3), (1, 5),
    (2, 1), (2, 2), (2, 3), (2, 4), (2, 5), (2, 7),
    (3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 6), (3, 7), (3, 8);

INSERT INTO hotel_services (title, description, icon, is_active, content_status, sort_order) VALUES
    (
        'Accueil & conciergerie',
        'Un accompagnement attentif pour faciliter votre arrivée et organiser votre séjour à Brazzaville. Prestation à confirmer.',
        'fa-bell-concierge', 1, 'demo', 10
    ),
    (
        'Table & moments partagés',
        'Un aperçu de l’expérience culinaire proposée au sein de l’hôtel. Carte et horaires à confirmer.',
        'fa-utensils', 1, 'demo', 20
    ),
    (
        'Espaces de détente',
        'Des espaces pensés pour ralentir et se retrouver, entre intérieur et lumière. Équipements à confirmer.',
        'fa-water-ladder', 1, 'demo', 30
    ),
    (
        'Séjour sur mesure',
        'Une équipe à l’écoute pour vos demandes particulières et vos séjours à plusieurs. Services à confirmer.',
        'fa-concierge-bell', 1, 'demo', 40
    );

INSERT INTO gallery_categories (slug, name, sort_order) VALUES
    ('architecture', 'Architecture', 10),
    ('chambres', 'Chambres', 20),
    ('table', 'Table', 30),
    ('details', 'Détails', 40);

INSERT INTO gallery_images
    (category_id, file_path, alt_text, caption, is_featured, is_active, content_status, sort_order)
VALUES
    (1, 'assets/images/hero.jpg', 'Illustration d’un espace extérieur avec piscine au coucher du soleil', 'Lumière de fin de journée — visuel d’illustration', 1, 1, 'demo', 10),
    (1, 'assets/images/lobby.jpg', 'Illustration d’une architecture hôtelière et de palmiers au crépuscule', 'Arrivée au crépuscule — visuel d’illustration', 1, 1, 'demo', 20),
    (2, 'assets/images/room-classic.jpg', 'Illustration d’une chambre d’hôtel contemporaine', 'Chambre — visuel d’illustration', 1, 1, 'demo', 30),
    (2, 'assets/images/room-suite.jpg', 'Illustration d’une chambre avec lumière naturelle', 'Suite — visuel d’illustration', 0, 1, 'demo', 40),
    (2, 'assets/images/gallery-detail.jpg', 'Illustration d’un détail de décoration intérieure', 'Détail — visuel d’illustration', 0, 1, 'demo', 50),
    (3, 'assets/images/restaurant.jpg', 'Illustration d’une table gastronomique dressée', 'Table — visuel d’illustration', 1, 1, 'demo', 60),
    (3, 'assets/images/gallery-food.jpg', 'Illustration d’un restaurant chaleureux', 'Salle — visuel d’illustration', 0, 1, 'demo', 70),
    (4, 'assets/images/gallery-pool.jpg', 'Illustration d’une piscine et de transats', 'Piscine — visuel d’illustration', 1, 1, 'demo', 80),
    (4, 'assets/images/gallery-lounge.jpg', 'Illustration d’un extérieur d’hôtel', 'Extérieurs — visuel d’illustration', 0, 1, 'demo', 90),
    (4, 'assets/images/about.jpg', 'Illustration d’une réception d’hôtel', 'Accueil — visuel d’illustration', 0, 1, 'demo', 100);

-- No administrator is created by this script on purpose.
-- Create the first account securely with:
--   php bin/create-admin.php
