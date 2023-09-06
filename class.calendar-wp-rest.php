<?php
class CalendarWPRest {
    public function __construct() {
        add_action('rest_api_init', [$this, 'custom_rest_api_endpoint']);

    }

    public function custom_rest_api_endpoint() {
        register_rest_route('wp-calendar/v1', '/events/', array(
            'methods' => 'GET',
            'callback' => [$this, 'custom_events_request'],
        ));

        register_rest_route('wp-calendar/v1', '/events/recent/', array(
            'methods' => 'GET',
            'callback' => [$this, 'custom_events_request_recent'],
        ));

        register_rest_route('wp-calendar/v1', '/generate/ics', array(
            'methods' => 'GET',
            'callback' => [$this, 'generate_ics'],
        ));
    }



    public function custom_events_request() {
        setlocale(LC_ALL, 'pt_BR.utf8', 'pt_BR', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        $current_date = date('Y-m-d');
        $month_last = intval($_GET['month']) + 1;
        $year_last = intval($_GET['year']);
        if ($month_last > 12) {
            $month_last = 1;
            $year_last = $year_last + 1;
        }
        $args = [
            'post_type' => 'agenda', // Substitua pelo tipo de post que você deseja buscar
            'posts_per_page' => -1, // Para buscar todos os posts, use -1
            'meta_query' => array(
                array(
                    'key' => '_data', // Nome do campo personalizado (meta)
                    'value' => $current_date, // Data específica após a qual você deseja buscar
                    'compare' => '>=', // Buscar datas maiores ou iguais
                    'type' => 'DATE',
                ),
                array(
                    'key' => '_data', // Nome do campo personalizado (meta)
                    'value' => $_GET['year'].'-'.intval($_GET['month']).'-'.'01', // Data específica após a qual você deseja buscar
                    'compare' => '>=', // Buscar datas maiores ou iguais
                    'type' => 'DATE',
                ),
                array(
                    'key' => '_data', // Nome do campo personalizado (meta)
                    'value' => $year_last.'-'.$month_last.'-'.'01', // Data específica após a qual você deseja buscar
                    'compare' => '<', // Buscar datas maiores ou iguais
                    'type' => 'DATE',
                ),
            ),
            'meta_key' => '_data', // Chave para ordenação pela data do evento
            'orderby' => 'meta_value', // Ordenar pela chave do meta
            'order' => 'ASC', // Ordenar em ordem crescente (ASC) ou decrescente (DESC)
        ];

        $posts = get_posts($args);

        $posts_return = [];
        foreach ($posts as $post) {
            $post->date = get_post_meta($post->ID,'_data', true);
            $post->link = get_post_meta($post->ID,'_url', true);
            $post->hora_inicio = get_post_meta($post->ID,'_hora_inicio', true) ? get_post_meta($post->ID,'_hora_inicio', true) : "00:00";

            $timestamp_sp = strtotime($post->date . ' ' .$post->hora_inicio);
            $timestamp_utc = gmdate('Y-m-d H:i', $timestamp_sp);
            // Formatar a data e hora em UTC para uso no JavaScript
            $datetime_utc = new DateTime($timestamp_utc, new DateTimeZone('UTC'));
            $datetime_js = $datetime_utc->format('Y-m-d\TH:i:s\Z');
            $post->date_utc = $datetime_js;
            $posts_return[] = $post;
        }
        return $posts_return;
    }


    public function custom_events_request_recent() {
        setlocale(LC_ALL, 'pt_BR.utf8', 'pt_BR', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        $current_date = date('Y-m-d');

        $args = [
            'post_type' => 'agenda', // Substitua pelo tipo de post que você deseja buscar
            'posts_per_page' => -1, // Para buscar todos os posts, use -1
            'meta_query' => array(
                array(
                    'key' => '_data', // Nome do campo personalizado (meta)
                    'value' => $current_date, // Data específica após a qual você deseja buscar
                    'compare' => '>=', // Buscar datas maiores ou iguais
                    'type' => 'DATE',
                ),
            ),
            'meta_key' => '_data', // Chave para ordenação pela data do evento
            'orderby' => 'meta_value', // Ordenar pela chave do meta
            'order' => 'ASC', // Ordenar em ordem crescente (ASC) ou decrescente (DESC)
        ];

        if($_GET['plano']) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'planos', // Replace with the desired taxonomy name
                    'field' => 'slug', // Use 'slug' if searching by taxonomy slug
                    'terms' => $_GET['plano'], // Replace with the desired term slug
                ),
            );
        }
        $current_time = date('H:i');
        $posts = get_posts($args);
        $posts_return = [];
        foreach ($posts as $post) {
            $post->date = get_post_meta($post->ID,'_data', true);
            $post->hora_inicio = get_post_meta($post->ID,'_hora_inicio', true) ? get_post_meta($post->ID,'_hora_inicio', true) : "00:00";
            $post->hora_fim = get_post_meta($post->ID,'_hora_fim', true) ? get_post_meta($post->ID,'_hora_fim', true) : "00:00";

            $post->link = get_post_meta($post->ID,'_url', true);
            $post->zoom = get_post_meta($post->ID,'_zoom', true);
            // Converter a data e hora para timestamp em UTC
            $timestamp_sp = strtotime($post->date . ' ' . $post->hora_inicio);
            $timestamp_utc = gmdate('Y-m-d H:i', $timestamp_sp);
            // Formatar a data e hora em UTC para uso no JavaScript
            $datetime_utc = new DateTime($timestamp_utc, new DateTimeZone('UTC'));
            $datetime_js = $datetime_utc->format('Y-m-d\TH:i:s\Z');

            $timestamp_sp_fim = strtotime($post->date . ' ' . $post->hora_fim);
            $timestamp_utc_fim = gmdate('Y-m-d H:i', $timestamp_sp_fim);
            $datetime_utc_fim = new DateTime($timestamp_utc_fim, new DateTimeZone('UTC'));
            $datetime_js_fim = $datetime_utc_fim->format('Y-m-d\TH:i:s\Z');


            // Armazenar a data e hora em UTC formatada para uso no JavaScript
            $post->datetime_utc_js = $datetime_js;
            $post->datetime_utc_js_fim = $datetime_js_fim;



            if($current_date === get_post_meta($post->ID,'_data', true)) {
                $post_in = [];
                foreach ($posts as $post_1) {
                    $post_in[] =$post_1->ID;
                }
                $args_ = [
                    'post_type' => 'agenda', // Substitua pelo tipo de post que você deseja buscar
                    'post__in' => $post_in, // Para buscar todos os posts, use -1
                    'meta_query' => array(
                        array(
                            'key' => '_hora_inicio', // Nome do campo personalizado (meta)
                            'value' => $current_time, // Data específica após a qual você deseja buscar
                            'compare' => '>=', // Buscar datas maiores ou iguais
                            'type' => 'TIME',
                        ),
                        array(
                            'key' => '_data', // Nome do campo personalizado (meta)
                            'value' => $current_date, // Data específica após a qual você deseja buscar
                            'compare' => '=', // Buscar datas maiores ou iguais
                            'type' => 'DATE',
                        ),
                    ),
                    'meta_key' => '_hora_inicio', // Chave para ordenação pela data do evento
                    'orderby' => 'meta_value', // Ordenar pela chave do meta
                    'order' => 'ASC', // Ordenar em ordem crescente (ASC) ou decrescente (DESC)
                ];


                if($_GET['plano']) {
                    $args_['tax_query'] = array(
                        array(
                            'taxonomy' => 'planos', // Replace with the desired taxonomy name
                            'field' => 'slug', // Use 'slug' if searching by taxonomy slug
                            'terms' => $_GET['plano'], // Replace with the desired term slug
                        ),
                    );
                }

                $posts_ = get_posts($args_);
                foreach ($posts_ as $post_2) {
                    $post_2->date = get_post_meta($post_2->ID,'_data', true);
                    $post_2->link = get_post_meta($post_2->ID,'_url', true);
                    $post_2->zoom = get_post_meta($post_2->ID,'_zoom', true);
                    $post_2->hora_inicio = get_post_meta($post_2->ID,'_hora_inicio', true) ? get_post_meta($post_2->ID,'_hora_inicio', true) : "00:00";
                    $post_2->hora_fim = get_post_meta($post_2->ID,'_hora_fim', true) ? get_post_meta($post_2->ID,'_hora_fim', true) : "00:00";

                    $timestamp_sp = strtotime($post_2->date . ' ' . $post_2->hora_inicio);
                    $timestamp_utc = gmdate('Y-m-d H:i', $timestamp_sp);
                    // Formatar a data e hora em UTC para uso no JavaScript
                    $datetime_utc = new DateTime($timestamp_utc, new DateTimeZone('UTC'));
                    $datetime_js = $datetime_utc->format('Y-m-d\TH:i:s\Z');

                    $timestamp_sp_fim = strtotime($post_2->date . ' ' . $post_2->hora_fim);
                    $timestamp_utc_fim = gmdate('Y-m-d H:i', $timestamp_sp_fim);
                    $datetime_utc_fim = new DateTime($timestamp_utc_fim, new DateTimeZone('UTC'));
                    $datetime_js_fim = $datetime_utc_fim->format('Y-m-d\TH:i:s\Z');


                    // Armazenar a data e hora em UTC formatada para uso no JavaScript
                    $post->datetime_utc_js = $datetime_js;
                    $post->datetime_utc_js_fim = $datetime_js_fim;

                    $posts_return[] = $post_2;
                }
            } else {
                $posts_return[] = $post;
            }
        }
        return $posts_return;
    }

    public function gerarICS($eventos) {
        $ics = "BEGIN:VCALENDAR\r\n";
        $ics .= "VERSION:2.0\r\n";
        $ics .= "PRODID:-//Oxygen Club//PRO//EN\r\n";
        $ics .= "X-WR-CALNAME:Oxygen Club | Pro\r\n";

        foreach ($eventos as $evento) {

            $timestamp_sp = strtotime($evento->date . ' ' . $evento->hora_inicio);
            $timestamp_utc = gmdate('Y-m-d H:i', $timestamp_sp);
            // Formatar a data e hora em UTC para uso no JavaScript
            $datetime_utc = new DateTime($timestamp_utc, new DateTimeZone('UTC'));
            $datetime_js = $datetime_utc->format('Ymd\THis\Z');

            $timestamp_sp_fim = strtotime($evento->date . ' ' . $evento->hora_fim);
            $timestamp_utc_fim = gmdate('Y-m-d H:i', $timestamp_sp_fim);
            $datetime_utc_fim = new DateTime($timestamp_utc_fim, new DateTimeZone('UTC'));
            $datetime_js_fim = $datetime_utc_fim->format('Ymd\THis\Z');


            $start = $datetime_js;
            $end = $datetime_js_fim;
            $summary = $evento->post_title;
            $location = $evento->zoom;
            $description = $evento->post_content;

            $ics .= "BEGIN:VEVENT\r\n";
            $ics .= "DTSTART:$start\r\n";
            $ics .= "DTEND:$end\r\n";
            $ics .= "SUMMARY:$summary\r\n";
            $ics .= "LOCATION:$location\r\n";
            $ics .= "DESCRIPTION:$description\r\n";
            $ics .= "END:VEVENT\r\n";
        }

        $ics .= "END:VCALENDAR\r\n";

        return $ics;
    }
    public function generate_ics() {
        // Defina o timezone desejado

        // Cabeçalho para download do arquivo .ics
        header('Content-Type: text/calendar');
        header('Content-Disposition: attachment; filename="evento.ics"');
        date_default_timezone_set('America/Sao_Paulo');
        $eventos = $this->custom_events_request_recent();
        $icsContent = $this->gerarICS($eventos);
        echo $icsContent;
        exit();
    }
}