<template>
  <CRow>
    <CCol :xs="6">
      <CCard class="mb-4">
        <CCardBody>
          <CChartBar
            v-if="chartDataBar"
            style="height:300px"
            :data="chartDataBar"
            :options="{ maintainAspectRatio: false }"
          />
          <div v-else>
            Loading...
          </div>
        </CCardBody>
      </CCard>
    </CCol>
    <CCol :xs="6">
      <CCard class="mb-4">
        <CCardBody>
          <CChartDoughnut
            v-if="chartDataPie"
            style="height:300px"
            :data="chartDataPie"
            :options="{ maintainAspectRatio: false }"
          />
          <div v-else>
            Loading...
          </div>
        </CCardBody>
      </CCard>
    </CCol>
  </CRow>
</template>

<script>
import { CChartBar, CChartDoughnut } from '@coreui/vue-chartjs'
import axios from 'axios'

export default {
  components: {
    CChartBar,
    CChartDoughnut,
  },
  data() {
    return {
      token: localStorage.getItem('token') || '',
      chartDataBar: null,
      chartDataPie: null,
    };
  },
  methods: {
    fetchConfirmedAttendees() {
      axios.get('/api/confirmed-attendees-per-event', {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        const data = response.data;
        this.chartDataBar = {
          labels: data.map(item => item.event),
          datasets: [
            {
              data: data.map(item => item.confirmed_attendees),
              backgroundColor: '#E55353',
              label: 'Confirmed attendees',
            }
          ]
        };
      })
      .catch(error => {
        console.error('Error fetching confirmed attendees per event:', error);
      });
    },
    fetchAttendeesPerCompany() {
      axios.get('/api/attendees-per-company', {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        const data = response.data;
        this.chartDataPie = {
          labels: data.map(item => item.company),
          datasets: [
            {
              data: data.map(item => item.attendees),
              backgroundColor: ['#E55353', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'],
              label: 'Registered attendees',
            }
          ]
        };
      })
      .catch(error => {
        console.error('Error fetching attendees per company:', error);
      });
    }
  },
  async created() {
    await this.fetchConfirmedAttendees();
    await this.fetchAttendeesPerCompany();
  }
}
</script>


