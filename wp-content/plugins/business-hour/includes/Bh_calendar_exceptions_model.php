<?php
class Bh_calendar_exceptions_model
{
    // Insérer ou mettre à jour une exception de calendrier pour un établissement et une date donnée
    public function insert_or_update_exception($establishment, $exception_date, $opening_time, $closing_time, $closed)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar_exceptions';
        $existing_exception = self::get_exception($establishment, $exception_date);

        if ($existing_exception) {
            // Mettre à jour l'exception existante
            $wpdb->update(
                $table_name,
                array(
                    'opening_time' => $opening_time,
                    'closing_time' => $closing_time,
                    'closed' => $closed,
                ),
                array(
                    'establishment' => $establishment,
                    'exception_date' => $exception_date,
                )
            );
        } else {
            // Insérer une nouvelle exception
            $wpdb->insert(
                $table_name,
                array(
                    'establishment' => $establishment,
                    'exception_date' => $exception_date,
                    'opening_time' => $opening_time,
                    'closing_time' => $closing_time,
                    'closed' => $closed,
                )
            );
        }
    }

    // Supprimer une exception de calendrier pour un établissement et une date donnée
    public function delete_exception($establishment, $exception_date)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar_exceptions';
        $wpdb->delete(
            $table_name,
            array(
                'establishment' => $establishment,
                'exception_date' => $exception_date,
            )
        );
    }

    // Récupérer toutes les exceptions de calendrier pour tous les établissements
    public function get_all_exceptions()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar_exceptions';
        $sql = "SELECT * FROM $table_name";
        return $wpdb->get_results($sql);
    }

    // Récupérer toutes les exceptions de calendrier pour un établissement donné
    public function get_exceptions_for_establishment($establishment)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar_exceptions';
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE establishment = %s",
            $establishment
        );
        return $wpdb->get_results($sql);
    }

    // Récupérer une exception de calendrier pour un établissement et une date donnée
    public function get_exception($establishment, $exception_date)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar_exceptions';
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE establishment = %s AND exception_date = %s",
            $establishment,
            $exception_date,
        );
        return $wpdb->get_row($sql);
    }

    // Récupérer toutes les exceptions pour un établissement donné dans les deux prochaines semaines
    public function get_exceptions_next_two_weeks($establishment)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar_exceptions';
        $today_date = date('Y-m-d'); // Récupérer la date d'aujourd'hui au format YYYY-MM-DD
        $two_weeks_future = date('Y-m-d', strtotime('+2 weeks')); // Date dans deux semaines au format YYYY-MM-DD

        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE establishment = %s AND exception_date BETWEEN %s AND %s",
            $establishment,
            $today_date,
            $two_weeks_future
        );
        return $wpdb->get_results($sql);
    }


    // Récupérer toutes les exceptions pour un établissement donné à partir d'aujourd'hui
    public function get_exceptions_from_date($establishment, $date)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar_exceptions';
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE establishment = %s AND exception_date >= %s",
            $establishment,
            $date
        );
        return $wpdb->get_results($sql);
    }

}
