<template>
  <CRow>
    <CCol :xs="12">
      <CCard class="mb-4">
        <CCardHeader>
          <strong>Events</strong>
          <button class="btn btn-primary float-end" @click="openModal('add')"><i class="bi bi-plus-lg me-1"></i>Add Event</button>
        </CCardHeader>
        <CCardBody>
          <CTable>
            <CTableHead>
              <CTableRow>
                <CTableHeaderCell scope="col" class="w-5">Id</CTableHeaderCell> 
                <CTableHeaderCell scope="col" class="w-15">Name</CTableHeaderCell> 
                <CTableHeaderCell scope="col" class="w-15">Start Time</CTableHeaderCell> 
                <CTableHeaderCell scope="col" class="w-15">End Time</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-10">Location</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-10">Is Set</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-20">Actions</CTableHeaderCell>
              </CTableRow>
            </CTableHead>
            <CTableBody>
              <CTableRow v-for="event in events.data" :key="event.id">
                <CTableDataCell>{{ event.id }}</CTableDataCell>
                <CTableDataCell>{{ event.name }}</CTableDataCell>
                <CTableDataCell>{{ formatDisplay(new Date(event.start_time)) }}</CTableDataCell>
                <CTableDataCell>{{ formatDisplay(new Date(event.end_time)) }}</CTableDataCell>
                <CTableDataCell>{{ event.location ? event.location.name : 'No location' }}</CTableDataCell>
                <CTableDataCell>
                  <CFormSwitch
                    :id="`formSwitchCheckChecked${event.id}`"
                    :checked="event.is_set === 1"
                    @change="toggleIsSet(event)"
                  />
                </CTableDataCell>
                <CTableDataCell>
                  <div class="btn-group" role="group">
                    <button class="btn btn-primary text-white" @click="eventDetails(event)">
                      <i class="bi bi-card-text"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary" @click="openModal('edit', event)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary text-white" @click="confirmDelete(event.id, event.name)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </CTableDataCell>
              </CTableRow>
            </CTableBody>
          </CTable>
          <CPagination v-if="events.data.length > 0" class="float-end" aria-label="Page navigation example">
            <CPaginationItem class="cursor-pointer"
              :disabled="events.current_page === 1"
              @click="fetchEvents(events.current_page - 1)">
              <span aria-hidden="true">&laquo;</span>
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              v-for="page in events.last_page"
              :key="page"
              :active="page === events.current_page"
              @click="fetchEvents(page)">
              {{ page }}
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              :disabled="events.current_page === events.last_page"
              @click="fetchEvents(events.current_page + 1)">
              <span aria-hidden="true">&raquo;</span>
            </CPaginationItem>
          </CPagination>
          <div v-if="errors.length" class="alert alert-danger mt-3">
            <ul>
              <li v-for="(error, index) in errors" :key="index">{{ error }}</li>
            </ul>
          </div>
        </CCardBody>
      </CCard>
      <!-- Modal Add/Edit Event --> 
      <CModal :visible="showModal" @close="closeModal" >
        <CModalHeader @close="closeModal">
            <CModalTitle>{{ modalTitle }}</CModalTitle>
        </CModalHeader>
        <CModalBody>
            <form @submit.prevent="handleSubmit">
              <div class="row mb-3">
                <span class="col-3">
                  <label for="eventName" class="form-label">Name</label>
                </span>
                <span class="col">
                  <input type="text" class="form-control" id="eventName" v-model="eventForm.name" />
                  <div v-if="errors.name" class="text-danger mt-2">{{ errors.name[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-3">
                  <label for="startTime" class="form-label">Start Time</label>
                </span>
                <span class="col">
                  <VueDatePicker v-model="eventForm.startTime" :min-date="new Date()" :format="formatDisplay" />
                  <div v-if="errors.start_time" class="text-danger mt-2">{{ errors.start_time[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-3">
                  <label for="endTime" class="form-label">End Time</label>
                </span>
                <span class="col">
                  <VueDatePicker v-model="eventForm.endTime" :min-date="new Date()" :format="formatDisplay" />
                  <div v-if="errors.end_time" class="text-danger mt-2">{{ errors.end_time[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-3">
                  <label for="eventLocation" class="form-label">Location</label>
                </span>
                <span class="col">
                  <CFormSelect v-model="eventForm.location_id" aria-label="Select location">
                    <option value="">Select Location</option>
                    <option v-for="location in locations" :key="location.id" :value="location.id">
                      {{ location.name }}
                    </option>
                  </CFormSelect>
                  <div v-if="errors.location_id" class="text-danger mt-2">{{ errors.location_id[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-3">
                  <label for="isSet" class="form-label">Is Set</label>
                </span>
                <span class="col">
                  <CFormSwitch
                    id="formSwitchCheckDefault"
                    v-model="eventForm.is_set"
                  />
                </span>
              </div>
              <button type="submit" class="btn btn-primary">Save</button> 
            </form>
        </CModalBody>
      </CModal>
      <!-- Modal Confirm Delete -->
      <CModal
        :visible="showConfirmDelete"
        @close="closeConfirmDelete"
      >
        <CModalHeader @close="closeConfirmDelete">
          <CModalTitle>Delete Event</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <p>Do you want to delete {{ confirmDeleteName }}?</p>
        </CModalBody>
        <CModalFooter>
          <div class="btn-group" role="group">
            <CButton color="primary" @click="deleteEvent(confirmDeleteId)">
              Yes
            </CButton>
            <div class="separator"></div>
            <CButton color="secondary" @click="closeConfirmDelete">
              Close
            </CButton>
          </div>
        </CModalFooter>
      </CModal>
      <!-- Modal Event Details -->
      <CModal
        :visible="showDetailsModal"
        @close="closeDetailsModal"
      >
        <CModalHeader @close="closeDetailsModal">
          <CModalTitle>Event Details</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <div class="row mb-3">
            <span class="col-3">
              Name
            </span>
            <span class="col">
              {{ detailsEvent.name }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col-3">
              <label for="eventStartTime" class="form-label">Start Time</label>
            </span>
            <span class="col">
              {{ formatDisplay(new Date(detailsEvent.start_time)) }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col-3">
              <label for="eventEndTime" class="form-label">End Time</label>
            </span>
            <span class="col">
              {{ formatDisplay(new Date(detailsEvent.end_time)) }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col-3">
                <label for="eventLocation" class="form-label">Location</label>
                </span>
                <span class="col">
                  {{ detailsEvent.location ? detailsEvent.location.name : 'No location' }}
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-3">
                  <label for="eventIsSet" class="form-label">Is Set</label>
                </span>
                <span class="col">
                  {{ detailsEvent.is_set === 1 ? 'Yes' : 'No' }}
                </span>
              </div>
            </CModalBody>
          </CModal>
        </CCol>
      </CRow>
</template>
<script>
import axios from 'axios';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { ref } from 'vue';

export default {
  components: {
    VueDatePicker,
  },
  data() {
    return {
      token: localStorage.getItem('token') || '',
      events: {
        data: [],
        current_page: 1,
        last_page: 1,
      },
      locations: [],
      errors: [],
      modalTitle: '',
      eventForm: {
        id: null,
        name: '',
        startTime: null,
        endTime: null,
        location_id: '',
        is_set: false
      },
      showModal: false,
      showConfirmDelete: false,
      showDetailsModal: false,
      detailsEvent: {
        name: '',
        start_time: null,
        end_time: null,
        location: {
          name: ''
        },
        is_set: 0
      },
      confirmDeleteId: null,
      confirmDeleteName: '',
      date: ref(new Date())
    };
  },
  methods: {
    fetchEvents(page = 1) {
      axios.get(`/api/events?page=${page}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.events = response.data;
      })
      .catch(error => {
        console.error('Error fetching events:', error);
        this.errors.push('Failed to fetch events.');
      });
    },
    fetchLocations() {
      axios.get('/api/locations', {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.locations = response.data.data;
      })
      .catch(error => {
        console.error('Error fetching locations:', error);
        this.errors.push('Failed to fetch locations.');
      });
    },
    formatDisplay(date) {
      if (!date) return 'Invalid date';
      
      const day = date.getDate();
      const month = date.getMonth() + 1;
      const year = date.getFullYear();
      const hours = date.getHours();
      const minutes = date.getMinutes().toString().padStart(2, '0');
      
      return `${day}-${month}-${year} ${hours}:${minutes}`;
    },
    formatSave(date) {
      if (!date) return 'Invalid date';
      
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      const day = date.getDate().toString().padStart(2, '0');
      const hours = date.getHours().toString().padStart(2, '0');
      const minutes = date.getMinutes().toString().padStart(2, '0');
      
      return `${year}-${month}-${day} ${hours}:${minutes}`;
    },
    handleSubmit() {
      this.errors = [];
      if (this.eventForm.id) {
        this.updateEvent();
      } else {
        this.addEvent();
      }
    },
    addEvent() {
      axios.post('/api/events', { 
        name: this.eventForm.name,
        start_time: this.eventForm.startTime ? this.formatSave(this.eventForm.startTime) : null,
        end_time: this.eventForm.endTime ? this.formatSave(this.eventForm.endTime) : null,
        location_id: this.eventForm.location_id,
        is_set: this.eventForm.is_set ? 1 : 0
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchEvents(this.events.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to add event.');
        }
      });
    },
    updateEvent() {
      axios.put(`/api/events/${this.eventForm.id}`, { 
        name: this.eventForm.name,
        start_time: this.eventForm.startTime ? this.formatSave(this.eventForm.startTime) : null,
        end_time: this.eventForm.endTime ? this.formatSave(this.eventForm.endTime) : null,
        location_id: this.eventForm.location_id,
        is_set: this.eventForm.is_set ? 1 : 0
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchEvents(this.events.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to update event.');
        }
      });
    },
    deleteEvent(eventId) {
      axios.delete(`/api/events/${eventId}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(() => {
        this.fetchEvents(this.events.current_page);
        this.closeConfirmDelete();
      })
      .catch(error => {
        console.error('Error deleting event:', error);
        this.errors.push('Failed to delete event.');
        this.closeConfirmDelete();
      });
    },
    confirmDelete(eventId, eventName) {
      this.confirmDeleteId = eventId;
      this.confirmDeleteName = eventName;
      this.showConfirmDelete = true;
    },
    closeConfirmDelete() {
      this.showConfirmDelete = false;
      this.confirmDeleteId = null;
      this.confirmDeleteName = '';
    },
    toggleIsSet(event) {
      axios.put(`/api/events/${event.id}`, {
        is_set: event.is_set === 1 ? 0 : 1
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchEvents(this.events.current_page);
        this.events.data.forEach(e => {
          if (e.id !== event.id) {
            e.is_set = 0;
          }
        });
      })
      .catch(error => {
        console.error('Error updating is_set:', error);
        this.errors.push('Failed to update is_set.');
      });
    },
    openModal(type, event = {}) {
      this.errors = [];
      if (type === 'edit') {
        this.modalTitle = 'Edit Event';
        this.eventForm.id = event.id;
        this.eventForm.name = event.name;
        this.eventForm.startTime = event.start_time ? new Date(event.start_time) : null;
        this.eventForm.endTime = event.end_time ? new Date(event.end_time) : null;
        this.eventForm.location_id = event.location_id;
        this.eventForm.is_set = event.is_set === 1;
      } else {
        this.modalTitle = 'Add Event';
        this.eventForm.id = null;
        this.eventForm.name = '';
        this.eventForm.startTime = null;
        this.eventForm.endTime = null;
        this.eventForm.location_id = '';
        this.eventForm.is_set = false;
      }
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    eventDetails(event) {
      this.detailsEvent = event;
      this.showDetailsModal = true;
    },
    closeDetailsModal() {
      this.showDetailsModal = false;
      this.detailsEvent = {
        name: '',
        start_time: null,
        end_time: null,
        location: {
          name: ''
        },
        is_set: 0
      };
    }
  },
  created() {
    this.fetchEvents();
    this.fetchLocations();
  }
};
</script>


