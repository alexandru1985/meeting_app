<template>
  <CRow>
    <CCol :xs="12">
      <CCard class="mb-4">
        <CCardHeader>
          <strong>Attendees</strong>
          <CBadge color="primary ms-1 me-1">Event: {{ event ? event.name : '' }}</CBadge>
          <CBadge color="primary">Location: {{ location ? location.name : '' }}</CBadge>
          <CBadge color="primary ms-1 me-1">Start Time: {{ event ? formatDisplay(new Date(event.start_time)) : '' }}</CBadge>
          <CBadge color="primary me-1">End Time: {{ event ? formatDisplay(new Date(event.end_time)) : '' }}</CBadge>
          <CBadge color="primary">Confirmed: {{ confirmedAttendees }}</CBadge>
          <div class="btn-group float-end" role="group">
            <button class="btn btn-primary" @click="openFilterModal">
              <i class="bi bi-filter me-1"></i>Filters </button>
            <div class="separator"></div>
            <CDropdown>
              <CDropdownToggle color="primary">
                <i class="bi bi-download me-1"></i>Export
              </CDropdownToggle>
              <CDropdownMenu>
                <CDropdownItem @click="exportRegisteredAttendeesToPdf">Export to pdf</CDropdownItem>
                <CDropdownItem @click="exportRegisteredAttendeesToXlsx">Export to xlsx</CDropdownItem>
              </CDropdownMenu>
            </CDropdown>
            <div class="separator"></div>
            <button class="btn btn-primary float-end" @click="openModal('add')">
              <i class="bi bi-plus-lg me-1"></i>Add Attendee </button>
          </div>
        </CCardHeader>
        <CCardBody>
          <CTable>
            <CTableHead>
              <CTableRow>
                <CTableHeaderCell scope="col" class="w-5">Id</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-25">Name</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-25">Company</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-25">Attendee Type</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-10">Confirmed</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-20">Actions</CTableHeaderCell>
              </CTableRow>
            </CTableHead>
            <CTableBody>
              <CTableRow v-for="attendee in attendees.data" :key="attendee.id">
                <CTableDataCell>{{ attendee.id }}</CTableDataCell>
                <CTableDataCell>{{ attendee.name }}</CTableDataCell>
                <CTableDataCell>{{ attendee.company.name }}</CTableDataCell>
                <CTableDataCell>{{ formatAttendeeGroupName(attendee.attendee_group?.name) }}</CTableDataCell>
                <CTableDataCell>
                  <CFormSwitch :id="`formSwitchCheckChecked${attendee.id}`" :checked="attendee.events && attendee.events.length > 0 ? attendee.events[0].pivot.confirmed === 1 : false" @change="toggleConfirmed(attendee)" />
                </CTableDataCell>
                <CTableDataCell>
                  <div class="btn-group" role="group">
                    <button class="btn btn-primary text-white" @click="attendeeDetails(attendee)">
                      <i class="bi bi-card-text"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary" @click="openModal('edit', attendee)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary text-white" @click="confirmDelete(attendee.id, attendee.name)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </CTableDataCell>
              </CTableRow>
            </CTableBody>
          </CTable>
          <CPagination v-if="attendees.data.length > 0" class="float-end" aria-label="Page navigation example">
            <CPaginationItem class="cursor-pointer" :disabled="attendees.current_page === 1" @click="fetchAttendees(attendees.current_page - 1)">
              <span aria-hidden="true">&laquo;</span>
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer" v-for="page in attendees.last_page" :key="page" :active="page === attendees.current_page" @click="fetchAttendees(page)">
              {{ page }}
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer" :disabled="attendees.current_page === attendees.last_page" @click="fetchAttendees(attendees.current_page + 1)">
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
      <ModalFormAttendee 
        :showModal="showModal" 
        :modalTitle="modalTitle" 
        :attendeeForm="attendeeForm" 
        :errors="errors" 
        :companies="companies" 
        :attendeeGroups="attendeeGroups" 
        @close="closeModal" 
        @submit="handleSubmit" 
      />
      <ModalAttendeeDetails 
        :showDetailsModal="showDetailsModal" 
        :detailsAttendee="detailsAttendee" 
        @close="closeDetailsModal" 
      />
      <ModalFilterAttendees 
        :showFilterModal="showFilterModal" 
        :filters="filters" 
        :companies="companies" 
        :attendeeGroups="attendeeGroups" 
        :confirmedOptions="confirmedOptions" 
        @close="closeFilterModal" 
        @apply="applyFilters" 
      />
      <ModalDeleteAttendee 
        :showConfirmDelete="showConfirmDelete" 
        :confirmDeleteName="confirmDeleteName" 
        @close="closeConfirmDelete" 
        @confirm="deleteAttendee(confirmDeleteId)" 
      />
    </CCol>
  </CRow>
