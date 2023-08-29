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
    }



    public function custom_events_request() {
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

        $posts = get_posts($args);

        $posts_return = [];
        foreach ($posts as $post) {
            $post->date = get_post_meta($post->ID,'_data', true);
            $post->link = get_post_meta($post->ID,'_url', true);
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
            'tax_query' => array(
                array(
                    'taxonomy' => 'planos', // Replace with the desired taxonomy name
                    'field' => 'slug', // Use 'slug' if searching by taxonomy slug
                    'terms' => $_GET['plano'], // Replace with the desired term slug
                ),
            ),
        ];
        $current_time = date('H:i');
        $posts = get_posts($args);
        $posts_return = [];
        foreach ($posts as $post) {
            $post->date = get_post_meta($post->ID,'_data', true);
            $post->link = get_post_meta($post->ID,'_url', true);
            $post->zoom = get_post_meta($post->ID,'_zoom', true);
            $post->hora_inicio = get_post_meta($post->ID,'_hora_inicio', true) ? get_post_meta($post->ID,'_hora_inicio', true) : "00:00";

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
                    ),
                    'meta_key' => '_hora_inicio', // Chave para ordenação pela data do evento
                    'orderby' => 'meta_value', // Ordenar pela chave do meta
                    'order' => 'ASC', // Ordenar em ordem crescente (ASC) ou decrescente (DESC)
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'planos', // Replace with the desired taxonomy name
                            'field' => 'slug', // Use 'slug' if searching by taxonomy slug
                            'terms' => $_GET['plano'], // Replace with the desired term slug
                        ),
                    ),
                ];
                $posts_ = get_posts($args_);
                foreach ($posts_ as $post_2) {
                    $post_2->date = get_post_meta($post_2->ID,'_data', true);
                    $post_2->link = get_post_meta($post_2->ID,'_url', true);
                    $post_2->zoom = get_post_meta($post_2->ID,'_zoom', true);
                    $post_2->hora_inicio = get_post_meta($post_2->ID,'_hora_inicio', true) ? get_post_meta($post_2->ID,'_hora_inicio', true) : "00:00";

                    $posts_return[] = $post_2;
                }
            } else {
                $posts_return[] = $post;
            }
        }
        return $posts_return;
    }
}