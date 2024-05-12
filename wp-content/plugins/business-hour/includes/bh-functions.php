<?php
/*
 * Ajoute un nouveau menu à la Panel de Contrôle Admin
 */

// Ajoute un nouveau lien de menu de niveau supérieur au Panel de Contrôle Admin
function bh_add_admin_menu()
{
    add_menu_page(
        'Ma Première Page', // Titre de la page
        'Gestion horaires', // Texte à afficher sur le lien du menu
        'manage_options', // Capacité requise pour voir le lien
        plugin_dir_path(__FILE__) . 'bh-first-acp-page.php' // Chemin absolu vers le fichier de la page
    );
}

// Fonction pour enregistrer le chargement du fichier CSS
function my_plugin_enqueue_styles()
{
    // Enregistrement du fichier CSS
    wp_enqueue_style('my-plugin-styles', plugin_dir_url(__FILE__) . '../css/styles.css');


}
// Action pour charger les scripts et les styles
add_action('wp_enqueue_scripts', 'my_plugin_enqueue_styles');


require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

// Créez une instance de la classe Bh_calendar_controller
$controller_instance = new Bh_calendar_controller();

// Hook l'action 'admin_menu', exécute la fonction nommée 'bh_add_admin_menu()'
add_action('admin_menu', 'bh_add_admin_menu');

// Assurez-vous que le nom de la fonction correspond à celui que vous ajoutez à l'action
add_action('load-bh-first-acp-page.php', 'display_all_exception_hours');

// Utilisez la méthode bh_register_settings de l'instance du contrôleur comme callback
add_action('admin_init', array($controller_instance, 'bh_register_settings'));

add_action('admin_init', array($controller_instance, 'bh_register_exception_settings'));

add_action('admin_init', array($controller_instance, 'delete_exception_for_establishment_date'));


//// AFFICHER LES HORAIRE DU JOUR DANS LE HEADER /////

// Fonction pour afficher les horaires d'ouverture d'un établissement dans le header
// $establishment : 'galerie' ou 'carrefour'
function display_opening_hours($establishment)
{

    // Inclure le fichier contenant la classe du contrôleur
    require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

    // Instancier le contrôleur
    $controller = new Bh_calendar_controller();

    // Spécifier le fuseau horaire souhaité
    $timezone = new DateTimeZone('Europe/Paris'); // Remplacez 'Europe/Paris' par le fuseau horaire approprié

    // Récupérer l'heure actuelle avec le fuseau horaire spécifié
    $current_time = new DateTime('now', $timezone);

    // Convertir l'objet DateTime en chaîne de caractères pour l'affichage
    $current_day = strtolower($current_time->format('l')); // Récupère le jour actuel en minuscule (par exemple, 'monday')

    // Récupérer les horaires du jour pour l'établissement donné
    $hours_today = $controller->get_hours_for_establishment_today($establishment);

    // Ajouter le bout de JavaScript
    echo '<script>
      $(function() {
          var $winHeight = $(window).height();
          $(".container").height($winHeight);
      });
    </script>';

// Vérifier si l'établissement est fermé aujourd'hui
if ($hours_today['closed']) {
    if ($establishment === 'gallerie') {
        echo '<span class="led-box"><div class="led-red"></div></span><div>La gallerie est fermée.</div>';
    } elseif ($establishment === 'carrefour') {
        echo '<span class="led-box"><div class="led-red"></div></span><div>Carrefour est fermé.</div>';
    }
    return;
}

// Vérifier si l'établissement est fermé à l'heure actuelle
if ($current_time->format('H:i:s') >= $hours_today['closing_time']) {
    if ($establishment === 'gallerie') {
        echo '<span class="led-box"><div class="led-red"></div></span><div>La gallerie est fermée.</div>';
    } elseif ($establishment === 'carrefour') {
        echo '<span class="led-box"><div class="led-red"></div></span><div>Carrefour est fermé.</div>';
    }
    return;
}

// Afficher les horaires normaux de fermeture pour l'établissement
$closing_time = date('H\hi', strtotime($hours_today['closing_time']));
if ($establishment === 'gallerie') {
    echo '<span class="led-box"><div class="led-green"></div></span><div>La gallerie est ouverte jusqu\'à ' . $closing_time . '</div>';
} elseif ($establishment === 'carrefour') {
    echo '<span class="led-box"><div class="led-green"></div></span><div>Carrefour est ouvert jusqu\'à ' . $closing_time . '</div>';
}
}


