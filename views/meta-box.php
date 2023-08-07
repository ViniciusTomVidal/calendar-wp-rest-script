<?php // Recupera os valores atuais dos campos, se já existirem
$data = get_post_meta($post->ID, '_data', true);
$hora_inicio = get_post_meta($post->ID, '_hora_inicio', true);
$hora_fim = get_post_meta($post->ID, '_hora_fim', true);
$url = get_post_meta($post->ID, '_url', true);
?>

    <table class="form-table" role="presentation">
        <tbody>
            <tr>
                <th scope="row">
                    <label for="data">Data</label>
                </th>
                <td>
                    <input type="date" name="data" id="data" value="<?php echo esc_attr($data); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="data">Hora de Início</label>
                </th>
                <td>
                    <input type="time" name="hora_inicio" value="<?php echo esc_attr($hora_inicio); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="data">Hora de Fim</label>
                </th>
                <td>
                    <input type="time" name="hora_fim" value="<?php echo esc_attr($hora_fim); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="data">URL para detalhe <span style="font-weight: normal">(opcional)</span></label>
                </th>
                <td>
                    <input type="text" name="url" value="<?php echo esc_attr($url)?>">
                </td>
            </tr>
        </tbody>
    </table>
<?php