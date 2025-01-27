<template>
  <CRow>
    <CCol :xs="12">
      <CCard class="mb-4">
        <CCardHeader>
          <strong>Booking</strong>
          <CBadge color="primary ms-1 me-1">Event: {{ event ? event.name : '' }}</CBadge>
          <CBadge color="primary">Location: {{ location ? location.name : '' }}</CBadge>
          <CBadge color="primary ms-1 me-1">Start Time: {{ event ? formatDisplay(new Date(event.start_time)) : '' }}</CBadge>
          <CBadge color="primary">End Time: {{ event ? formatDisplay(new Date(event.end_time)) : '' }}</CBadge>
          <CDropdown class="float-end">
            <CDropdownToggle color="primary">Export</CDropdownToggle>
            <CDropdownMenu>
              <CDropdownItem @click="exportBookedAttendeesToPdf">Export to pdf</CDropdownItem>
              <CDropdownItem @click="exportBookedAttendeesToXlsx">Export to xlsx</CDropdownItem>
            </CDropdownMenu>
          </CDropdown>
        </CCardHeader>
        <CCardBody>
          <div class="container select">
            <div class="position-relative">
              <div class="position-absolute" style="top: 0; left: 0; width: 400px;">
                <label for="multiselect" class="me-2">Attendees</label>
                <Multiselect 
                  id="multiselect"
                  v-model="selectedOptions" 
                  :options="formattedAttendees" 
                  track-by="id" 
                  :show-labels="false" 
                  :multiple="true" 
                  label="formattedName"
                  class="multiselect"
                >
                  <template #selection="{ remove }">
                    <draggable class="selected-item-container" :list="selectedOptions" item-key="id" group="category1">
                      <template #item="{ element }">
                        <button class="selected-item">
                          <span>{{ element.formattedName }}</span>
                          <span class="selected-item-remove" type="button" @click="remove(element)">
                            <span>&times;</span>
                          </span>
                        </button>
                      </template>
                    </draggable>
                  </template>
                </Multiselect>
              </div>
            </div>
          </div>
          <div class="container mt-10">
            <div v-for="(row, rowIndex) in tableRows" :key="rowIndex" class="row mb-4">
              <div 
                class="col ms-4 me-4 table-container"
                v-for="(table, tableIndex) in row" 
                :key="tableIndex"
                :data-table-id="table.id"
              >
                <div class="d-flex justify-content-between align-items-center">
                  <h5>{{ table.name }}</h5>
                  <h5>{{ table.seats }} seats</h5>
                </div>
                <draggable 
                  v-model="table.attendees" 
                  :group="{ name: 'category1' }" 
                  @end="onEnd" 
                  @add="addAttendeeToTable"
                >
                  <template #item="{ element, index }">
                    <button class="selected-item">
                      <span>{{ element.formattedName }}</span>
                      <span class="selected-item-remove" type="button" @click="removeDroppedItem(table, index)">
                        <span>&times;</span>
                      </span>
                    </button>
                  </template>
                </draggable>
              </div>
            </div>
          </div>
        </CCardBody>
      </CCard>
    </CCol>
  </CRow>
</template>
<script>
import { defineComponent } from 'vue';
import Multiselect from 'vue-multiselect';
import draggable from 'vuedraggable';
import axios from 'axios';

