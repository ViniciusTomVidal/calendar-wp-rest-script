<script>
import {defineComponent, onMounted, ref} from 'vue';

export default defineComponent({
  props: {
    itemEvent: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    // Access the prop value using props.myProp
    // You can also use reactive or ref to create reactive data
    // Example:
    let valueColor = ref(textColorCron);
    const targetDate = new Date(props.itemEvent.date + 'T' + props.itemEvent.hora_inicio + ":01"); // Replace with your target date and time
    const currentDate = ref(new Date());
    const timeRemaining = ref(getTimeRemaining());

    // Calculate the time remaining until the target date and time
    function getTimeRemaining() {
      const totalSeconds = Math.floor((targetDate - currentDate.value) / 1000);
      const days = Math.floor(totalSeconds / (3600 * 24));
      const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
      const minutes = Math.floor((totalSeconds % 3600) / 60);
      const seconds = Math.floor(totalSeconds % 60);

      return {
        days,
        hours,
        minutes,
        seconds
      };
    }

    // Update the countdown timer every second
    onMounted(() => {
      setInterval(() => {
        currentDate.value = new Date();
        timeRemaining.value = getTimeRemaining();
      }, 1000);
    });

    return {
      timeRemaining,
      valueColor
    };
  }
});
</script>

<template>
  <div class="contagem" :style="'color:' + valueColor">
    <h2 class="titulo">
      {{itemEvent.post_title}}
    </h2>
    <div class="flex">
        <div class="dias">
          <span>{{ timeRemaining.days }}</span>
          Dias
        </div>

        <div class="horas">
          <span>{{ timeRemaining.hours }}</span>
          Horas
        </div>

        <div class="minutos">
          <span>{{ timeRemaining.minutes }}</span>
          Minutos
        </div>

      <div class="minutos" style="min-width: 52px">
        <span>{{ timeRemaining.seconds }}</span>
        Segundos
      </div>
    </div>

    <div :style="itemEvent.zoom ? '' : 'opacity:0;pointer-events:none'">
      <a class="flex-a" :href="itemEvent.zoom" target="_blank" :style="'color:' + valueColor">
        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 472.4 472.4" width="24"><circle cx="236.2" cy="236.2" fill="#4a8cff" r="236.2"/><path d="m84.65 162.25v111a45.42 45.42 0 0 0 45.6 45.2h161.8a8.26 8.26 0 0 0 8.3-8.2v-111a45.42 45.42 0 0 0 -45.6-45.2h-161.75a8.26 8.26 0 0 0 -8.35 8.2zm226 43.3 66.8-48.8c5.8-4.81 10.3-3.6 10.3 5.1v148.8c0 9.9-5.5 8.7-10.3 5.09l-66.8-48.69z" fill="#fff"/></svg>
        Entre pelo Zoom
      </a>
    </div>
  </div>
</template>

<style scoped>

.flex-a {
  margin-top: 12px;
  text-decoration: none !important;
  display: flex;
  align-items: center;
  color: black;
  font-weight: 500;
  gap: 8px;
  font-size: 11px;
  text-transform: uppercase;
}

.flex-a svg {
  width: 24px;
  height: auto;
}
.flex {
  display: flex;
  justify-content: space-evenly;
  gap: 12px;
  > div {
    display: flex;
    flex-direction: column;
    align-items: center;
    font-size: 10px;
    font-weight: 700;
  }
}

h2 {
  font-size: 18px;
  line-height: normal;
  margin-bottom: 18px;
  font-weight: 800;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.contagem {
  width: 100%;
  padding: 0px 15px;
}


span {
  font-weight: bold;
  margin-bottom: 6px;
  font-size: 18px;
  display: block;
  background-color: white;
  padding: 8px 16px;
  color: #212529;
  border-radius: 8px;
}

@media (max-width: 768px) {
  h2 {
    font-size: 14px;
    margin-bottom: 10px;
  }

  .flex-a {
    margin-top: 10px;
  }

  .flex {
    gap: 8px;
  }
}
</style>