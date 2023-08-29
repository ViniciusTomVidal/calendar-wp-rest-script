<?php

class CalendarAdmin
{
    public function __construct()
    {

        //REGISTRANDO POST TYPE E META BOX
        add_action('init', [$this, 'registrar_post_type_agenda']);
        add_action('add_meta_boxes', [$this, 'agenda_metabox']);
        add_action('save_post_agenda', [$this, 'save_agenda_metabox']);

        //COLUNAS E ORDEM POR DATA E HORA
        add_filter( 'manage_agenda_posts_columns', [$this, 'custom_agenda_columns']);
        add_action( 'manage_agenda_posts_custom_column', [$this, 'custom_agenda_column_content'], 10, 2 );
        add_filter( 'manage_edit-agenda_sortable_columns', [$this, 'custom_agenda_sortable_columns']);
        add_action( 'pre_get_posts', [$this, 'custom_agenda_orderby']);
        add_action('init', [$this, 'registrar_taxonomia_planos']);


        //OPTIONS PAGE
        add_action( 'admin_menu', [$this, 'custom_agenda_options_page']);

        //VERIFICAR SE EXISTE PERMISSAO DE ESCRITA
        add_action( 'admin_init', [$this, 'check_calendar_wp_rest_script_permission']);

    }

    function registrar_post_type_agenda()
    {
        $labels = array(
            'name' => 'Agenda',
            'singular_name' => 'Agenda',
            'menu_name' => 'Agenda',
            'name_admin_bar' => 'Agenda',
            'add_new' => 'Adicionar Novo',
            'add_new_item' => 'Adicionar Novo Item',
            'new_item' => 'Novo Item',
            'edit_item' => 'Editar Item',
            'view_item' => 'Ver Item',
            'all_items' => 'Todos os Itens',
            'search_items' => 'Buscar Itens',
            'parent_item_colon' => 'Item Pai:',
            'not_found' => 'Nenhum item encontrado.',
            'not_found_in_trash' => 'Nenhum item encontrado na lixeira.',
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'agenda'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor'),
            'menu_icon'          => 'dashicons-calendar-alt', // Ícone do post type "Agenda"
        );

        register_post_type('agenda', $args);
    }

    function agenda_metabox()
    {
        add_meta_box(
            'agenda_metabox',
            'Detalhes da Agenda',
            [$this, 'agenda_metabox_callback'],
            'agenda',
            'normal',
            'high'
        );
    }

    function agenda_metabox_callback($post)
    {
        require 'views/meta-box.php';
    }


    function save_agenda_metabox($post_id)
    {
        // Verifica se o usuário tem permissão para editar o post
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Salva os valores dos campos
        if (isset($_POST['data'])) {
            update_post_meta($post_id, '_data', sanitize_text_field($_POST['data']));
        }

        if (isset($_POST['hora_inicio'])) {
            update_post_meta($post_id, '_hora_inicio', sanitize_text_field($_POST['hora_inicio']));
        }

        if (isset($_POST['hora_fim'])) {
            update_post_meta($post_id, '_hora_fim', sanitize_text_field($_POST['hora_fim']));
        }

        if (isset($_POST['url'])) {
            update_post_meta($post_id, '_url', sanitize_text_field($_POST['url']));
        }

        if (isset($_POST['zoom'])) {
            update_post_meta($post_id, '_zoom', sanitize_text_field($_POST['zoom']));
        }
    }

    function custom_agenda_columns( $columns ) {
        unset( $columns['date'] ); // Remove a coluna "Data"

        $new_columns = array(
            'descricao' => "Descrição",
            'data_horario' => 'Data e horário do evento',
        );
        return array_merge( $columns, $new_columns );
    }

