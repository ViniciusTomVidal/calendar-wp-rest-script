let vueColorCron, textColorCron, urlBaseCron, classContainerCron, eventsLastCron, titleEventCron, positionEventCron, positionBottom;

class newCron {
    constructor(object) {
        if (object == null) {
            if (typeof calendarAttributes !== 'undefined') {
                object = calendarAttributes;
            }
        }

        if (!object) object = {};

        var scriptElement = document.getElementById('script-cron');
        // Obtém a URL do script carregado
        var scriptSrc = scriptElement.src;
        const scriptBase = scriptSrc.replace("/_script/cron.app.js", "");

        vueColorCron = object.hasOwnProperty("vueColorCron") ? object.vueColor : "#37BBC7";
        textColorCron = object.hasOwnProperty("textColorCron") ? object.textColor : "#1E1E1E";
        classContainerCron = object.hasOwnProperty("classContainerCron") ? object.classContainer : "container";
        urlBaseCron = object.hasOwnProperty("urlBaseCron") ? object.urlBase : scriptBase;
        eventsLastCron = object.hasOwnProperty("eventsLastCron") ? object.eventsLast : false;
        titleEventCron = object.hasOwnProperty("titleEventCron") ? object.titleEvent : "Próximos conteúdos";
        positionEventCron = object.hasOwnProperty("positionEventCron") ? object.positionEventCron : "bottom-right";
        positionBottom = object.hasOwnProperty("positionBottom") ? object.positionBottom : 20;

        // Criação do elemento <script>
        var vueScriptElement = document.createElement('script');
        vueScriptElement.src = scriptBase + '/_frontend/cron/dist/assets/index.js';
        vueScriptElement.type = "module";
        vueScriptElement.crossOrigin = 'anonymous';
        // Evento de carregamento do Vue.js
        vueScriptElement.onload = function () {
            // Após o carregamento do Vue.js, podemos adicionar o link CSS
            // Criação do elemento <link>
            var linkElement = document.createElement('link');
            // Definindo os atributos necessários
            linkElement.rel = 'stylesheet';
            linkElement.href = scriptBase + '/_frontend/cron/dist/assets/index.css';

            // Adicionando o elemento <link> ao head da página
            document.head.appendChild(linkElement);
        };
        // Adicionando o elemento <script> ao final do <body>
        document.body.appendChild(vueScriptElement);
    }
}

new newCron();