export default defineComponent({
  components: {
    Multiselect,
    draggable
  },
  data() {
    return {
      selectedOptions: [],
      tables: [],
      attendees: [],
      cloneAttendees: [], // Clone array for initial attendees
      errors: [],
      token: localStorage.getItem('token') || '',
      event: null,
      location: null
    };
  },
  computed: {
    tableRows() {
      const rows = [];
      for (let i = 0; i < this.tables.length; i += 2) {
        rows.push(this.tables.slice(i, i + 2));
      }
      return rows;
    },
    formattedAttendees() {
      return this.attendees.map(attendee => ({
        ...attendee,
        formattedName: `${attendee.name} - ${attendee.company?.name || 'No Company'} - ${this.formatAttendeeGroupName(attendee.attendee_group?.name || 'No Group')}`
      }));
    }
  },
  methods: {
    removeDroppedItem(table, index) {
      const attendee = table.attendees[index];
      table.attendees.splice(index, 1);
      this.removeAttendeeFromTable(attendee.id, table.id, this.event.id);
    },
    addAttendeeToTable(evt) {
      const attendee = evt.item._underlying_vm_;
      const fromTableElement = evt.from.closest('.table-container');
      const toTableElement = evt.to.closest('.table-container');
      const fromTableId = fromTableElement ? fromTableElement.dataset.tableId : null;
      const toTableId = toTableElement ? toTableElement.dataset.tableId : null;
      const eventId = this.event.id;

      axios.post('/api/add-attendee-to-table', {
        attendee_id: attendee.id,
        table_id: toTableId,
        event_id: eventId
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        console.log('Attendee added to new table:', response.data);
        // Elimină attendee din array-ul attendees
        this.attendees = this.attendees.filter(u => u.id !== attendee.id);
      })
      .catch(error => {
        console.error('Error adding attendee to new table:', error);
        this.errors.push('Failed to add attendee to new table.');
      });

      if (fromTableId) {
        this.removeAttendeeFromTable(attendee.id, fromTableId, eventId);
      }
    },
    removeAttendeeFromTable(attendeeId, tableId, eventId) {
      axios.delete('/api/remove-attendee-from-table', {
        headers: {
          'Authorization': `Bearer ${this.token}`
        },
        data: {
          attendee_id: attendeeId,
          table_id: tableId,
          event_id: eventId
        }
      })
      .then(response => {
        console.log('Attendee removed from table:', response.data);
        // Adaugă attendee înapoi în array-ul attendees și sortează
        const attendee = this.cloneAttendees.find(u => u.id === attendeeId);
        if (attendee) {
          this.attendees.push(attendee);
          this.attendees.sort((a, b) => a.id - b.id);
        }
      })
      .catch(error => {
        console.error('Error removing attendee from table:', error);
        this.errors.push('Failed to remove attendee from table.');
      });
    },
    fetchAttendees() {
      axios.get('/api/all-attendees', {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.attendees = response.data;
        this.cloneAttendees = JSON.parse(JSON.stringify(response.data)); // Clone the initial attendees array
      })
      .catch(error => {
        console.error('Error fetching attendees:', error);
        this.errors.push('Failed to fetch attendees.');
      });
    },
    fetchEventSet() {
      axios.get('/api/event-set', {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        const event = response.data;
        this.event = event;
        this.location = event.location; 
        this.fetchTablesByEvent(event.id);
      })
      .catch(error => {
        console.error('Error fetching event set:', error);
        this.errors.push('Failed to fetch event set.');
      });
    },
    fetchTablesByEvent(eventId) {
      axios.get(`/api/tables-by-event/${eventId}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.tables = response.data.map(table => ({
          ...table,
          attendees: []
        }));
        this.fetchTableAttendees();
      })
      .catch(error => {
        console.error('Error fetching tables:', error);
        this.errors.push('Failed to fetch tables.');
      });
    },
    fetchTableAttendees() {
      axios.get('/api/set-attendees-to-table', {
        headers: {
          'Authorization': `Bearer ${this.token}`
        },
        params: {
          event_id: this.event.id
        }
      })
      .then(response => {
        const tableAttendees = response.data;
        this.tables.forEach(table => {
          table.attendees = tableAttendees
            .filter(ta => ta.table_id === table.id)
            .map(ta => ({
              id: ta.attendee_id,
              name: ta.attendee?.name || 'No Name',
              company: ta.attendee?.company || { name: 'No Company' },
              attendee_group: ta.attendee?.attendee_group || { name: 'No Group' },
              formattedName: `${ta.attendee?.name || 'No Name'} - ${ta.attendee?.company?.name || 'No Company'} - ${this.formatAttendeeGroupName(ta.attendee?.attendee_group?.name || 'No Group')}`
            }));

          // Elimină attendee care sunt deja la o masă din array-ul attendees
          tableAttendees.forEach(ta => {
            this.attendees = this.attendees.filter(a => a.id !== ta.attendee_id);
          });
        });
      })
      .catch(error => {
        console.error('Error fetching table attendees:', error);
        this.errors.push('Failed to fetch table attendees.');
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
    formatAttendeeGroupName(name) {
      if (!name) return '';
      return name.slice(0, -1);
    },
    exportBookedAttendeesToPdf() {
      axios.post('/api/export/booked-attendees-to-pdf', {
        attendees: this.tables,
        event: this.event
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        },
        responseType: 'blob'
      })
      .then(response => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'booked_attendees.pdf');
        document.body.appendChild(link);
        link.click();
        link.remove();
      })
      .catch(error => {
        console.error('Error exporting booked attendees to PDF:', error);
      });
    },
    exportBookedAttendeesToXlsx() {
      axios.post('/api/export/booked-attendees-to-xlsx', {
        attendees: this.tables,
        event: this.event
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        },
        responseType: 'blob'
      })
      .then(response => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'booked_attendees.xlsx');
        document.body.appendChild(link);
        link.click();
        link.remove();
      })
      .catch(error => {
        console.error('Error exporting booked attendees to XLSX:', error);
      });
    }
  },
  created() {
    this.fetchEventSet();
    this.fetchAttendees(); 
  }
});
</script>
<style>
.multiselect {
  width: 400px;
  margin-bottom: 20px;
}
.selected-item {
  display: inline-block;
  margin: 2px;
  padding: 4px 8px;
  background-color: #f4f4f4 !important; 
  border: 1px solid #ddd;
  cursor: grab;
  border-radius: 5px;
}
.selected-item-remove {
  margin-left: 10px;
  cursor: pointer;
}
.table-container {
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-color: #f9f9f9;
}
.multiselect__option--highlight { 
  background-color: var(--cui-primary) !important; 
  color: white !important; 
}
</style>