    function custom_agenda_column_content( $column_name, $post_id ) {
        if ( $column_name === 'data_horario' ) {
            $data = get_post_meta( $post_id, '_data', true );
            $hora_inicio = get_post_meta( $post_id, '_hora_inicio', true );
            $hora_fim = get_post_meta( $post_id, '_hora_fim', true );

            $data_formatada = date( 'd/m/Y', strtotime( $data ) );
            $hora_inicio_formatada = date( 'H:i', strtotime( $hora_inicio ) );
            $hora_fim_formatada = date( 'H:i', strtotime( $hora_fim ) );

            echo "{$data_formatada}, {$hora_inicio_formatada} - {$hora_fim_formatada}";
        }

        if($column_name == 'descricao') {
            $post_content = get_the_content( $post_id );
            // Remove todas as tags HTML
            $post_content_sem_html = wp_strip_all_tags( $post_content );
            // Imprime o conteúdo sem as tags HTML
            echo $post_content_sem_html;
        }
    }

    function custom_agenda_sortable_columns( $columns ) {
        $columns['data_horario'] = 'data_horario';
        return $columns;
    }

    function custom_agenda_orderby( $query ) {
        if ( ! is_admin() ) {
            return;
        }

        $orderby = $query->get( 'orderby' );

        if ( empty( $orderby ) ) {
            $query->set( 'orderby', array( 'meta_value' => 'DESC', 'meta_value_time' => 'DESC' ) );
            $query->set( 'meta_key', '_data' );
        }

        if ( 'data_horario' === $orderby ) {
            $query->set( 'orderby', array( 'meta_value' => 'DESC', 'meta_value_time' => 'DESC' ) );
            $query->set( 'meta_key', '_data' );
        }
    }



    //options page


    function custom_agenda_options_page() {
        add_submenu_page(
            'edit.php?post_type=agenda',
            'Opções da Agenda',
            'Opções da Agenda',
            'manage_options',
            'custom-agenda-options',
            [$this, 'custom_agenda_options_callback']
        );


        add_submenu_page(
            'edit.php?post_type=agenda',
            'Opções da contagem regressiva',
            'Opções da contagem regressiva',
            'manage_options',
            'custom-cron-options',
            [$this, 'custom_contagem_options_callback']
        );
    }

    function custom_agenda_options_callback() {
        if(isset($_POST['title'])) {
            update_option('calendar_title', $_POST['title']);
        }

        $title = get_option("calendar_title") ? get_option("calendar_title") : "Próximos conteúdos";

        ob_start();
        $plugin_directory_url = plugins_url('', __FILE__);require 'views/options-page.php';
        echo ob_get_clean();
    }


    function custom_contagem_options_callback() {
        if(isset($_POST['title'])) {
            update_option('calendar_title', $_POST['title']);
        }

        $title = get_option("calendar_title") ? get_option("calendar_title") : "Próximos conteúdos";

        ob_start();
        $plugin_directory_url = plugins_url('', __FILE__);require 'views/options-page-cron.php';
        echo ob_get_clean();
    }


    //VERIFICANDO SE O ARQUIVO É EDITÁVEL
    function check_calendar_wp_rest_script_permission() {
        $file_path = WP_CONTENT_DIR . '/uploads/calendar-wp-rest-script/index.app.js';
        $is_writable = is_writable( $file_path );

        if ( ! $is_writable ) {
            //add_action( 'admin_notices', [$this, 'calendar_wp_rest_script_permission_alert']);
        }
    }

    function calendar_wp_rest_script_permission_alert() {
        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>Calendar WP Rest Script:</strong> O arquivo "index.app.js" localizado em "/wp-content/uploads/calendar-wp-rest-script/" não tem permissão de escrita. Verifique as permissões do arquivo.</p>';
        echo '</div>';
    }


    function registrar_taxonomia_planos()
    {
        $labels = array(
            'name' => 'Planos',
            'singular_name' => 'Plano',
            'search_items' => 'Buscar Planos',
            'all_items' => 'Todos os Planos',
            'parent_item' => 'Plano Pai',
            'parent_item_colon' => 'Plano Pai:',
            'edit_item' => 'Editar Plano',
            'update_item' => 'Atualizar Plano',
            'add_new_item' => 'Adicionar Novo Plano',
            'new_item_name' => 'Nome do Novo Plano',
            'menu_name' => 'Planos',
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'planos'),
        );

        register_taxonomy('planos', 'agenda', $args);
    }
}