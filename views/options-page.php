<?php

if(isset($_POST['title'])) {
    update_option('calendar_title', $_POST['title']);
}
if(isset($_POST['color'])) {
    update_option('calendar_color_background', $_POST['color']);
}

if(isset($_POST['color_text'])) {
    update_option('calendar_color_text', $_POST['color_text']);
}

if(isset($_POST['container'])) {
    update_option('calendar_container', $_POST['container']);
}

$title = get_option("calendar_title") ? get_option("calendar_title") : "Próximos conteúdos";
$color = get_option("calendar_color_background") ? get_option("calendar_color_background") : "#37BBC7";
$color_text = get_option("calendar_color_text") ? get_option("calendar_color_text") : "#1E1E1E";
$container = get_option("calendar_container") ? get_option("calendar_container") : "container";

?>

<div class="wrap">
    <h1>Opções da Agenda</h1>
    <!-- Adicione seus campos e configurações aqui -->
    <form action="?post_type=agenda&page=custom-agenda-options" method="POST">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="titulo">Título</label>
                    </th>
                    <td>
                        <input name="title" type="text" id="titulo" class="regular-text" value="<?php echo $title?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="color">Cor de fundo</label>
                    </th>
                    <td>
                        <input name="color" type="color" id="color" class="regular-text" value="<?php echo $color?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="color_text">Cor de texto</label>
                    </th>
                    <td>
                        <input name="color_text" type="color" id="color_text" class="regular-text" value="<?php echo $color_text?>">
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="container">Classe de container</label>
                    </th>
                    <td>
                        <input name="container" type="text" id="container" class="regular-text"  value="<?php echo $container?>">
                    </td>
                </tr>


                <!--<tr>
                    <th scope="row">
                        <label for="color_text">Eventos</label>
                    </th>
                    <td>
                        <fieldset>
                            <label for="users_can_register">
                                <input name="users_can_register" type="checkbox" id="users_can_register" value="1">
                                Mostrar eventos anteriores da data atual
                            </label>
                        </fieldset>
                    </td>
                </tr> -->
                <tr>
                    <th></th>
                    <td>
                        <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Salvar alterações"></p>
                    </td>
                </tr>


                <tr>
                    <th scope="row">
                        <label for="textarea">Script</label>
                    </th>
                    <td>
                        <fieldset>
                            <textarea name="" cols="120" rows="10" readonly="readonly" id="textarea">
<script>
/* Calendar WP Rest Script - Posicionar abaixo do body */
var e=document.createElement("script");
e.src="<?php echo $plugin_directory_url;?>/_script/index.app.js",e.id="script-calendar",document.body.appendChild(e),calendarAttributes = {'titleEvent' : '<?php echo $title?>', 'vueColor' : '<?php echo $color?>', 'textColor' : '<?php echo $color_text?>', 'classContainer' : '<?php echo $container?>'};
</script>

<!-- Posicione o calendário onde deseja que ele renderize -->
<div id="calendar-wp-rest-script"></div></textarea>
                        </fieldset>
                        <span style="font-size: 14px;font-style: italic">
                        Salvar antes de copiar o código
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>