<?php
require_once (plugin_dir_path(__FILE__) . 'Bh_calendar_model.php');
require_once (plugin_dir_path(__FILE__) . 'Bh_calendar_exceptions_model.php');


class Bh_calendar_controller
{
    private $calendar_model;
    private $exceptions_model;
    public $establishments;

    public $days_of_week;

    public function __construct()
    {
        $this->calendar_model = new Bh_calendar_model();
        $this->exceptions_model = new Bh_calendar_exceptions_model();
        $this->establishments = ['gallerie', 'carrefour'];
        $this->days_of_week = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
    }

    // ENREGISTRES LES HORAIRES REGULIERS POUR LES 2 ETEBLISSEMENTS
    function bh_register_settings()
    {
        if (isset($_POST['save_regular_hours'])) {

            foreach ($this->establishments as $establishment) {
                // Enregistrer les horaires pour la Gallerie
                foreach ($this->days_of_week as $day) {
                    // Récupérer l'heure d'ouverture et de fermeture
                    $opening_time = sanitize_text_field($_POST[$establishment . '_' . $day . '_heure_ouverture']);
                    $closing_time = sanitize_text_field($_POST[$establishment . '_' . $day . '_heure_fermeture']);

                    // Vérifier si l'établissement est fermé le dimanche
                    $closed = isset($_POST[$establishment . '_' . $day . '_ferme']) && $_POST[$establishment . '_dimanche_ferme'] == 'on' ? 1 : 0;


                    // Enregistrer les horaires avec l'information de fermeture le dimanche
                    $this->calendar_model->insert_or_update_calendar_entry($establishment, ucfirst($day), $opening_time, $closing_time, $closed);

                }

            }
        }
    }

    // RECUPERE LES HORAIRES REGULIERS POUR UN ETABLISSEMENT SPECIFIQUE
function get_regular_hours_by_establishment($establishment)
{
    //$calendar_model = new Bh_calendar_model();
    $days_of_week = array('lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');
    $regular_hours = array();

    foreach ($days_of_week as $day) {
        $calendar_entry = $this->calendar_model->get_calendar_entry($establishment, ucfirst($day));

        if ($calendar_entry) {
            $regular_hours[$day]['opening_time'] = date('H\hi', strtotime($calendar_entry->opening_time));
            $regular_hours[$day]['closing_time'] = date('H\hi', strtotime($calendar_entry->closing_time));
            $regular_hours[$day]['closed'] = $calendar_entry->closed;
        } else {
            // Si aucune entrée n'est trouvée pour ce jour, initialisez les valeurs par défaut
            $regular_hours[$day]['opening_time'] = '';
            $regular_hours[$day]['closing_time'] = '';
            $regular_hours[$day]['closed'] = 0; // Par défaut, le jour n'est pas fermé
        }
    }

    // Récupérer également l'information de fermeture le dimanche
    $regular_hours['dimanche']['closed'] = $this->calendar_model->get_calendar_entry($establishment, 'Dimanche')->closed;

    return $regular_hours;
}




    // ENREGISTRE LES HORAIRES IRREGULIERS POUR LES DEUX ETABLISSEMENTS
    function bh_register_exception_settings()
    {

        if (!isset($_POST['save_carrefour_hours']) && !isset($_POST['save_gallerie_hours'])) {
            return;
        }
        if (isset($_POST['save_carrefour_hours'])) {
            $establishment = 'carrefour';
        }
        if (isset($_POST['save_gallerie_hours'])) {
            $establishment = 'gallerie';
        }
        $establishment = sanitize_text_field($establishment);
        if (!isset($_POST[$establishment . '_exception_date'])) {
            return;
        }


        $exception_date = sanitize_text_field($_POST[$establishment . '_exception_date']);

        // Vérifier si une exception existe déjà pour cette date et cet établissement
        $existing_exception = $this->exceptions_model->get_exception($establishment, $exception_date);

        $opening_time = sanitize_text_field($_POST[$establishment . '_exception_opening_time']);
        $closing_time = sanitize_text_field($_POST[$establishment . '_exception_closing_time']);
        $closed = sanitize_text_field($_POST[$establishment . '_exception_closed']);

        echo $opening_time;
        echo  $closing_time;


        if ($existing_exception && $closed) {
            // Mettre à jour l'exception existante
            $this->exceptions_model->insert_or_update_exception($establishment, $exception_date, $opening_time, $closing_time, $closed);
        } else {
            // Insérer une nouvelle exception
            $this->exceptions_model->insert_or_update_exception($establishment, $exception_date, $opening_time, $closing_time, $closed);
        }


    }