// Shortcode pour afficher les horaires d'ouverture
function display_business_hour_shortcode()
{
    ob_start(); // Commence le tampon de sortie

    // Appelle la fonction pour afficher les horaires de la galerie
    display_opening_hours("gallerie");
    display_opening_hours("carrefour");


    return ob_get_clean(); // Récupère le contenu du tampon de sortie et le nettoie

}
add_shortcode('business_hour', 'display_business_hour_shortcode');




// Ajouter un widget pour afficher les horaires d'ouverture dans le header
class Business_hour_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(
            'business_hour_widget',
            __('Widget Heure d\'affaires', 'text_domain'),
            array(
                'description' => __('Affiche les horaires d\'affaires dans le header', 'text_domain'),
            )
        );
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        // Affichage des horaires d'ouverture
        echo '<div class="business-hour-widget-container">';
        echo '<div class="business-hour-item gallerie">';
        display_opening_hours('gallerie');
        echo '</div>';
        echo '<div class="business-hour-item carrefour">';
        display_opening_hours('carrefour');
        echo '</div>';
        echo '</div>';
        echo $args['after_widget'];
    }
}

function register_business_hour_widget()
{
    register_widget('Business_hour_Widget');
}
add_action('widgets_init', 'register_business_hour_widget');



//// AFFICHER LES HORAIRES PAR DEFAUT DANS UN TABLEAU /////

// Ajouter un shortcode pour afficher un tableau des horaires d'ouverture

// Shortcode pour afficher les horaires de la Galerie : [gallerie_hours]
function gallerie_hours_shortcode()
{
    ob_start(); // Start output buffering
    ?>
    <div class="gallerie-hours">
        <?php echo display_opening_hours_table('gallerie'); ?>
        <?php echo display_regular_hours('gallerie'); ?>
        <?php echo display_exception_hours_frontend('gallerie'); ?>
    </div>
    <?php
    return ob_get_clean(); // Return the output buffer content and clean the buffer
}
add_shortcode('gallerie_hours', 'gallerie_hours_shortcode');

// Shortcode pour afficher les horaires de Carrefour : [carrefour_hours]
function carrefour_hours_shortcode()
{
    ob_start(); // Start output buffering
    ?>
    <div class="carrefour-hours">
        <?php echo display_opening_hours_table('carrefour'); ?>
        <?php echo display_regular_hours('carrefour'); ?>
        <?php echo display_exception_hours_frontend('carrefour'); ?>
    </div>
    <?php
    return ob_get_clean(); // Return the output buffer content and clean the buffer
}
add_shortcode('carrefour_hours', 'carrefour_hours_shortcode');


/// AFFCHER LES HORAIRES REGULIERS PREVU DANS LES 2 SEMAINES A VENIR SUR LE SITE DANS LE TABLEAU //////

function display_regular_hours($establishment)
{
    // Inclure le fichier contenant la classe du contrôleur
    require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

    // Instancier le contrôleur
    $controller = new Bh_calendar_controller();

    // Récupérer les horaires réguliers pour l'établissement spécifié
    $regular_hours = $controller->get_regular_hours_by_establishment($establishment);

    // Vérifier si des horaires réguliers ont été récupérés
    if (!empty($regular_hours)) {
        // Initialiser une variable pour les horaires du lundi au samedi
        $monday_to_saturday_hours = '';
        // Initialiser une variable pour les horaires du dimanche
        $sunday_hours = '';

        // Parcourir les horaires pour chaque jour de la semaine
        foreach ($regular_hours as $day => $hours) {
            // Si c'est le dimanche
            if ($day === 'dimanche') {
                // Stocker les horaires du dimanche
                $sunday_hours = '<div class="sunday">';
                $sunday_hours .= '<span>Dimanche</span>';
                $sunday_hours .= '<span>' . ($hours['closed'] ? 'Fermé' : $hours['opening_time'] . ' - ' . $hours['closing_time']) . '</span>';
                $sunday_hours .= '</div>';
            } else {
                // Si c'est un jour de la semaine autre que le dimanche
                // Stocker les horaires du lundi au samedi
                $monday_to_saturday_hours = '<div class="week">';
                $monday_to_saturday_hours .= '<span>Du lundi au samedi</span>';
                $monday_to_saturday_hours .= '<span>' . ($hours['closed'] ? 'Fermé' : $hours['opening_time'] . ' - ' . $hours['closing_time']) . '</span>';
                $monday_to_saturday_hours .= '</div>';
            }
        }

        // Afficher les horaires du lundi au samedi et ceux du dimanche
        echo $monday_to_saturday_hours;
        echo $sunday_hours;
    } else {
        echo 'Aucun horaire régulier trouvé pour ' . ucfirst($establishment);
    }
}






