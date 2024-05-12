<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Business hour
 * Plugin URI:        https://www.clickandigital.com/
 * Description:       The best plugin to manage hour
 * Version:           1.0.0
 * Author:            Click and Digital
 * Author URI:        https://www.clickandigital.com/
 * Text Domain:        business-hour
 * Domain Path:       /i18n/languages
 */


// Inclure les fichiers nécessaires
require_once plugin_dir_path(__FILE__) . 'includes/bh-functions.php'; // Inclure les fonctions génériques

require_once plugin_dir_path(__FILE__) . 'includes/bh-first-acp-page.php';

// Assurez-vous que WordPress est chargé
if (!defined('ABSPATH'))
    exit;

// Inclure le fichier d'upgrade de WordPress
require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
/**
 * Crée une nouvelle table dans la base de données pour stocker les heures d'exception.
 */
function business_hour_install_table()
{
 
    global $wpdb;
    $table_name = $wpdb->prefix . 'calendar_exceptions';


    // Vérifie si la table existe déjà
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        // Requête SQL pour créer la table

        $sql = "CREATE TABLE $table_name (
            establishment VARCHAR(50) NOT NULL,
            exception_date DATE NOT NULL,
            opening_time TIME NOT NULL,
            closing_time TIME NOT NULL,
            closed TINYINT(1) NOT NULL,
            PRIMARY KEY (establishment, exception_date)
        )";
      
        // Exécute la requête SQL et crée la table
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . 'calendar';
    // Vérifie si la table existe déjà
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        // Requête SQL pour créer la table

        $sql = "CREATE TABLE $table_name (
                establishment VARCHAR(50) NOT NULL,
                day_of_week VARCHAR(25)  NOT NULL,
                opening_time TIME NOT NULL,
                closing_time TIME NOT NULL,
                closed TINYINT(1) NOT NULL,
                PRIMARY KEY (establishment, day_of_week)
            )";
  
        // Exécute la requête SQL et crée la table
        dbDelta($sql);
    }
}

// Appeler la fonction lors de l'activation du plugin
register_activation_hook(__FILE__, 'business_hour_install_table');