    public function delete_exception_for_establishment_date()
    {       echo 'coucou';
        // Vérifie si la requête est une requête POST valide
        if (isset($_POST['action'], $_POST['exception_date']) && $_POST['action'] === 'delete_exception_page') {
            // Ajoutez des messages de débogage pour inspecter les valeurs de $_POST['action'] et $_POST['exception_date']

            $exception_date = sanitize_text_field($_POST['exception_date']);
            // Assurez-vous que $establishment est récupéré correctement depuis le formulaire, par exemple :
            $establishment = sanitize_text_field($_POST['establishment']);


            // Supprimez l'exception de la base de données
            $this->exceptions_model->delete_exception($establishment, $exception_date);
        
        }
    }




    //RECUPERE TOUTE LES EXCEPTIONS POUR UN ETABLISSEMENT DONNE A PARTIR DE LA DATE DU JOURS
    function get_exceptions_for_establishment_from_today($establishment)
    {
        $establishment = sanitize_text_field($establishment); // Nettoyer les données entrantes
        $date = date('Y-m-d'); // Récupérer la date d'aujourd'hui au format YYYY-MM-DD

        //$exceptions_model = new Bh_calendar_exceptions_model();
        return $this->exceptions_model->get_exceptions_from_date($establishment, $date);

    }


    // RECUPERE TOUTES LES EXCEPTIONS POUR UN ETABLISSEMENT DONNE A PARTIR DE LA DATE DU JOUR JUSQU'A DANS DEUX SEMAINES
    function get_exceptions_for_establishment_next_two_weeks($establishment)
    {
        $establishment_exceptions = array(); // Tableau pour stocker les exceptions pour chaque établissement

        // Vérifier la disponibilité du modèle d'exceptions
        if (!$this->exceptions_model) {
            // Gérer l'erreur: modèle d'exceptions non disponible
            return false;
        }

        $establishment = sanitize_text_field($establishment); // Nettoyer les données entrantes

        // Récupérer les exceptions pour cet établissement dans les deux prochaines semaines
        $exceptions = $this->exceptions_model->get_exceptions_next_two_weeks($establishment);

        // Stocker les exceptions pour cet établissement
        $establishment_exceptions[$establishment] = $exceptions;
        // }

        return $establishment_exceptions;
    }


    // RECUPERE LES HORAIRES DU JOUR POUR UN ETABLISSEMENT DONNE EN PRENANT EN COMPTE LES HORAIRES REGULIERS ET LES EXCEPTIONS
    function get_hours_for_establishment_today($establishment)
    {
        // Définir le fuseau horaire
        date_default_timezone_set('Europe/Paris');

        // Tableau de correspondance des jours de la semaine

        $jours_semaine_fr = array(
            'monday' => 'lundi',
            'tuesday' => 'mardi',
            'wednesday' => 'mercredi',
            'thursday' => 'jeudi',
            'friday' => 'vendredi',
            'saturday' => 'samedi',
            'sunday' => 'dimanche'
        );

        $establishment = sanitize_text_field($establishment); // Nettoyer les données entrantes
        $day_of_week = $jours_semaine_fr[strtolower(date('l'))]; // Récupérer le jour de la semaine au format texte (lundi, mardi, ...)
        $current_time = date('H:i:s'); // Récupérer l'heure actuelle au format HH:MM:SS
        $current_date = date('Y-m-d'); // Récupérer la date actuelle au format YYYY-MM-DD

        // Récupérer les horaires réguliers pour cet établissement et ce jour de la semaine
        $regular_hours_entry = $this->calendar_model->get_calendar_entry($establishment, $day_of_week);

        // Récupérer les exceptions pour cet établissement et la date d'aujourd'hui
        $exception_entry = $this->exceptions_model->get_exception($establishment, $current_date);

        // Si une exception existe pour aujourd'hui, l'utiliser
        if ($exception_entry) {
            return array(
                'opening_time' => $exception_entry->opening_time,
                'closing_time' => $exception_entry->closing_time,
                'closed' => $exception_entry->closed,
            );
        }

        // Sinon, retourner les horaires réguliers si disponibles
        if ($regular_hours_entry) {
            // Vérifier si l'établissement est fermé pour ce jour de la semaine
            if ($regular_hours_entry->closed == 1) {
                return array(
                    'opening_time' => '',
                    'closing_time' => '',
                    'closed' => true,
                );
            } else {
                // Vérifier si l'établissement est actuellement ouvert
                if ($regular_hours_entry->opening_time <= $current_time && $current_time < $regular_hours_entry->closing_time) {
                    return array(
                        'opening_time' => $regular_hours_entry->opening_time,
                        'closing_time' => $regular_hours_entry->closing_time,
                        'closed' => false,
                    );
                } else {
                    return array(
                        'opening_time' => $regular_hours_entry->opening_time,
                        'closing_time' => $regular_hours_entry->closing_time,
                        'closed' => true, // L'établissement est fermé en dehors de ces heures
                    );
                }
            }
        } else {
            // Si aucun horaire régulier n'est défini pour ce jour de la semaine, retourner des valeurs par défaut
            return array(
                'opening_time' => '',
                'closing_time' => '',
                'closed' => true,
            );
        }
    }

}

