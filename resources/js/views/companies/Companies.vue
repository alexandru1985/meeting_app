<template>
  <CRow>
    <CCol :xs="12">
      <CCard class="mb-4">
        <CCardHeader>
          <strong>Companies</strong>
          <button class="btn btn-primary float-end" @click="openModal('add')"><i class="bi bi-plus-lg me-1"></i>Add Company</button>
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
              <CTableRow v-for="company in companies.data" :key="company.id">
                <CTableDataCell>{{ company.id }}</CTableDataCell>
                <CTableDataCell>{{ company.name }}</CTableDataCell>
                <CTableDataCell>
                  <div class="btn-group" role="group">
                    <button class="btn btn-primary text-white" @click="companyDetails(company)">
                      <i class="bi bi-card-text"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary" @click="openModal('edit', company)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary text-white" @click="confirmDelete(company.id, company.name)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </CTableDataCell>
              </CTableRow>
            </CTableBody>
          </CTable>
          <CPagination v-if="companies.data.length > 0" class="float-end" aria-label="Page navigation example">
            <CPaginationItem class="cursor-pointer"
              :disabled="companies.current_page === 1"
              @click="fetchCompanies(companies.current_page - 1)">
              <span aria-hidden="true">&laquo;</span>
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              v-for="page in companies.last_page"
              :key="page"
              :active="page === companies.current_page"
              @click="fetchCompanies(page)">
              {{ page }}
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              :disabled="companies.current_page === companies.last_page"
              @click="fetchCompanies(companies.current_page + 1)">
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
      <!-- Modal Add/Edit Company -->
      <CModal :visible="showModal" @close="closeModal">
        <CModalHeader @close="closeModal">
            <CModalTitle>{{ modalTitle }}</CModalTitle>
        </CModalHeader>
        <CModalBody>
            <form @submit.prevent="handleSubmit">
              <div class="row mb-3">
                <span class="col-2">
                  <label for="companyName" class="form-label">Name</label>
                </span>
                <span class="col">
                  <input type="text" class="form-control" id="companyName" v-model="companyForm.name" />
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
          <CModalTitle>Delete Company</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <p>Do you want to delete {{ confirmDeleteName }}?</p>
        </CModalBody>
        <CModalFooter>
          <div class="btn-group" role="group">
            <CButton color="primary" @click="deleteCompany(confirmDeleteId)">
              Yes
            </CButton>
            <div class="separator"></div>
            <CButton color="secondary" @click="closeConfirmDelete">
              Close
            </CButton>
          </div>
        </CModalFooter>
      </CModal>
      <!-- Modal Company Details -->
      <CModal
        :visible="showDetailsModal"
        @close="closeDetailsModal"
      >
        <CModalHeader @close="closeDetailsModal">
          <CModalTitle>Company Details</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <div class="row mb-3">
            <span class="col-2">
              Name
            </span>
            <span class="col">
              {{ detailsCompany.name }}
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
      companies: {
        data: [],
        current_page: 1,
        last_page: 1,
      },
      errors: [],
      modalTitle: '',
      companyForm: {
        id: null,
        name: ''
      },
      showModal: false,
      showConfirmDelete: false,
      showDetailsModal: false,
      detailsCompany: {
        name: ''
      },
      confirmDeleteId: null,
      confirmDeleteName: ''
    };
  },
  methods: {
    fetchCompanies(page = 1) {
      axios.get(`/api/companies?page=${page}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.companies = response.data;
      })
      .catch(error => {
        console.error('Error fetching companies:', error);
        this.errors.push('Failed to fetch companies.');
      });
    },
    handleSubmit() {
      this.errors = [];
      if (this.companyForm.id) {
        this.updateCompany();
      } else {
        this.addCompany();
      }
    },
    created() {
    this.fetchCompanies();
  }
    addCompany() {
      axios.post('/api/companies', {
        name: this.companyForm.name
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchCompanies(this.companies.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to add company.');
        }
      });
    },
    updateCompany() {
      axios.put(`/api/companies/${this.companyForm.id}`, {
        name: this.companyForm.name
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchCompanies(this.companies.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to update company.');
        }
      });
    },
    deleteCompany(companyId) {
      axios.delete(`/api/companies/${companyId}`, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(() => {
        this.fetchCompanies(this.companies.current_page);
        this.closeConfirmDelete();
      })
      .catch(error => {
        console.error('Error deleting company:', error);
        this.errors.push('Failed to delete company.');
        this.closeConfirmDelete();
      });
    },
    confirmDelete(companyId, companyName) {
      this.confirmDeleteId = companyId;
      this.confirmDeleteName = companyName;
      this.showConfirmDelete = true;
    },
    closeConfirmDelete() {
      this.showConfirmDelete = false;
      this.confirmDeleteId = null;
      this.confirmDeleteName = '';
    },
    openModal(type, company = {}) {
      this.errors = [];
      if (type === 'edit') {
        this.modalTitle = 'Edit Company';
        this.companyForm.id = company.id;
        this.companyForm.name = company.name;
      } else {
        this.modalTitle = 'Add Company';
        this.companyForm.id = null;
        this.companyForm.name = '';
      }
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    companyDetails(company) {
      this.detailsCompany = company;
      this.showDetailsModal = true;
    },
    closeDetailsModal() {
      this.showDetailsModal = false;
      this.detailsCompany = {
        name: ''
      };
    }
  },
  created() {
    this.fetchCompanies();
  }
};
</script>