//// AFFCHER LES HORAIRES EXCEPTIONNELS PREVU DANS LES 2 SEMAINES A VENIR SUR LE SITE DANS LE TABLEAU //////


function display_exception_hours_frontend($establishment)
{
    // Tableau associatif pour les noms de mois en français
    $french_months = array(
        'January' => 'janvier',
        'February' => 'février',
        'March' => 'mars',
        'April' => 'avril',
        'May' => 'mai',
        'June' => 'juin',
        'July' => 'juillet',
        'August' => 'août',
        'September' => 'septembre',
        'October' => 'octobre',
        'November' => 'novembre',
        'December' => 'décembre'
    );

    // Tableau associatif pour les noms de jours en français
    $french_days = array(
        'Monday' => 'lundi',
        'Tuesday' => 'mardi',
        'Wednesday' => 'mercredi',
        'Thursday' => 'jeudi',
        'Friday' => 'vendredi',
        'Saturday' => 'samedi',
        'Sunday' => 'dimanche'
    );

    // Inclure le fichier contenant la classe du contrôleur
    require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

    // Instancier le contrôleur
    $controller = new Bh_calendar_controller();

    // Récupérer toutes les exceptions pour l'établissement spécifié pour les deux semaines à venir
    $establishment_exceptions = $controller->get_exceptions_for_establishment_next_two_weeks($establishment);

// Vérifier s'il y a des exceptions à afficher
if (!empty($establishment_exceptions[$establishment])) {
    // Ouvrir la balise <ul>
    echo '<ul>';
    // Parcourir chaque exception pour l'établissement spécifié
    foreach ($establishment_exceptions[$establishment] as $exception) {
        // Formater la date en français
        $date_parts = explode('-', $exception->exception_date);
        $formatted_date = $french_days[date('l', strtotime($exception->exception_date))] . ' ' . intval($date_parts[2]) . ' ' . $french_months[date('F', strtotime($exception->exception_date))];

        // Vérifier si l'établissement est La gallerie
        if ($establishment === 'gallerie') {
            // Vérifier si l'établissement est fermé aujourd'hui
            if ($exception->closed == 1) {
                echo '<li>La gallerie sera fermée le ' . $formatted_date . '.</li>';
            } else {
                echo '<li>La gallerie sera ouverte le ' . $formatted_date . '.</li>';
            }
        }
        // Vérifier si l'établissement est Carrefour
        elseif ($establishment === 'carrefour') {
            // Vérifier si l'établissement est fermé aujourd'hui
            if ($exception->closed == 1) {
                echo '<li>Carrefour sera fermé le ' . $formatted_date . '.</li>';
            } else {
                echo '<li>Carrefour sera ouvert le ' . $formatted_date . '.</li>';
            }
        }
    }
    // Fermer la balise <ul>
    echo '</ul>';
} else {
    return;
    // Aucune exception à afficher
    //echo '<p>Aucun horaire exceptionnel prévu pour ' . ucfirst($establishment) . ' dans les deux semaines à venir.</p>';
}
}



