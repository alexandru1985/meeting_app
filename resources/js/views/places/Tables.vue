<template>
  <CRow>
    <CCol :xs="12">
      <CCard class="mb-4">
        <CCardHeader>
          <strong>Tables</strong>
          <button class="btn btn-primary float-end" @click="openModal('add')"><i class="bi bi-plus-lg me-1"></i>Add Table</button>
        </CCardHeader>
        <CCardBody>
          <CTable>
            <CTableHead>
              <CTableRow>
                <CTableHeaderCell scope="col" class="w-5">Id</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-30">Name</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-15">Seats</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-30">Location</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-20">Actions</CTableHeaderCell>
              </CTableRow>
            </CTableHead>
            <CTableBody>
              <CTableRow v-for="table in tables.data" :key="table.id">
                <CTableDataCell>{{ table.id }}</CTableDataCell>
                <CTableDataCell>{{ table.name }}</CTableDataCell>
                <CTableDataCell>{{ table.seats }}</CTableDataCell>
                <CTableDataCell>{{ table.location.name }}</CTableDataCell>
                <CTableDataCell>
                  <div class="btn-group" role="group">
                    <button class="btn btn-primary text-white" @click="tableDetails(table)">
                      <i class="bi bi-card-text"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary" @click="openModal('edit', table)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary text-white" @click="confirmDelete(table.id, table.name)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </CTableDataCell>
              </CTableRow>
            </CTableBody>
          </CTable>
          <CPagination v-if="tables.data.length > 0" class="float-end" aria-label="Page navigation example">
            <CPaginationItem class="cursor-pointer"
              :disabled="tables.current_page === 1"
              @click="fetchTables(tables.current_page - 1)">
              <span aria-hidden="true">&laquo;</span>
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              v-for="page in tables.last_page"
              :key="page"
              :active="page === tables.current_page"
              @click="fetchTables(page)">
              {{ page }}
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              :disabled="tables.current_page === tables.last_page"
              @click="fetchTables(tables.current_page + 1)">
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
      <!-- Modal Add/Edit Table -->
      <CModal :visible="showModal" @close="closeModal">
        <CModalHeader @close="closeModal">
            <CModalTitle>{{ modalTitle }}</CModalTitle>
        </CModalHeader>
        <CModalBody>
            <form @submit.prevent="handleSubmit">
              <div class="row mb-3">
                <span class="col-3">
                  <label for="tableName" class="form-label">Name</label>
                </span>
                <span class="col">
                  <input type="text" class="form-control" id="tableName" v-model="tableForm.name" />
                  <div v-if="errors.name" class="text-danger mt-2">{{ errors.name[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-3">
                  <label for="tableSeats" class="form-label">Seats</label>
                </span>
                <span class="col">
                  <input type="number" class="form-control" id="tableSeats" v-model="tableForm.seats" />
                  <div v-if="errors.seats" class="text-danger mt-2">{{ errors.seats[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-3">
                  <label for="tableLocation" class="form-label">Location</label>
                </span>
                <span class="col">
                  <CFormSelect v-model="tableForm.location_id" aria-label="Select location">
                    <option value="">Select Location</option>
                    <option v-for="location in locations" :key="location.id" :value="location.id">
                      {{ location.name }}
                    </option>
                  </CFormSelect>
                  <div v-if="errors.location_id" class="text-danger mt-2">{{ errors.location_id[0] }}</div>
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
          <CModalTitle>Delete Table</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <p>Do you want to delete {{ confirmDeleteName }}?</p>
        </CModalBody>
        <CModalFooter>
          <div class="btn-group" role="group">
            <CButton color="primary" @click="deleteTable(confirmDeleteId)">
              Yes
            </CButton>
            <div class="separator"></div>
            <CButton color="secondary" @click="closeConfirmDelete">
              Close
            </CButton>
          </div>
        </CModalFooter>
      </CModal>
      <!-- Modal Table Details -->
      <CModal
        :visible="showDetailsModal"
        @close="closeDetailsModal"
      >
        <CModalHeader @close="closeDetailsModal">
          <CModalTitle>Table Details</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <div class="row mb-3">
            <span class="col-3">
              Name
            </span>
            <span class="col">
              {{ detailsTable.name }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col-3">
              Seats
            </span>
            <span class="col">
               {{ detailsTable.seats }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col-3">
              Location
            </span>
            <span class="col">
              {{ detailsTable.location.name }}
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
      tables: {
        data: [],
        current_page: 1,
        last_page: 1,
      },
      locations: [],
      errors: [],
      modalTitle: '',
      tableForm: {
        id: null,
        name: '',
        seats: '',
        location_id: ''
      },
      showModal: false,
      showConfirmDelete: false,
      showDetailsModal: false,
      detailsTable: {
        name: '',
        seats: '',
        location: {
          name: ''
        }
      },
      confirmDeleteId: null,
      confirmDeleteName: ''
    };
  },
  methods: {
    fetchTables(page = 1) {
      axios.get(`/api/tables?page=${page}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.tables = response.data;
      })
      .catch(error => {
        console.error('Error fetching tables:', error);
        this.errors.push('Failed to fetch tables.');
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
    handleSubmit() {
      this.errors = [];
      if (this.tableForm.id) {
        this.updateTable();
      } else {
        this.addTable();
      }
    },
    addTable() {
      axios.post('/api/tables', {
        name: this.tableForm.name,
        location_id: this.tableForm.location_id,
        seats: this.tableForm.seats
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchTables(this.tables.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to add table.');
        }
      });
    },
    updateTable() {
      axios.put(`/api/tables/${this.tableForm.id}`, {
        name: this.tableForm.name,
        location_id: this.tableForm.location_id,
        seats: this.tableForm.seats
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchTables(this.tables.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to update table.');
        }
      });
    },
    deleteTable(tableId) {
      axios.delete(`/api/tables/${tableId}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(() => {
        this.fetchTables(this.tables.current_page);
        this.closeConfirmDelete();
      })
      .catch(error => {
        console.error('Error deleting table:', error);
        this.errors.push('Failed to delete table.');
        this.closeConfirmDelete();
      });
    },
    confirmDelete(tableId, tableName) {
      this.confirmDeleteId = tableId;
      this.confirmDeleteName = tableName;
      this.showConfirmDelete = true;
    },
    closeConfirmDelete() {
      this.showConfirmDelete = false;
      this.confirmDeleteId = null;
      this.confirmDeleteName = '';
    },
    openModal(type, table = {}) {
      this.errors = [];
      if (type === 'edit') {
        this.modalTitle = 'Edit Table';
        this.tableForm.id = table.id;
        this.tableForm.name = table.name;
        this.tableForm.location_id = table.location_id;
        this.tableForm.seats = table.seats;
      } else {
        this.modalTitle = 'Add Table';
        this.tableForm.id = null;
        this.tableForm.name = '';
        this.tableForm.location_id = '';
        this.tableForm.seats = '';
      }
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    tableDetails(table) {
      this.detailsTable = table;
      this.showDetailsModal = true;
    },
    closeDetailsModal() {
      this.showDetailsModal = false;
      this.detailsTable = {
        name: '',
        seats: '',
        location: {
          name: ''
        }
      };
    }
  },
  created() {
    this.fetchTables();
    this.fetchLocations();
  }
};
</script>
