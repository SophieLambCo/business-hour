<?php
/*
Plugin Name: Votre Plugin
Description: Description de votre plugin.
Version: 1.0
Author: Votre Nom
*/
require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

// Créez une instance de la classe Bh_calendar_controller
$controller_instance = new Bh_calendar_controller();


//add_action('admin_init', array($controller_instance, 'delete_exception_for_establishment_date'));
add_action('admin_init', array($controller_instance, 'bh_register_exception_settings'));

// Assurez-vous que le nom de la fonction correspond à celui que vous ajoutez à l'action
add_action('admin_menu', 'display_all_exception_hours');

////// AFFICHAGE SUR LA PAGE ADMIN //////

// Afficher tous les horaires exceptionnels & fermetures exceptionnelles enregistrés sur la page admin 
function display_all_exception_hours()
{
    // Inclure le fichier contenant la classe du contrôleur
    require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

    // Instancier le contrôleur
    $controller = new Bh_calendar_controller();

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

    foreach ($controller->establishments as $establishment) {

        // Récupérer toutes les exceptions pour les deux établissements pour les deux semaines à venir
        $exceptions = $controller->get_exceptions_for_establishment_from_today($establishment);

        if (!empty($exceptions)) {
            echo '<div class="notice notice-info"><p>Horaires exceptionnels prévus dans les deux semaines à venir :</p><ul>';
            foreach ($exceptions as $exception) {
                // Formater la date en français
                $date_parts = explode('-', $exception->exception_date);
                $formatted_date = $french_days[date('l', strtotime($exception->exception_date))] . ' ' . intval($date_parts[2]) . ' ' . $french_months[date('F', strtotime($exception->exception_date))];

                // Formater les heures au format "19h00"
                $opening_time = date('H\hi', strtotime($exception->opening_time));
                $closing_time = date('H\hi', strtotime($exception->closing_time));

                // Afficher les détails de l'exception

                // Afficher les détails de l'exception
                echo '<li>';
                if ($establishment === 'gallerie') {
                    echo 'La gallerie le ' . $formatted_date;
                } elseif ($establishment === 'carrefour') {
                    echo 'Carrefour le ' . $formatted_date;
                }

                if ($exception->closed == 1) {
                    if ($establishment === 'gallerie') {
                        echo ' est fermée exceptionnellement.';
                    } elseif ($establishment === 'carrefour') {
                        echo ' est fermé exceptionnellement.';
                    }
                } else {
                    echo '' . ' horaires exceptionnels de ' . $opening_time . ' à ' . $closing_time;
                }

                // Ajouter le formulaire de suppression
               // echo '<form method="post" action="' . admin_url('admin-post.php') . '">';
                echo '<form method="post">';
                echo '<input type="hidden" name="action" value="delete_exception_for_establishment_date">';
                echo '<input type="hidden" name="exception_date" value="' . $exception->exception_date . '">';
                echo '<input type="hidden" name="establishment" value="' . $exception->establishment . '">';
                echo '<button type="submit">Supprimer</button>';
                echo '</form>';

                echo '</li>';
            }
            echo '</ul></div>';
        } else {
            // Si aucune exception n'est trouvée pour cet établissement, afficher un message approprié
            echo '<div class="notice notice-info"><p>Aucun horaire exceptionnel prévu dans les deux semaines à venir pour ' . $establishment . '.</p></div>';
        }
    }
}

 // Inclure le fichier contenant la classe du contrôleur
 //require_once plugin_dir_path(__FILE__) . 'Bh_calendar_controller.php';

//add_action('admin_init', array($controller_instance, 'delete_exception_for_establishment_date'));

// Modifiez la fonction delete_exception_page pour traiter la demande POST
// function delete_exception_page()
// { echo 'coucou';
   
//         $controller = new Bh_calendar_controller();
//         $controller->delete_exception_for_establishment_date();
    
//     // Redirigez vers la même page après la suppression
//     wp_redirect($_SERVER['REQUEST_URI']);
//     exit;
// }
// Liez la fonction au hook approprié pour traiter la demande POST
//add_action('admin_post_delete_exception_page', 'delete_exception_for_establishment_date');

?>

