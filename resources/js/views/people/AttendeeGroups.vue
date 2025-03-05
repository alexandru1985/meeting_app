<template>
  <CRow>
    <CCol :xs="12">
      <CCard class="mb-4">
        <CCardHeader>
          <strong>Attendee Groups</strong>
          <button class="btn btn-primary float-end" @click="openModal('add')"><i class="bi bi-plus-lg me-1"></i>Add Attendee Group</button>
        </CCardHeader>
        <CCardBody>
          <CTable>
            <CTableHead>
              <CTableRow>
                <CTableHeaderCell scope="col" class="w-5">Id</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-75">Name</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-20">Actions</CTableHeaderCell>
              </CTableRow>
            </CTableHead>
            <CTableBody>
              <CTableRow v-for="attendeeGroup in attendeeGroups.data" :key="attendeeGroup.id">
                <CTableDataCell>{{ attendeeGroup.id }}</CTableDataCell>
                <CTableDataCell>{{ attendeeGroup.name }}</CTableDataCell>
                <CTableDataCell>
                  <div class="btn-group" role="group">
                    <button class="btn btn-primary text-white" @click="attendeeGroupDetails(attendeeGroup)">
                      <i class="bi bi-card-text"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary" @click="openModal('edit', attendeeGroup)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary text-white" @click="confirmDelete(attendeeGroup.id, attendeeGroup.name)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </CTableDataCell>
              </CTableRow>
            </CTableBody>
          </CTable>
          <CPagination v-if="attendeeGroups.data.length > 0" class="float-end" aria-label="Page navigation example">
            <CPaginationItem class="cursor-pointer"
              :disabled="attendeeGroups.current_page === 1"
              @click="fetchattendeeGroups(attendeeGroups.current_page - 1)">
              <span aria-hidden="true">&laquo;</span>
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              v-for="page in attendeeGroups.last_page"
              :key="page"
              :active="page === attendeeGroups.current_page"
              @click="fetchattendeeGroups(page)">
              {{ page }}
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              :disabled="attendeeGroups.current_page === attendeeGroups.last_page"
              @click="fetchattendeeGroups(attendeeGroups.current_page + 1)">
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
      <!-- Modal Add/Edit Attendee Group -->
      <CModal :visible="showModal" @close="closeModal">
        <CModalHeader @close="closeModal">
            <CModalTitle>{{ modalTitle }}</CModalTitle>
        </CModalHeader>
        <CModalBody>
            <form @submit.prevent="handleSubmit">
              <div class="row mb-3">
                <span class="col-2">
                  <label for="attendeeGroupName" class="form-label">Name</label>
                </span>
                <span class="col">
                  <input type="text" class="form-control" id="attendeeGroupName" v-model="attendeeGroupForm.name" />
                  <div v-if="errors.name" class="text-danger mt-2">{{ errors.name[0] }}</div>
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
          <CModalTitle>Delete Attendee Group</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <p>Do you want to delete {{ confirmDeleteName }}?</p>
        </CModalBody>
        <CModalFooter>
          <div class="btn-group" role="group">
            <CButton color="primary" @click="deleteattendeeGroup(confirmDeleteId)">
              Yes
            </CButton>
            <div class="separator"></div>
            <CButton color="secondary" @click="closeConfirmDelete">
              Close
            </CButton>
          </div>
        </CModalFooter>
      </CModal>
      <!-- Modal Attendee Group Details -->
      <CModal
        :visible="showDetailsModal"
        @close="closeDetailsModal"
      >
        <CModalHeader @close="closeDetailsModal">
          <CModalTitle>Attendee Group Details</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <div class="row mb-3">
            <span class="col-2">
              Name
            </span>
            <span class="col">
              {{ detailsattendeeGroup.name }}
            </span>
          </div>
        </CModalBody>
      </CModal>
    </CCol>
  </CRow>
</template>
<script>
import axios from 'axios';

export default {
  data() {
    return {
      token: localStorage.getItem('token') || '',
      attendeeGroups: {
        data: [],
        current_page: 1,
        last_page: 1,
      },
      errors: [],
      modalTitle: '',
      attendeeGroupForm: {
        id: null,
        name: ''
      },
      showModal: false,
      showConfirmDelete: false,
      showDetailsModal: false,
      detailsattendeeGroup: {
        name: ''
      },
      confirmDeleteId: null,
      confirmDeleteName: ''
    };
  },
  methods: {
    fetchattendeeGroups(page = 1) {
      axios.get(`/api/attendee-groups?page=${page}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.attendeeGroups = response.data;
      })
      .catch(error => {
        console.error('Error fetching attendee groups:', error);
        this.errors.push('Failed to fetch attendee groups.');
      });
    },
    handleSubmit() {
      this.errors = [];
      if (this.attendeeGroupForm.id) {
        this.updateattendeeGroup();
      } else {
        this.addattendeeGroup();
      }
    },
    addattendeeGroup() {
      axios.post('/api/attendee-groups', {
        name: this.attendeeGroupForm.name
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchattendeeGroups(this.attendeeGroups.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to add attendee group.');
        }
      });
    },
    updateattendeeGroup() {
      axios.put(`/api/attendee-groups/${this.attendeeGroupForm.id}`, {
        name: this.attendeeGroupForm.name
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchattendeeGroups(this.attendeeGroups.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to update attendee group.');
        }
      });
    },
    deleteattendeeGroup(attendeeGroupId) {
      axios.delete(`/api/attendee-groups/${attendeeGroupId}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(() => {
        this.fetchattendeeGroups(this.attendeeGroups.current_page);
        this.closeConfirmDelete();
      })
      .catch(error => {
        console.error('Error deleting attendee group:', error);
        this.errors.push('Failed to delete attendee group.');
        this.closeConfirmDelete();
      });
    },
    confirmDelete(attendeeGroupId, attendeeGroupName) {
      this.confirmDeleteId = attendeeGroupId;
      this.confirmDeleteName = attendeeGroupName;
      this.showConfirmDelete = true;
    },
    closeConfirmDelete() {
      this.showConfirmDelete = false;
      this.confirmDeleteId = null;
      this.confirmDeleteName = '';
    },
    openModal(type, attendeeGroup = {}) {
      this.errors = [];
      if (type === 'edit') {
        this.modalTitle = 'Edit Attendee Group';
        this.attendeeGroupForm.id = attendeeGroup.id;
        this.attendeeGroupForm.name = attendeeGroup.name;
      } else {
        this.modalTitle = 'Add Attendee Group';
        this.attendeeGroupForm.id = null;
        this.attendeeGroupForm.name = '';
      }
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    attendeeGroupDetails(attendeeGroup) {
      this.detailsattendeeGroup = attendeeGroup;
      this.showDetailsModal = true;
    },
    closeDetailsModal() {
      this.showDetailsModal = false;
      this.detailsattendeeGroup = {
        name: ''
      };
    }
  },
  created() {
    this.fetchattendeeGroups();
  }
};
</script>