function calculateTimeRemaining($timestamp)
{
    $current_time = time();
    $time_diff = $timestamp - $current_time;

    if ($time_diff < 60) {
        return ' moins de 1 minute';
    } elseif ($time_diff < 3600) {
        $minutes = ceil($time_diff / 60); // Utiliser ceil() pour arrondir à la minute supérieure
        return $minutes > 1 ? $minutes . ' minutes' : '1 minute';
    } else {
        $hours = floor($time_diff / 3600);
        $minutes = ceil(($time_diff % 3600) / 60); // Utiliser ceil() pour arrondir à la minute supérieure
        if ($minutes == 0) {
            return $hours > 1 ? $hours . ' heures' : '1 heure';
        } else {
            return $hours > 1 ? $hours . ' heures et ' . $minutes . ' minutes' : '1 heure et ' . $minutes . ' minutes';
        }
    }
}

function calculateNextOpeningTime($opening_time)
{
    // Récupère le timestamp de maintenant
    $current_time = time();

    // Obtient la date et l'heure de demain
    $tomorrow = strtotime('tomorrow');

    // Combine la date de demain avec l'heure d'ouverture pour obtenir la prochaine ouverture
    $next_opening_timestamp = strtotime(date('Y-m-d', $tomorrow) . ' ' . $opening_time);

    // Si l'heure d'ouverture est déjà passée pour aujourd'hui, on considère que c'est pour demain
    if ($current_time > strtotime(date('Y-m-d') . ' ' . $opening_time)) {
        $next_opening_timestamp += 86400; // Ajoute un jour en secondes (86400 secondes dans une journée)
    }

    return $next_opening_timestamp;
}

function display_opening_hours_table($establishment)
{
    // Inclure le fichier contenant la classe du contrôleur
    require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

    // Instancier le contrôleur
    $controller = new Bh_calendar_controller();

    // Spécifier le fuseau horaire souhaité
    $timezone = new DateTimeZone('Europe/Paris'); // Remplacez 'Europe/Paris' par le fuseau horaire approprié

    // Récupérer l'heure actuelle avec le fuseau horaire spécifié
    $current_time = new DateTime('now', $timezone);

    // Convertir l'objet DateTime en chaîne de caractères pour l'affichage
    $current_day = strtolower($current_time->format('l')); // Récupère le jour actuel en minuscule (par exemple, 'monday')

    // Récupérer les horaires du jour pour l'établissement donné
    $hours_today = $controller->get_hours_for_establishment_today($establishment);

    // Vérifier si l'établissement est La gallerie
    if ($establishment === 'gallerie') {
        // Vérifier si l'établissement est fermé aujourd'hui
        if ($hours_today['closed']) {
            echo 'La gallerie est fermée aujourd\'hui.';
        } else {
            // Vérifier si l'établissement est fermé à l'heure actuelle
            if ($current_time->format('H:i:s') >= $hours_today['closing_time']) {
                echo 'La gallerie est fermée.';
            } else {
                // Calculer le temps restant avant la fermeture
                $closing_timestamp = strtotime($hours_today['closing_time']);
                $time_ago = calculateTimeRemaining($closing_timestamp); // Utilise le timestamp de fermeture

                echo ' Vite, vite, vite !!! ' . $time_ago . ' avant la fermeture de La gallerie';
            }
        }
    } elseif ($establishment === 'carrefour') {
        // Vérifier si l'établissement est fermé aujourd'hui
        if ($hours_today['closed']) {
            echo 'Carrefour est fermé aujourd\'hui.';
        } else {
            // Vérifier si l'établissement est fermé à l'heure actuelle
            if ($current_time->format('H:i:s') >= $hours_today['closing_time']) {
                echo 'Carrefour est fermé.';
            } else {
                // Calculer le temps restant avant la fermeture
                $closing_timestamp = strtotime($hours_today['closing_time']);
                $time_ago = calculateTimeRemaining($closing_timestamp); // Utilise le timestamp de fermeture

                echo ' Vite, vite, vite !!! ' . $time_ago . ' avant la fermeture de Carrefour';
            }
        }
    } else {
        echo 'Établissement inconnu.';
    }


    // Déterminer le nom du fichier du logo en fonction de l'état d'ouverture de la galerie
    $logo_file = $hours_today['closed'] ? 'closed.svg' : 'open.svg';
    echo '<img src="/wp-content/plugins/business-hour/assets/' . $logo_file . '" alt="Logo">';
    //echo '<img src="https://gie.clickandigital.com/wp-content/plugins/business-hour/assets/open.svg" alt="Logo">';
}






