<script setup>
import $ from "jquery";
import Contagem from "./components/Contagem.vue";
import axios from "axios";
import {onMounted, ref} from "vue";

let minimized = ref(false);
let backgroundColor;
let events;

backgroundColor = ref(vueColorCron);
let valueColor = ref(textColorCron);
let titleEventText = ref(titleEventCron);
let urlBaseApp = ref(urlBase);
let position = ref(positionEventCron);
let positionBottomCron = ref(positionBottom);
let itemEvent = ref(null);

let styleCron = 'background-color:' + backgroundColor.value + "e6";

if (position.value === "bottom-right")
  styleCron += ";bottom:" + positionBottomCron.value + "px";


let plano = ref(false);
const getEvent = () => {
  axios.get(replaceUrlBase(urlBase, '/wp-json/wp-calendar/v1/events/recent?plano=' + plano.value)).then((x) => {
    if(x.data.length > 0) {
      itemEvent.value = x.data[0];
    }

  })
}

const replaceUrlBase = (urlB, add = '') => {
  return urlB.split("wp-content")[0] + add;
}

onMounted(() => {
  $(document).ready(() => {
    const PlanoC = getPlano();
    console.log($(window).innerWidth());

    if($(window).innerWidth() <= 768) {
      minimized.value = true;
    }

    if (PlanoC)
      localStorage.setItem("plano", getPlano());

    plano.value = localStorage.getItem("plano");


    if(plano.value) getEvent();

    setInterval(() => {
      getEvent();
    }, 1000)
  })
})


const getPlano = () => {
// Select all elements with the class ".group-title a"
  const elements = document.querySelectorAll('.list-group .group-title a');

  if(elements.length === 0) return false;

  let iPlano = 0;
// Loop through each element
  for (let i = 0; i < elements.length; i++) {
    const element = elements[i];
    console.log(element.textContent);
    // Check if the element's text content contains the desired text
    if (element.textContent.includes('Pills')) {
      iPlano++;
    }

    if (element.textContent.includes('Mentoring')) {
      iPlano++;
    }
  }

  if (iPlano === 0) {
    return 'light';
  } else if (iPlano === 1) {
    return 'plus';
  } else if (iPlano > 1) {
    return 'pro';
  }

  return false;
}

</script>

<template>
  <section :class="'cron ' + position + ' minimized-' + minimized" :style="styleCron" v-if="plano && itemEvent">
    <span class="click" @click="minimized = !minimized" :style="'color:' + valueColor">X</span>
    <span class="zoom" @click="minimized = !minimized">
      <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 472.4 472.4" width="24"><circle cx="236.2" cy="236.2" fill="#4a8cff" r="236.2"/><path d="m84.65 162.25v111a45.42 45.42 0 0 0 45.6 45.2h161.8a8.26 8.26 0 0 0 8.3-8.2v-111a45.42 45.42 0 0 0 -45.6-45.2h-161.75a8.26 8.26 0 0 0 -8.35 8.2zm226 43.3 66.8-48.8c5.8-4.81 10.3-3.6 10.3 5.1v148.8c0 9.9-5.5 8.7-10.3 5.09l-66.8-48.69z" fill="#fff"/></svg>
    </span>
    <div class="container-cron">
      <Contagem :item-event="itemEvent"/>
    </div>
  </section>
</template>

<style scoped>

.click {
  position: absolute;
  bottom: 12px;
  right: 16px;
  cursor: pointer;
  font-size: 18px;
  font-weight: 800;
  text-transform: lowercase;
}

.minimized-false .zoom {
  display: none;
}

.minimized-true .zoom {
  display: block;
  cursor: pointer;
}

.minimized-true .zoom svg {
  width: 40px;
  height: 40px;
  position: absolute;
  top: 12px;
  left: 12px;
}

.minimized-true {
  animation: blink 2s infinite; /* 1s para um piscar de 1 segundo */
  width: 64px;
  height: 64px;
  border-radius: 50%;
}

.minimized-true .container-cron, .minimized-true .click {
  display: none !important;
}

section {
  padding: 15px 0px;
  border-radius: 14px;
  max-width: 300px;
  width: 100%;
  -webkit-box-shadow: 0px 0px 22px -6px rgba(0, 0, 0, 0.26);
  -moz-box-shadow: 0px 0px 22px -6px rgba(0, 0, 0, 0.26);
  box-shadow: 0px 0px 22px -6px rgba(0, 0, 0, 0.26);
}



section.bottom-right {
  position: fixed;
  bottom: 15px;
  right: 25px;
}

section.top-right {
  position: fixed;
  top: 60px;
  right: 25px;
}

section.top-left {
  position: fixed;
  top: 60px;
  left: 30px;
}



section.bottom-left {
  position: fixed;
  bottom: 20px;
  left: 30px;
}


.cron .container-cron {
  display: flex;
  justify-content: center;
}


@keyframes blink {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
  100% {
    opacity: 1;
  }
}

@media (max-width: 768px) {
  section {
    width: 270px;
  }
}

</style>
