let vueColor, textColor, urlBase, classContainer, eventsLast, titleEvent;

class newCalendar {
    constructor(object) {
        if (object == null) {
            if (typeof calendarAttributes !== 'undefined') {
                object = calendarAttributes;
            }
        }

        if (!object) object = {};

        var scriptElement = document.getElementById('script-calendar');
        // Obtém a URL do script carregado
        var scriptSrc = scriptElement.src;
        const scriptBase = scriptSrc.replace("/_script/index.app.js", "");

        vueColor = object.hasOwnProperty("vueColor") ? object.vueColor : "#37BBC7";
        textColor = object.hasOwnProperty("textColor") ? object.textColor : "#1E1E1E";
        classContainer = object.hasOwnProperty("classContainer") ? object.classContainer : "container";
        urlBase = object.hasOwnProperty("urlBase") ? object.urlBase : scriptBase;
        eventsLast = object.hasOwnProperty("eventsLast") ? object.eventsLast : false;
        titleEvent = object.hasOwnProperty("titleEvent") ? object.titleEvent : "Próximos conteúdos";

        // Criação do elemento <script>
        var vueScriptElement = document.createElement('script');
        vueScriptElement.src = scriptBase + '/_frontend/calendar-wp-rest-script-vue/dist/assets/index.js';
        vueScriptElement.type = "module";
        vueScriptElement.crossOrigin = 'anonymous';
        // Evento de carregamento do Vue.js
        vueScriptElement.onload = function () {
            // Após o carregamento do Vue.js, podemos adicionar o link CSS
            // Criação do elemento <link>
            var linkElement = document.createElement('link');
            // Definindo os atributos necessários
            linkElement.rel = 'stylesheet';
            linkElement.href = scriptBase + '/_frontend/calendar-wp-rest-script-vue/dist/assets/index.css';

            // Adicionando o elemento <link> ao head da página
            document.head.appendChild(linkElement);
        };
        // Adicionando o elemento <script> ao final do <body>
        document.body.appendChild(vueScriptElement);
    }
}

new newCalendar();