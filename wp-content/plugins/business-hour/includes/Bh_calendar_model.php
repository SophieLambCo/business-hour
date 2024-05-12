<?php
class Bh_calendar_model
{
    // Insérer ou mettre à jour une entrée de calendrier pour un établissement et un jour de la semaine donnés
    public function insert_or_update_calendar_entry($establishment, $day_of_week, $opening_time, $closing_time, $closed)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar';
        $existing_entry = self::get_calendar_entry($establishment, $day_of_week);

        if ($existing_entry) {
            // Mettre à jour l'entrée existante
            $wpdb->update(
                $table_name,
                array(
                    'opening_time' => $opening_time,
                    'closing_time' => $closing_time,
                    'closed' => $closed,
                ),
                array(
                    'establishment' => $establishment,
                    'day_of_week' => $day_of_week,
                )
            );
        } else {
            // Insérer une nouvelle entrée
            $wpdb->insert(
                $table_name,
                array(
                    'establishment' => $establishment,
                    'day_of_week' => $day_of_week,
                    'opening_time' => $opening_time,
                    'closing_time' => $closing_time,
                    'closed' => $closed,
                )
            );
        }
    }

    // Récupérer une entrée de calendrier pour un établissement et un jour de la semaine donnés
    public function get_calendar_entry($establishment, $day_of_week)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar';
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE establishment = %s AND day_of_week = %s",
            $establishment,
            $day_of_week
        );
        $result = $wpdb->get_row($sql);

        return $result;
    }

    // Compter le nombre d'entrées de calendrier pour un établissement donné
    public function count_calendar_entries($establishment)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar';
        $sql = $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE establishment = %s",
            $establishment
        );
        return $wpdb->get_var($sql);
    }

    // Récupérer les entrées de calendrier pour un établissement donné
    public function get_calendar_entries_for_establishment($establishment)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'calendar';
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE establishment = %s",
            $establishment
        );
        return $wpdb->get_results($sql);
    }



}
