<script>
import moment from "moment";

export default {
  props:['events', 'month'],
  data() {
    return {
      current: {
        year: 0,
        month: 0,
        date: 0,
        monthName: ''
      },
      pages: 1,
      page:1,
      loading: false,
      hours: null,
      person: false,
      dataSelect: null,
      dataSelectAn: null,
      today: {
        year: 0,
        month: 0,
        date: 0
      },
      dataWeek: [
        "domingo",
        "segunda",
        "terca",
        "quarta",
        "quinta",
        "sexta",
        "sabado"
      ],
      selectType: 0,
      data: null,
      hourEnd: null,
      heading: ["D", "S", "T", "Q", "Q", "S", "S"],
      monthName: ['', 'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro']
    }
  },
  methods: {
    changeMonth(isNext) {
      let month = this.current.month;
      isNext === true ? (month = month + 1) : (month = month - 1);
      if (month <= 0) {
        month = 12;
        this.current.year = this.current.year - 1;
      }
      if (month > 12) {
        month = 1;
        this.current.year = this.current.year + 1;
      }
      this.current.month = month;
      this.$emit('changeMonth', month);
      this.current.date = 1;

    },
    backToToday() {
      this.current.year = this.today.year;
      this.current.month = this.today.month;
      this.current.date = this.today.date;
    },
    getToday() {
      this.today.year = moment().year();
      this.today.month = moment().month() + 1;
      this.today.date = moment().date();
      this.current.monthName = moment().locale()
    },
    hour2Minutes(hours) {
      return parseInt(hours.slice(0, 2)) * 60 + parseInt(hours.slice(3, 5));
    },
    doesEventExist(date) {
      const filter = this.events.filter((x) => x.date === date);
      return filter.length > 0;
    }
  },
  computed: {
    buildCalendar() {
      let myYears = this.current.year;
      let myMonth = this.current.month;
      let myDate = this.current.date;

      let monthText = myMonth < 10 ? `0${myMonth}` : myMonth.toString();

      let dateArray = [];
      let patchNum = 0;
      let totalDate = moment(`${myYears}-${monthText}`, 'YYYY-MM').daysInMonth();
      let firstDayOfMonth = moment(`${myYears}-${monthText}-01`).day();

      // Adiciona os dias do mês anterior
      const prevMonth = myMonth - 1 < 1 ? 12 : myMonth - 1;
      const prevYear = myMonth - 1 < 1 ? myYears - 1 : myYears;
      const daysInPrevMonth = moment(`${prevYear}-${prevMonth}`, 'YYYY-MM').daysInMonth();
      for (let i = firstDayOfMonth - 1; i >= 0; i--) {
        const dayNum = daysInPrevMonth - i;
        let obj = {
          years: prevYear,
          month: prevMonth,
          date: dayNum,
          act: false,
          date_format:new Date(prevYear, (prevMonth - 1), dayNum, 0, 0, 1).toISOString().slice(0, 10),
          number: dayNum < 10 ? `0${dayNum}` : dayNum.toString(),
          today: false,
          dayOfWeek: i,
          current: false,
          none: true
        };
        dateArray.push(obj);
      }

      // Adiciona os dias do mês atual
      for (let i = 0; i < totalDate; i++) {
        let dateNum = i + 1;
        let isToday = false;
        let isCurrent = false;
        let dateText = "";

        if (
            myYears === this.today.year &&
            myMonth === this.today.month &&
            dateNum === this.today.date
        ) {
          isToday = true;
        }

        if (dateNum === myDate) {
          isCurrent = true;
        }

        dateNum < 10 ? (dateText = `0${dateNum}`) : (dateText = dateNum.toString());

        let dateTotal = new Date(myYears, myMonth - 1, dateNum - 1);
        let dateActual = new Date(this.today.year, this.today.month - 1, this.today.date);
        let act = dateTotal >= dateActual;
        let obj = {
          years: myYears,
          month: myMonth,
          date: dateNum,
          act,
          number: dateText,
          today: isToday,
          date_format:new Date(myYears, (myMonth -1), dateNum, 0, 0, 1).toISOString().slice(0, 10),
          dayOfWeek: (firstDayOfMonth + i) % 7,
          current: isCurrent,
          none: false
        };
        dateArray.push(obj);
      }

      // Adiciona os dias do próximo mês
      const nextMonth = myMonth + 1 > 12 ? 1 : myMonth + 1;
      const nextYear = myMonth + 1 > 12 ? myYears + 1 : myYears;
      const lastDayOfWeek = moment(`${myYears}-${monthText}-${totalDate}`, 'YYYY-MM-DD').day();
      for (let i = 1; i <= 6 - lastDayOfWeek; i++) {
        const dayNum = i;
        let obj = {
          years: nextYear,
          month: nextMonth,
          date: dayNum,
          act: false,
          date_format:new Date(nextYear, nextMonth - 1, dayNum, 0, 0, 1).toISOString().slice(0, 10),
          number: dayNum < 10 ? `0${dayNum}` : dayNum.toString(),
          today: false,
          dayOfWeek: (lastDayOfWeek + i) % 7,
          current: false,
          none: true
        };
        dateArray.push(obj);
      }

      // Calculate patchNum
      if (dateArray.length % 7 === 0) {
        patchNum = 0;
      } else {
        patchNum = 7 - (dateArray.length % 7);
      }

      // Adiciona dias em branco para o final do calendário
      for (let i = 0; i < patchNum; i++) {
        const dayNum = i + 1;
        let obj = {
          years: nextYear,
          month: nextMonth,
          date: dayNum,
          date_format:new Date(nextYear, nextMonth - 1, dayNum, 0, 0, 1).toISOString().slice(0, 10),
          act: false,
          number: dayNum < 10 ? `0${dayNum}` : dayNum.toString(),
          today: false,
          dayOfWeek: (lastDayOfWeek + i + 1) % 7,
          current: false,

          none: true
        };
        dateArray.push(obj);
      }

      console.log(dateArray);
      return dateArray;
    },
    convertTwoDigits() {
      let text = "";
      this.current.month;
      return text;
    }
  },
  created() {
    this.getToday();
    this.backToToday();
  },
  mounted() {
    //this.getAgendamento();
  },
  watch: {
    month() {
      if(this.month !== null)
      this.current.month = this.month;
    },
    events() {
      if(this.events.length > 0) {
        this.pages = Math.ceil(4 / this.events.length);
      }
    },
    dataSelect() {
      this.hourEnd = null;
      setTimeout(() => {
        this.dataSelectAn = this.dataSelect ? true : false;
      }, 300);
      let dayOfWeek1;

      if (this.person) {
        var a = this.dataSelect.split('-');
        var b = new Date(a[0], a[1], a[2], 0, 0, 0);
        console.log(b, b.getDay());
        let test = this.data.dias_personalizado.filter((z) => {
          return z.dia_da_semana == this.dataWeek[b.getDay()]
        });

        dayOfWeek1 = test[0];
      }


      console.log(dayOfWeek1);

      var hours = this.person ? this.hour2Minutes(dayOfWeek1.hora_de_fim) - this.hour2Minutes(dayOfWeek1.hora_de_inicio) : this.hour2Minutes(this.data.fim) - this.hour2Minutes(this.data.inicio);
      var agend = Math.floor(hours / parseInt(this.data.intervalo));
      var hours_ar = [];

      for (var i = 0; i < agend; i++) {
        var dateAc = this.dataSelect.split('-');
        var date = this.person ? new Date(dateAc[0], dateAc[1], dateAc[2], dayOfWeek1.hora_de_inicio.slice(0, 2), dayOfWeek1.hora_de_inicio.slice(3, 5)) : new Date(dateAc[0], dateAc[1], dateAc[2], this.data.inicio.slice(0, 2), this.data.inicio.slice(3, 5));
        date.setTime(date.getTime() + parseInt(this.data.intervalo) * 60 * 1000 * i);
        hours_ar.push(date);
      }

      this.hours = hours_ar;
    },
    selectType() {
      this.dataSelect = null;
      this.hourEnd = null;
      this.getAgendamento();
      this.$parent.data.typeSelect = this.selectType;
    },
    hourEnd() {
      this.$parent.data.dataSelecionada = this.hourEnd.toISOString();
    },
  }
}
</script>

