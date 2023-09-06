<script setup>
import Calendar from './components/Calendar.vue'
import axios from "axios";
import {onMounted, ref, watch} from "vue";

let backgroundColor;
let classContainerText;

backgroundColor = ref(vueColor);
classContainerText = ref(classContainer);
let valueColor = ref(textColor);
let titleEventText = ref(titleEvent);

let events = ref([]);
let month = ref((new Date()).getMonth() + 1);
let year = ref((new Date()).getFullYear());
let loading = ref(false);
let eventsElement = ref(null);
let elementFocus = ref(0);


const getEvents = () => {
  axios.get(replaceUrlBase(urlBase, '/wp-json/wp-calendar/v1/events?month=' + month.value + "&year=" + year.value)).then((x) => {
    events.value = x.data;
    //if(events.value.length > 0)
    //month.value = new Date(events.value[0].date).getMonth() + 1;
    //year.value = new Date(events.value[0].date).getFullYear();
    loading.value = false;
  })
};

const replaceUrlBase = (urlB, add = '') => {
  return urlB.split("wp-content")[0] + add;
}

const formatDate = (inputDate) => {
  const date = new Date(inputDate);
  const day = (date.getDate()).toString().padStart(2, '0');
  const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Adicione 1 ao mês
  return `${day}.${month}`;
}

onMounted(() => {
  getEvents();
})

const scrollDown = () => {
  elementFocus.value++;
}

const scrollUp = () => {
  elementFocus.value--;
}


watch(elementFocus, (newVal, oldVal) => {
  const scrollableElement = document.getElementById('scrollable-element');
  scrollableElement.scrollTo({
    top: elementFocus.value * 83,
    behavior: 'smooth' // Use 'auto' para rolagem instantânea
  });
});

const changeMonthApp = (event) => {
  elementFocus.value = 0;
  month.value = event.month;
  year.value = event.year;
  getEvents();
}

const formatTime = (time) => {
  const hours = time.getHours().toString().padStart(2, '0');
  const minutes = time.getMinutes().toString().padStart(2, '0');

  if (minutes === '00') {
    return `${hours}h`;
  } else {
    return `${hours}h${minutes}`;
  }
}


// expose the ref to the template
</script>

<template>
  <section class="wp-calendar-wp-rest-script">
    <div :class="classContainerText">
      <div class="content" :style="'background-color:' + backgroundColor">
        <div class="flex g40 flex-column-mobile">
          <div class="calendar">
            <Calendar :events="events" :month="month" @changeMonthApp="changeMonthApp($event)"/>
          </div>
          <div class="context">
            <h2 :style="'color:' + valueColor">{{ titleEventText }}</h2>
            <ul class="events" ref="eventsElement" id="scrollable-element">
              <li v-for="(event, date) in events" :style="'border-color:' + valueColor">
                <a :href="event.link" :style="!event.link ? 'pointer-events:none' : ''">
                  <span :style="'color:' + valueColor">
                    {{ formatDate(event.date_utc) }}
                  </span>
                  <p :style="'color:' + valueColor">
                    {{formatTime(new Date(event.date_utc))}} - {{ event.post_title }}
                  </p>
                </a>
              </li>
            </ul>
            <div class="buttons-overflow flex flex-end">
              <a href="#" style="transform: rotate(180deg)" @click="scrollUp()" onclick="return false" :class="{'notFocus' : elementFocus === 0}" >
                <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                      d="M26.6666 16.5153L24.7866 14.6353L17.3333 22.0753L17.3333 5.84863L14.6666 5.84863L14.6666 22.0753L7.21329 14.6353L5.33329 16.5153L16 27.182L26.6666 16.5153Z"
                      :fill="valueColor"/>
                </svg>
              </a>

              <a href="#" @click="scrollDown()" onclick="return false" :class="{'notFocus' : elementFocus > (events.length - 5) || events.length <= 4}">
                <svg width="32" height="33" viewBox="0 0 32 33" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path
                      d="M26.6666 16.5153L24.7866 14.6353L17.3333 22.0753L17.3333 5.84863L14.6666 5.84863L14.6666 22.0753L7.21329 14.6353L5.33329 16.5153L16 27.182L26.6666 16.5153Z"
                      :fill="valueColor"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

  </section>
</template>

<style scoped>
.calendar {
  max-width: 496px;
}



.events {
  margin-top: 32px;
}

.flex-end {
  justify-content: flex-end;
  display: flex;
}

.notFocus {
  opacity: 0.5;
  pointer-events: none;
}

.events li {
  border-bottom: 1px solid #1e1e1e78;
  padding-bottom: 10px;
  height: 51px;

}

.events li a p {
  display: -webkit-box !important;
  overflow: hidden;
  margin-top: 0px;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  text-transform: uppercase;
  text-decoration: none;
  gap: 8px;
  color: #1E1E1E;
  /* Button/Regular/Large | Regular */
  font-size: 16px;
  font-style: normal;
  font-weight: 400;
  line-height: 20px; /* 125% */
  letter-spacing: 0.1px;
}

.events li + li {
  margin-top: 32px;
}

.events li:last-child {
  border-bottom: 0px;
}

.events, .events li {
  padding-left: 0px;
  list-style: none;
}

.events {
  max-height: 300px;
  overflow: hidden;
}

.events li a span {
  font-weight: bold;
}

.events li a {
  text-decoration: none;
  display: flex;
  gap: 8px;
  color: #1E1E1E;
  /* Button/Regular/Large | Regular */
  font-size: 16px;
  font-style: normal;
  font-weight: 400;
  line-height: 20px; /* 125% */
  letter-spacing: 0.1px;
}

.flex {
  display: flex;
}

h2 {
  color: #1E1E1E;
  font-size: 42px;
  font-style: normal;
  font-weight: 700;
  line-height: 52px; /* 115.556% */
}

.g40 {
  gap: 40px;
}

.context {
  flex: 1;
  max-width: 584px;
  padding: 45px 40px;
}

.wp-calendar-wp-rest-script {
  .content {
    padding: 40px;
    border-radius: 20px;

  }
}

.buttons-overflow {
  margin-top: 32px;
  gap: 10px;
}

.buttons-overflow a {
  display: block;
  height: 32px;
}

@media (max-width: 768px) {
  .wp-calendar-wp-rest-script .content {
    padding: 24px 20px !important;
  }

  .wp-calendar-wp-rest-script .flex-column-mobile {
    flex-direction: column;
    gap:24px;
  }

  .context {
    padding: 0px !important;
    max-width: 100%;
  }

  h2 {
    font-size: 30px;
  }

}
</style>