<div class="wrap">
    <h1>Paramètres d'heure d'ouverture</h1>

    <?php display_all_exception_hours(); ?>

    <!-- Formulaire pour les horaires réguliers -->
    <form method="post" action="">
        <!-- Horaires pour la carrefour -->
        <h2>Gallerie</h2>//
        <label>Lundi</label>
        <input type="text" name="gallerie_lundi_heure_ouverture" placeholder="Heure d'ouverture"
            value="<?php echo esc_attr(get_option('gallerie_lundi_heure_ouverture')); ?>" />
        <input type="text" name="gallerie_lundi_heure_fermeture" placeholder="Heure de fermeture"
            value="<?php echo esc_attr(get_option('gallerie_lundi_heure_fermeture')); ?>" /><br>

        <label>Mardi</label>
        <input type="text" name="gallerie_mardi_heure_ouverture" placeholder="Heure d'ouverture"
            value="<?php echo esc_attr(get_option('gallerie_mardi_heure_ouverture')); ?>" />
        <input type="text" name="gallerie_mardi_heure_fermeture" placeholder="Heure de fermeture"
            value="<?php echo esc_attr(get_option('gallerie_mardi_heure_fermeture')); ?>" /><br>

        <label>Mercredi</label>
        <input type="text" name="gallerie_mercredi_heure_ouverture" placeholder="Heure d'ouverture"
            value="<?php echo esc_attr(get_option('gallerie_mercredi_heure_ouverture')); ?>" />
        <input type="text" name="gallerie_mercredi_heure_fermeture" placeholder="Heure de fermeture"
            value="<?php echo esc_attr(get_option('gallerie_mercredi_heure_fermeture')); ?>" /><br>

        <label>Jeudi</label>
        <input type="text" name="gallerie_jeudi_heure_ouverture" placeholder="Heure d'ouverture"
            value="<?php echo esc_attr(get_option('gallerie_jeudi_heure_ouverture')); ?>" />
        <input type="text" name="gallerie_jeudi_heure_fermeture" placeholder="Heure de fermeture"
            value="<?php echo esc_attr(get_option('gallerie_jeudi_heure_fermeture')); ?>" /><br>

        <label>Vendredi</label>
        <input type="text" name="gallerie_vendredi_heure_ouverture" placeholder="Heure d'ouverture"
            value="<?php echo esc_attr(get_option('gallerie_vendredi_heure_ouverture')); ?>" />
        <input type="text" name="gallerie_vendredi_heure_fermeture" placeholder="Heure de fermeture"
            value="<?php echo esc_attr(get_option('gallerie_vendredi_heure_fermeture')); ?>" /><br>

        <label>Samedi</label>
        <input type="text" name="gallerie_samedi_heure_ouverture" placeholder="Heure d'ouverture"
            value="<?php echo esc_attr(get_option('gallerie_samedi_heure_ouverture')); ?>" />
        <input type="text" name="gallerie_samedi_heure_fermeture" placeholder="Heure de fermeture"
            value="<?php echo esc_attr(get_option('gallerie_samedi_heure_fermeture')); ?>" /><br>

        <label>Dimanche</label>
        <input type="text" name="gallerie_dimanche_heure_ouverture" placeholder="Heure d'ouverture"
            value="<?php echo esc_attr(get_option('gallerie_dimanche_heure_ouverture')); ?>" />
        <input type="text" name="gallerie_dimanche_heure_fermeture" placeholder="Heure de fermeture"
            value="<?php echo esc_attr(get_option('gallerie_dimanche_heure_fermeture')); ?>" /><br>


        <label>Dimanche fermé ?</label>
        <input type="checkbox" name="gallerie_dimanche_ferme" <?php echo (get_option('gallerie_dimanche_ferme') == '1') ? 'checked' : ''; ?> /><br>



        <form method="post" action="">
            <!-- Horaires pour la carrefour -->
            <h2>Carrefour</h2>
            <label>Lundi</label>
            <input type="text" name="carrefour_lundi_heure_ouverture" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('carrefour_lundi_heure_ouverture')); ?>" />
            <input type="text" name="carrefour_lundi_heure_fermeture" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('carrefour_lundi_heure_fermeture')); ?>" /><br>

            <label>Mardi</label>
            <input type="text" name="carrefour_mardi_heure_ouverture" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('carrefour_mardi_heure_ouverture')); ?>" />
            <input type="text" name="carrefour_mardi_heure_fermeture" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('carrefour_mardi_heure_fermeture')); ?>" /><br>

            <label>Mercredi</label>
            <input type="text" name="carrefour_mercredi_heure_ouverture" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('carrefour_mercredi_heure_ouverture')); ?>" />
            <input type="text" name="carrefour_mercredi_heure_fermeture" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('carrefour_mercredi_heure_fermeture')); ?>" /><br>

            <label>Jeudi</label>
            <input type="text" name="carrefour_jeudi_heure_ouverture" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('carrefour_jeudi_heure_ouverture')); ?>" />
            <input type="text" name="carrefour_jeudi_heure_fermeture" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('carrefour_jeudi_heure_fermeture')); ?>" /><br>

            <label>Vendredi</label>
            <input type="text" name="carrefour_vendredi_heure_ouverture" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('carrefour_vendredi_heure_ouverture')); ?>" />
            <input type="text" name="carrefour_vendredi_heure_fermeture" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('carrefour_vendredi_heure_fermeture')); ?>" /><br>

            <label>Samedi</label>
            <input type="text" name="carrefour_samedi_heure_ouverture" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('carrefour_samedi_heure_ouverture')); ?>" />
            <input type="text" name="carrefour_samedi_heure_fermeture" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('carrefour_samedi_heure_fermeture')); ?>" /><br>

            <label>Dimanche</label>
            <input type="text" name="carrefour_dimanche_heure_ouverture" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('carrefour_dimanche_heure_ouverture')); ?>" />
            <input type="text" name="carrefour_dimanche_heure_fermeture" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('carrefour_dimanche_heure_fermeture')); ?>" /><br>


            <label>Dimanche fermé ?</label>
            <input type="checkbox" name="carrefour_dimanche_ferme" <?php echo (get_option('carrefour_dimanche_ferme') == '1') ? 'checked' : ''; ?> /><br>

            <!-- Bouton d'envoi pour les horaires réguliers -->
            <input type="submit" name="save_regular_hours" class="button-primary"
                value="Enregistrer les horaires réguliers">
        </form>

        <!-- Formulaire pour les exceptions -->
        <form method="post" action="">
            <!-- Ajoutez un champ caché pour identifier le traitement -->
            <input type="hidden" name="action" value="bh_register_exception_settings">
            <!-- Exceptions pour la gallerie -->
            <h2>Horaires & fermetures exceptionnelles pour la gallerie</h2>
            <label>Date</label>
            <input type="date" name="gallerie_exception_date" placeholder="YYYY-MM-DD"
                value="<?php echo esc_attr(get_option('gallerie_exception_date')); ?>" /><br>

            <label>Ouverture</label>
            <input type="text" name="gallerie_exception_opening_time" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('gallerie_exception_opening_time')); ?>" /><br>

            <label>Fermeture</label>
            <input type="text" name="gallerie_exception_closing_time" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('gallerie_exception_closing_time')); ?>" /><br>

            <label>Fermé exceptionnellement ?</label>
            <select name="gallerie_exception_closed">
                <option value="0">Non</option>
                <option value="1">Oui</option>
            </select><br>

            <!-- Bouton d'envoi pour les exceptions de la gallerie -->
            <input type="submit" name="save_gallerie_hours" class="button-primary"
                value="Enregistrer les horaires pour la gallerie">
        </form>

        <form method="post" action="">
            <!-- Ajoutez un champ caché pour identifier le traitement -->
            <input type="hidden" name="action" value="bh_register_exception_settings">
            <!-- Exceptions pour la carrefour -->
            <h2>Horaires & fermetures exceptionnelles pour Carrefour</h2>
            <label>Date</label>
            <input type="date" name="carrefour_exception_date" placeholder="YYYY-MM-DD"
                value="<?php echo esc_attr(get_option('carrefour_exception_date')); ?>" /><br>

            <label>Ouverture</label>
            <input type="text" name="carrefour_exception_opening_time" placeholder="Heure d'ouverture"
                value="<?php echo esc_attr(get_option('carrefour_exception_opening_time')); ?>" /><br>

            <label>Fermeture</label>
            <input type="text" name="carrefour_exception_closing_time" placeholder="Heure de fermeture"
                value="<?php echo esc_attr(get_option('carrefour_exception_closing_time')); ?>" /><br>

            <label>Fermé exceptionnellement ?</label>
            <select name="carrefour_exception_closed">
                <option value="0">Non</option>
                <option value="1">Oui</option>
            </select><br>

            <!-- Bouton d'envoi pour les exceptions de la carrefour -->
            <input type="submit" name="save_carrefour_hours" class="button-primary"
                value="Enregistrer les horaires pour la carrefour">
        </form>

</div>