<template>
  <div class="calendar">
    <div class="calendar__header">
      <div class="calendar__title" @click.prevent="backToToday()">
        <span class="caption-month">{{ monthName[current.month] }} {{ current.year }}</span>
      </div>
      <div class="flex gap26">
        <a href="javascript:;" class=""
           @click.prevent="changeMonth(false)">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M16 5.33337L17.88 7.21337L10.44 14.6667H26.6666V17.3334H10.44L17.88 24.7867L16 26.6667L5.33329 16L16 5.33337Z"
                fill="#FFFFF4"/>
          </svg>
        </a>
        <a href="javascript:;" class=""
           @click.prevent="changeMonth(true)">
          <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path
                d="M16 5.33337L14.12 7.21337L21.56 14.6667H5.33337V17.3334H21.56L14.12 24.7867L16 26.6667L26.6667 16L16 5.33337Z"
                fill="#FFFFF4"/>
          </svg>
        </a>
      </div>
    </div>
    <div class="calendar__body">
      <ul class="calendar__heading">
        <li v-for="item in heading">
          <div class="calendar__item">{{ item }}</div>
        </li>
      </ul>
      <ul class="calendar__content">
        <li v-for="item in buildCalendar">
          <a href="javascript:" class="calendar__item" :class="{ 'grayed-out': item.none, 'exists' : doesEventExist(item.date_format) }">{{ item.number }}</a>
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
.calendar {
  border-radius: 20px;
}