</template>
<script>
import axios from 'axios';
import Multiselect from 'vue-multiselect';
import ModalFormAttendee from './ModalFormAttendee.vue';
import ModalAttendeeDetails from './ModalAttendeeDetails.vue';
import ModalFilterAttendees from './ModalFilterAttendees.vue';
import ModalDeleteAttendee from './ModalDeleteAttendee.vue';

export default {
	components: {
		Multiselect,
		ModalFormAttendee,
		ModalAttendeeDetails,
		ModalFilterAttendees,
		ModalDeleteAttendee
	},
	data() {
		return {
			token: localStorage.getItem('token') || '',
			attendees: {
				data: [],
				current_page: 1,
				last_page: 1,
			},
			companies: [],
			attendeeGroups: [],
			errors: [],
			modalTitle: '',
			attendeeForm: {
				id: null,
				name: '',
				company_id: '',
				attendee_group_id: '',
				confirmed: false
			},
			filters: {
				name: '',
				companies: [],
				attendeeGroups: [],
				confirmed: []
			},
			confirmedOptions: [{
					value: 1,
					text: 'Confirmed'
				},
				{
					value: 0,
					text: 'Not Confirmed'
				}
			],
			showModal: false,
			showConfirmDelete: false,
			showDetailsModal: false,
			showFilterModal: false,
			detailsAttendee: {
				name: '',
				company: {
					name: ''
				},
				attendee_group: {
					name: ''
				}
			},
			confirmDeleteId: null,
			confirmDeleteName: '',
			event: null,
			location: null,
			confirmedAttendees: null
		};
	},
	methods: {
		fetchAttendees(page = 1) {
			const params = {
				page: page,
				name: this.filters.name,
				companies: this.filters.companies.map(c => c.id),
				attendee_groups: this.filters.attendeeGroups.map(g => g.id),
				confirmed: this.filters.confirmed.map(c => c.value),
				event_id: this.event ? this.event.id : null
			};

			axios.get('/api/users', {
					headers: {
						'Authorization': `Bearer ${this.token}`
					},
					params: params
				})
				.then(response => {
					this.attendees = response.data;
					return response;
				})
				.catch(error => {
					console.error('Error fetching attendees:', error);
					this.errors.push('Failed to fetch attendees.');
					throw error;
				});
		},
		async fetchCompanies() {
			await axios.get('/api/companies', {
					headers: {
						'Authorization': `Bearer ${this.token}`
					}
				})
				.then(response => {
					this.companies = response.data.data;
					return response;
				})
				.catch(error => {
					console.error('Error fetching companies:', error);
					this.errors.push('Failed to fetch companies.');
					throw error;
				});
		},
		async fetchAttendeeGroups() {
			await axios.get('/api/attendee-groups', {
					headers: {
						'Authorization': `Bearer ${this.token}`
					}
				})
				.then(response => {
					this.attendeeGroups = response.data.data.map(ag => {
						return {
							...ag,
							name: this.formatAttendeeGroupName(ag.name)
						};
					});
					return response;
				})
				.catch(error => {
					console.error('Error fetching Attendee Types:', error);
					this.errors.push('Failed to fetch Attendee Types.');
					throw error;
				});
		},
		async fetchEventSet() {
			await axios.get('/api/event-set', {
					headers: {
						'Authorization': `Bearer ${this.token}`
					}
				})
				.then(response => {
					const event = response.data;
					this.event = event;
					this.location = event.location;
					this.fetchAttendees();
					return response;
				})
				.catch(error => {
					console.error('Error fetching event set:', error);
					this.errors.push('Failed to fetch event set.');
					throw error;
				});
		},
		handleSubmit() {
			this.errors = [];
			if (this.attendeeForm.id) {
				this.updateAttendee();
			} else {
				this.addAttendee();
			}
		},
		addAttendee() {
			axios.post('/api/users', {
					name: this.attendeeForm.name,
					company_id: this.attendeeForm.company_id,
					attendee_group_id: this.attendeeForm.attendee_group_id,
					confirmed: this.attendeeForm.confirmed ? 1 : 0,
					event_id: this.event ? this.event.id : null
				}, {
					headers: {
						'Authorization': `Bearer ${this.token}`
					}
				})
				.then(response => {
					this.fetchAttendees(this.attendees.current_page);
					this.closeModal();
				})
				.catch(error => {
					if (error.response && error.response.data.errors) {
						this.errors = error.response.data.errors;
					} else {
						this.errors.push('Failed to add attendee.');
					}
				});
		},
		updateAttendee() {
			axios.put(`/api/users/${this.attendeeForm.id}`, {
					name: this.attendeeForm.name,
					company_id: this.attendeeForm.company_id,
					attendee_group_id: this.attendeeForm.attendee_group_id,
					confirmed: this.attendeeForm.confirmed ? 1 : 0,
					event_id: this.event ? this.event.id : null
				}, {
					headers: {
						'Authorization': `Bearer ${this.token}`
					}
				})
				.then(response => {
					this.fetchAttendees(this.attendees.current_page);
					this.closeModal();
				})
				.catch(error => {
					if (error.response && error.response.data.errors) {
						this.errors = error.response.data.errors;
					} else {
						this.errors.push('Failed to update attendee.');
					}
				});
		},
		confirmDelete(attendeeId, attendeeName) {
			this.confirmDeleteId = attendeeId;
			this.confirmDeleteName = attendeeName;
			this.showConfirmDelete = true;
		},
		deleteAttendee(attendeeId) {
			axios.delete(`/api/users/${attendeeId}`, {
					headers: {
						'Authorization': `Bearer ${this.token}`
					}
				})
				.then(() => {
					this.fetchAttendees(this.attendees.current_page);
					this.closeConfirmDelete();
				})
				.catch(error => {
					console.error('Error deleting attendee:', error);
					this.errors.push('Failed to delete attendee.');
					this.closeConfirmDelete();
				});
		},
		closeConfirmDelete() {
			this.showConfirmDelete = false;
			this.confirmDeleteId = null;
			this.confirmDeleteName = '';
		},
		openModal(type, attendee = {}) {
			this.errors = [];
			if (type === 'edit') {
				this.modalTitle = 'Edit Attendee';
				this.attendeeForm.id = attendee.id;
				this.attendeeForm.name = attendee.name;
				this.attendeeForm.company_id = attendee.company_id;
				this.attendeeForm.attendee_group_id = attendee.attendee_group_id;
				this.attendeeForm.confirmed = attendee.events && attendee.events.length > 0 ? attendee.events[0].pivot.confirmed === 1 : false;
			} else {
				this.modalTitle = 'Add Attendee';
				this.attendeeForm.id = null;
				this.attendeeForm.name = '';
				this.attendeeForm.company_id = '';
				this.attendeeForm.attendee_group_id = '';
				this.attendeeForm.confirmed = false;
			}
			this.showModal = true;
		},
		closeModal() {
			this.showModal = false;
		},
		attendeeDetails(attendee) {
			this.detailsAttendee = attendee;
			this.showDetailsModal = true;
		},
		closeDetailsModal() {
			this.showDetailsModal = false;
			this.detailsAttendee = {
				name: '',
				company: {
					name: ''
				},
				attendee_group: {
					name: ''
				}
			};
		},
		toggleConfirmed(attendee) {
			const confirmed = attendee.events && attendee.events.length > 0 ? (attendee.events[0].pivot.confirmed === 1 ? 0 : 1) : 1;
			axios.put(`/api/users/${attendee.id}/toggle-confirmed`, {
					event_id: this.event.id,
					confirmed: confirmed
				}, {
					headers: {
						'Authorization': `Bearer ${this.token}`
					}
				})
				.then(response => {
					this.fetchAttendees(this.attendees.current_page);
					this.updateConfirmedCount();
				})
				.catch(error => {
					console.error('Error toggling confirmed status:', error);
					this.errors.push('Failed to update confirmed status.');
				});
		},
		openFilterModal() {
			this.showFilterModal = true;
		},
		closeFilterModal() {
			this.showFilterModal = false;
		},
		applyFilters() {
			this.fetchAttendees();
			this.closeFilterModal();
		},
		formatAttendeeGroupName(name) {
			if (!name) return '';
			return name.slice(0, -1);
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
		exportRegisteredAttendeesToPdf() {
			axios.post('api/export/registered-attendees-to-pdf', {
				attendees: this.attendees.data,
				event: this.event
			}, {
				headers: {
					'Authorization': `Bearer ${this.token}`
				},
				responseType: 'blob'
			}).then(response => {
				const url = window.URL.createObjectURL(new Blob([response.data]));
				const link = document.createElement('a');
				link.href = url;
				link.setAttribute('download', 'registered_attendees.pdf');
				document.body.appendChild(link);
				link.click();
				link.remove();
			}).catch(error => {
				console.error('Error exporting to PDF:', error);
			});
		},
		exportRegisteredAttendeesToXlsx() {
			axios.post('api/export/registered-attendees-to-xlsx', {
				attendees: this.attendees.data,
				event: this.event
			}, {
				headers: {
					'Authorization': `Bearer ${this.token}`
				},
				responseType: 'blob'
			}).then(response => {
				const url = window.URL.createObjectURL(new Blob([response.data]));
				const link = document.createElement('a');
				link.href = url;
				link.setAttribute('download', 'registered_attendees.xlsx');
				document.body.appendChild(link);
				link.click();
				link.remove();
			}).catch(error => {
				console.error('Error exporting to XLSX:', error);
			});
		},
		async updateConfirmedCount() {
			await axios.get('/api/confirmed-attendees-per-event', {
					headers: {
						'Authorization': `Bearer ${this.token}`
					}
				})
				.then(response => {
					const eventData = response.data.find(e => e.event === this.event.name);
					this.confirmedAttendees = eventData ? eventData.confirmed_attendees : 0;
					return response;
				})
				.catch(error => {
					console.error('Error fetching confirmed attendees:', error);
					throw error;
				});
		}
	},
	async created() {
		try {
			await this.fetchCompanies();
			await this.fetchAttendeeGroups();
			await this.fetchEventSet();
			await this.updateConfirmedCount();
		} catch (error) {
			console.error('Error in one of the initialization methods:', error);
		}
	}
};
</script>
<style>
.multiselect {
  width: 400px;
  margin-bottom: 20px;
}
.multiselect__tag {
  display: inline-block;
  margin: 2px;
  padding: 4px 8px !important;
  background-color: #f4f4f4 !important; 
  border: 1px solid #ddd;
  cursor: grab;
  margin-right: 7px !important;
  color:var(--cui-table-color-state, var(--cui-table-color-type, var(--cui-table-color))) !important;
}

.multiselect__tag-icon:after { 
  color:var(--cui-table-color-state, var(--cui-table-color-type, var(--cui-table-color))) !important;
}

.multiselect__tag-icon {
  margin-left: 10px;
  cursor: pointer;
  position: relative !important;
  color:var(--cui-table-color-state, var(--cui-table-color-type, var(--cui-table-color))) !important;
}

.multiselect__tag-icon:after:hover { color: var(--cui-primary) !important; }

.multiselect__option--highlight { 
  background-color: var(--cui-primary) !important; 
  color: white !important; 
}
</style>