////// AFFICHAGE SUR LA PAGE ADMIN //////

// Afficher tous les horaires exceptionnels & fermetures exceptionnelles enregistrés sur la page admin 
// function display_all_exception_hours()
// {
//     // Inclure le fichier contenant la classe du contrôleur
//     require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

//     // Instancier le contrôleur
//     $controller = new Bh_calendar_controller();

//     // Tableau associatif pour les noms de mois en français
//     $french_months = array(
//         'January' => 'janvier',
//         'February' => 'février',
//         'March' => 'mars',
//         'April' => 'avril',
//         'May' => 'mai',
//         'June' => 'juin',
//         'July' => 'juillet',
//         'August' => 'août',
//         'September' => 'septembre',
//         'October' => 'octobre',
//         'November' => 'novembre',
//         'December' => 'décembre'
//     );

//     // Tableau associatif pour les noms de jours en français
//     $french_days = array(
//         'Monday' => 'lundi',
//         'Tuesday' => 'mardi',
//         'Wednesday' => 'mercredi',
//         'Thursday' => 'jeudi',
//         'Friday' => 'vendredi',
//         'Saturday' => 'samedi',
//         'Sunday' => 'dimanche'
//     );

//     foreach ($controller->establishments as $establishment) {

//         // Récupérer toutes les exceptions pour les deux établissements pour les deux semaines à venir
//         $exceptions = $controller->get_exceptions_for_establishment_from_today($establishment);

//         if (!empty($exceptions)) {
//             echo '<div class="notice notice-info"><p>Horaires exceptionnels prévus dans les deux semaines à venir :</p><ul>';
//             foreach ($exceptions as $exception) {
//                 // Formater la date en français
//                 $date_parts = explode('-', $exception->exception_date);
//                 $formatted_date = $french_days[date('l', strtotime($exception->exception_date))] . ' ' . intval($date_parts[2]) . ' ' . $french_months[date('F', strtotime($exception->exception_date))];

//                 // Formater les heures au format "19h00"
//                 $opening_time = date('H\hi', strtotime($exception->opening_time));
//                 $closing_time = date('H\hi', strtotime($exception->closing_time));

//                 // Afficher les détails de l'exception
             
// // Afficher les détails de l'exception
// echo '<li>';
// if ($establishment === 'gallerie') {
//     echo 'La gallerie le ' . $formatted_date;
// } elseif ($establishment === 'carrefour') {
//     echo 'Carrefour le ' . $formatted_date;
// }

// if ($exception->closed == 1) {
//     if ($establishment === 'gallerie') {
//         echo ' est fermée exceptionnellement.';
//     } elseif ($establishment === 'carrefour') {
//         echo ' est fermé exceptionnellement.';
//     }
// } else {
//     echo  '' . ' horaires exceptionnels  de ' . $opening_time . ' à ' . $closing_time;
// }

// // Ajouter le formulaire de suppression
// echo '<form method="post">';
// echo '<input type="hidden" name="action" value="delete_exception_for_establishment_date">';
// echo '<input type="hidden" name="exception_date" value="' . $exception->exception_date . '">';
// echo '<input type="hidden" name="establishment" value="' . $exception->establishment . '">';
// echo '<button type="submit">Supprimer</button>';
// echo '</form>';

// echo '</li>';
//             }
//             echo '</ul></div>';
//         } else {
//             // Si aucune exception n'est trouvée pour cet établissement, afficher un message approprié
//             echo '<div class="notice notice-info"><p>Aucun horaire exceptionnel prévu dans les deux semaines à venir pour ' . $establishment . '.</p></div>';
//         }
//     }
// }

// // Assurez-vous que le nom de la fonction correspond à celui que vous ajoutez à l'action
// add_action('admin_menu', 'display_all_exception_hours');

// // Ajoutez la fonction pour le traitement de la suppression d'exception
// function delete_exception()
// {
//     $controller = new Bh_calendar_controller();
//     $controller->delete_exception_for_establishment_date();
//     // Redirigez vers la même page après la suppression
//     wp_redirect($_SERVER['REQUEST_URI']);
//     exit;

// }

// // Liez la fonction au hook approprié
// add_action('admin_post_delete_exception', 'delete_exception_for_establishment_date');