.calendar__header a {
  display: block;
}

.calendar__header a svg {
  display: block;
}

.flex {
  display: flex;
  align-items: center;
  gap: 24px;
}

.calendar__heading,
.calendar__content {
  width: 100%;
  display: flex;
  flex-wrap: wrap;
}

.calendar__heading > li,
.calendar__content > li {
  width: 14.285%;
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 71px;
}

.calendar__body {
  border-radius: 0px 0px 20px 20px;
}

.calendar__item {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  padding: 10px 0px;
  border-radius: 3px;
  transition: all 0.3s;
  color: #4A4A4A;
}

.calendar__item.current {
  background-color: white;
}

.calendar__content .calendar__item {
  padding: 16px 0px;
}

.calendar__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 45px;
  background-color: #F3523F;
  border-radius: 20px 20px 0px 0px;
}

.calendar__header .arrow-btn {
  display: flex;
  font-size: 0rem;
}

.calendar__header .arrow-btn::before {
  content: "";
  display: block;
  width: 0;
  height: 0;
  border-style: solid;
}

.calendar__header .arrow-btn.btn-prevmonth::before {
  border-width: 10px 13px 10px 0;
  border-color: transparent #4A4A4A transparent transparent;
}

.calendar__header .arrow-btn.btn-nextmonth::before {
  border-width: 10px 0 10px 13px;
  border-color: transparent transparent transparent #4A4A4A;
}

.calendar__title {
  color: #FFFFF4;
  text-transform: uppercase;
  opacity: 0.5;
  font-size: 22px;
  font-style: normal;
  font-weight: 700;
  line-height: 28px;
}

.calendar__content .calendar__item.act {
  background-color: #480D7B;
  border-radius: 50%;
  color: white;
  cursor: pointer;
  transition: background-color ease-in-out 300ms;
  pointer-events: all;
}

.calendar__content .calendar__item.act:hover, .calendar__content .calendar__item.act.current {
  background-color: #c1122e;
}

.calendar__heading {
  padding: 0px 5px;
  background-color: #FFFFF4;

}

.calendar__heading .calendar__item {
  font-weight: 500;
}

.calendar__content {
  padding: 5px;
  position: relative;
  background-color: #FFFFF4;
  border-radius: 0px 0px 20px 20px;
}

.calendar__content .calendar__item {
  font-weight: 500;
  cursor: default;
  pointer-events: none;
  text-decoration: none;
  width: 60px;
  height: 60px;
  color: #1E1E1E;
  text-align: center;
  font-size: 18px;
  font-style: normal;
  line-height: 24px;
  letter-spacing: 0.5px;
}

.calendar__content .calendar__item.active {
  background: #c8dd7f;
  border-radius: 50%;
}

.grayed-out {
  opacity: 0.2;
}

.exists {
  background-color: #D3E27E;
  border-radius: 50%;
}

@media (max-width: 768px) {
  .calendar__header {
    padding: 16px 30px !important;
  }

  .calendar__heading>li, .calendar__content>li {
    min-height: 50px;
  }


  .calendar__content .calendar__item {
    width: 35px;
    height: 35px;
  }


  .gap26 {
    gap: 12px !important;
  }

}
</style>