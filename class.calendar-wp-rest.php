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
        $current_date = date('Y-m-d');

        $args = [
            'post_type' => 'agenda', // Substitua pelo tipo de post que você deseja buscar
            'posts_per_page' => 1, // Para buscar todos os posts, use -1
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

        $posts = get_posts($args);
        $posts_return = [];
        foreach ($posts as $post) {
            $post->date = get_post_meta($post->ID,'_data', true);
            $post->link = get_post_meta($post->ID,'_url', true);
            $post->zoom = get_post_meta($post->ID,'_zoom', true);
            $post->hora_inicio = get_post_meta($post->ID,'_hora_inicio', true) ? get_post_meta($post->ID,'_hora_inicio', true) : "00:00";
            $posts_return[] = $post;
        }
        return $posts_return;
    }
}