<?php

date_default_timezone_set('America/Sao_Paulo');
$eventos = [
    [
        'start' => '2022-01-01 09:00:00',
        'end' => '2022-01-01 10:30:00',
        'summary' => 'Evento 1',
        'location' => 'Online',
        'link' => 'https://exemplo.com/evento1'
    ],
    [
        'start' => '2022-01-02 14:00:00',
        'end' => '2022-01-02 16:00:00',
        'summary' => 'Evento 2',
        'location' => 'Online',
        'link' => 'https://exemplo.com/evento2'
    ],
    // Adicione mais eventos conforme necessário
];

function gerarICS($eventos) {
    $ics = "BEGIN:VCALENDAR\r\n";
    $ics .= "VERSION:2.0\r\n";
    $ics .= "PRODID:-//Your Company//NONSGML Event Calendar//EN\r\n";

    foreach ($eventos as $evento) {
        $start = date('Ymd\THis\Z', strtotime($evento['start']));
        $end = date('Ymd\THis\Z', strtotime($evento['end']));
        $summary = $evento['summary'];
        $location = $evento['location'];
        $link = $evento['link'];

        $ics .= "BEGIN:VEVENT\r\n";
        $ics .= "DTSTART:$start\r\n";
        $ics .= "DTEND:$end\r\n";
        $ics .= "SUMMARY:$summary\r\n";
        $ics .= "LOCATION:$location\r\n";
        $ics .= "DESCRIPTION:$link\r\n";
        $ics .= "END:VEVENT\r\n";
    }

    $ics .= "END:VCALENDAR\r\n";

    return $ics;
}

$icsContent = gerarICS($eventos);
file_put_contents('eventos.ics', $icsContent);
