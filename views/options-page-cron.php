<?php

if(isset($_POST['title'])) {
    update_option('cron_title', $_POST['title']);
}
if(isset($_POST['color'])) {
    update_option('cron_color_background', $_POST['color']);
}

if(isset($_POST['color_text'])) {
    update_option('cron_color_text', $_POST['color_text']);
}

if(isset($_POST['container'])) {
    update_option('cron_container', $_POST['container']);
}

if(isset($_POST['position'])) {
    update_option('cron_position', $_POST['cron_position']);
}

if(isset($_POST['position-bottom'])) {
    update_option('cron-position-bottom', $_POST['position-bottom']);
}


$title = get_option("cron_title") ? get_option("cron_title") : "Contagem regressiva";
$color = get_option("cron_color_background") ? get_option("cron_color_background") : "#37BBC7";
$color_text = get_option("cron_color_text") ? get_option("cron_color_text") : "#1E1E1E";
$container = get_option("cron_container") ? get_option("cron_container") : "container";
$position = get_option("cron_position") ? get_option("cron_position") : "bottom-right";
$position_bottom = get_option("cron-position-bottom") ? get_option("cron-position-bottom") : "20";

?>

<div class="wrap">
    <h1>Opções da contagem regressiva</h1>
    <!-- Adicione seus campos e configurações aqui -->
    <form action="?post_type=agenda&page=custom-cron-options" method="POST">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        Posição
                    </th>
                    <td>
                        <label for="top-right">
                            <input type="radio" name="posicao" id="top-right" value="top-right" <?php if($position == "top-right") echo 'checked'?>>
                            Topo direita
                        </label>

                        <label for="top-left">
                            <input type="radio" name="posicao" value="top-left" id="top-left" <?php if($position == "top-left") echo 'checked'?>>
                            Topo esquerda
                        </label>

                        <label for="bottom-left">
                            <input type="radio" name="posicao" value="bottom-left" id="bottom-left" <?php if($position == "bottom-left") echo 'checked'?>>
                            Inferior esquerda
                        </label>

                        <label for="bottom-right">
                            <input type="radio" name="posicao" value="bottom-right" id="bottom-right" <?php if($position == "bottom-right") echo 'checked'?>>
                            Inferior direita
                        </label>
                    </td>

                </tr>
                <tr>
                    <th scope="row">
                        Posição em pixels inferior (caso for topo direita)
                    </th>
                    <td>
                        <input type="number" name="position-bottom" value="<?php echo $position_bottom?>">
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
/* Contagem regressiva Script - Posicionar abaixo do body */
var e=document.createElement("script");
e.src="<?php echo $plugin_directory_url;?>/_script/cron.app.js",e.id="script-cron",document.body.appendChild(e),cronAttributes = {'titleEvent' : '<?php echo $title?>', 'vueColor' : '<?php echo $color?>', 'textColor' : '<?php echo $color_text?>', 'classContainer' : '<?php echo $container?>', 'positionEventCron' : '<?php echo $position ?>', 'positionBottom' : <?php echo $position_bottom?>};
</script>

<!-- Posicione a contagem regressiva onde deseja que ele renderize -->
<div id="cron-script"></div></textarea>
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