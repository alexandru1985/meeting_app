<template>
  <CRow>
    <CCol :xs="12">
      <CCard class="mb-4">
        <CCardHeader>
          <strong>Locations</strong>
          <button class="btn btn-primary float-end" @click="openModal('add')"><i class="bi bi-plus-lg me-1"></i>Add Location</button>
        </CCardHeader>
        <CCardBody>
          <CTable>
            <CTableHead>
              <CTableRow>
                <CTableHeaderCell scope="col" class="w-5">Id</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-15">Name</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-35">Address</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-15">Latitude</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-15">Longitude</CTableHeaderCell>
                <CTableHeaderCell scope="col" class="w-15">Actions</CTableHeaderCell>
              </CTableRow>
            </CTableHead>
            <CTableBody>
              <CTableRow v-for="location in locations.data" :key="location.id">
                <CTableDataCell>{{ location.id }}</CTableDataCell>
                <CTableDataCell>{{ location.name }}</CTableDataCell>
                <CTableDataCell>{{ location.address }}</CTableDataCell>
                <CTableDataCell>{{ location.latitude }}</CTableDataCell>
                <CTableDataCell>{{ location.longitude }}</CTableDataCell>
                <CTableDataCell>
                  <div class="btn-group" role="group">
                    <button class="btn btn-primary text-white" @click="locationDetails(location)">
                      <i class="bi bi-card-text"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary" @click="openModal('edit', location)">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <div class="separator"></div>
                    <button class="btn btn-primary text-white" @click="confirmDelete(location.id, location.name)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </CTableDataCell>
              </CTableRow>
            </CTableBody>
          </CTable>
          <CPagination v-if="locations.data.length > 0" class="float-end" aria-label="Page navigation example">
            <CPaginationItem class="cursor-pointer"
              :disabled="locations.current_page === 1"
              @click="fetchLocations(locations.current_page - 1)">
              <span aria-hidden="true">&laquo;</span>
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              v-for="page in locations.last_page"
              :key="page"
              :active="page === locations.current_page"
              @click="fetchLocations(page)">
              {{ page }}
            </CPaginationItem>
            <CPaginationItem class="cursor-pointer"
              :disabled="locations.current_page === locations.last_page"
              @click="fetchLocations(locations.current_page + 1)">
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
      <!-- Modal Add/Edit Location -->
      <CModal :visible="showModal" @close="closeModal">
        <CModalHeader @close="closeModal">
            <CModalTitle>{{ modalTitle }}</CModalTitle>
        </CModalHeader>
        <CModalBody>
            <form @submit.prevent="handleSubmit">
              <div class="row mb-3">
                <span class="col-2">
                  <label for="locationName" class="form-label">Name</label>
                </span>
                <span class="col">
                  <input type="text" class="form-control" id="locationName" v-model="locationForm.name" />
                  <div v-if="errors.name" class="text-danger mt-2">{{ errors.name[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-2">
                  <label for="locationAddress" class="form-label">Address</label>
                </span>
                <span class="col">
                  <input type="text" class="form-control" id="locationAddress" v-model="locationForm.address" />
                  <div v-if="errors.address" class="text-danger mt-2">{{ errors.address[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-2">
                  <label for="locationLatitude" class="form-label">Latitude</label>
                </span>
                <span class="col">
                  <input type="text" class="form-control" id="locationLatitude" v-model="locationForm.latitude" />
                  <div v-if="errors.latitude" class="text-danger mt-2">{{ errors.latitude[0] }}</div>
                </span>
              </div>
              <div class="row mb-3">
                <span class="col-2">
                  <label for="locationLongitude" class="form-label">Longitude</label>
                </span>
                <span class="col">
                  <input type="text" class="form-control" id="locationLongitude" v-model="locationForm.longitude" />
                  <div v-if="errors.longitude" class="text-danger mt-2">{{ errors.longitude[0] }}</div>
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
          <CModalTitle>Delete Location</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <p>Do you want to delete {{ confirmDeleteName }}?</p>
        </CModalBody>
        <CModalFooter>
          <div class="btn-group" role="group">
            <CButton color="primary" @click="deleteLocation(confirmDeleteId)">
              Yes
            </CButton>
            <div class="separator"></div>
            <CButton color="secondary" @click="closeConfirmDelete">
              Close
            </CButton>
          </div>
        </CModalFooter>
      </CModal>
      <CModal
        :visible="showDetailsModal"
        @close="closeDetailsModal"
      >
        <CModalHeader @close="closeDetailsModal">
          <CModalTitle>Location Details</CModalTitle>
        </CModalHeader>
        <CModalBody>
          <div class="row mb-3">
            <span class="col-2">
              Name
            </span>
            <span class="col">
              {{ detailsLocation.name }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col-2">
              Address
            </span>
            <span class="col">
              {{ detailsLocation.address }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col-2">
              Latitude
            </span>
            <span class="col">
              {{ detailsLocation.latitude }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col-2">
              Longitude
            </span>
            <span class="col">
              {{ detailsLocation.longitude }}
            </span>
          </div>
          <div class="row mb-3">
            <span class="col">
              <div style="height: 350px;">
                <l-map :center="center" :zoom="zoom" style="height: 350px;">
                  <l-tile-layer :url="url" :attribution="attribution"></l-tile-layer>
                  <l-marker :lat-lng="markerLatLng"></l-marker>
                </l-map>
              </div>
            </span>
          </div>
        </CModalBody>
      </CModal>
    </CCol>
  </CRow>
</template>
<script>
import axios from 'axios';
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LMarker } from '@vue-leaflet/vue-leaflet';

export default {
  components: {
    LMap,
    LTileLayer,
    LMarker
  },
  data() {
    return {
      token: localStorage.getItem('token') || '',
      locations: {
        data: [],
        current_page: 1,
        last_page: 1
      },
      errors: [],
      modalTitle: '',
      locationForm: {
        id: null,
        name: '',
        address: '',
        latitude: '',
        longitude: ''
      },
      showModal: false,
      showConfirmDelete: false,
      showDetailsModal: false,
      detailsLocation: {
        name: '',
        address: '',
        latitude: '',
        longitude: ''
      },
      confirmDeleteId: null,
      confirmDeleteName: '',
      url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
      attribution: '&copy; <a target="_blank" href="http://osm.org/copyright">OpenStreetMap</a> contributors',
      zoom: 13,
      center: [47.313220, -1.319482],  // Default values
      markerLatLng: [47.313220, -1.319482]  // Default values
    };
  },
  methods: {
    fetchLocations(page = 1) {
      axios.get(`/api/locations?page=${page}`, {
        headers: {
          Authorization: `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.locations = response.data;
      })
      .catch(error => {
        console.error('Error fetching locations:', error);
        this.errors.push('Failed to fetch locations.');
      });
    },
    handleSubmit() {
      this.errors = [];
      if (this.locationForm.id) {
        this.updateLocation();
      } else {
        this.addLocation();
      }
    },
    addLocation() {
      axios.post('/api/locations', {
        name: this.locationForm.name,
        address: this.locationForm.address,
        latitude: this.locationForm.latitude,
        longitude: this.locationForm.longitude
      }, {
        headers: {
          Authorization: `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchLocations(this.locations.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to add location.');
        }
      });
    },
    updateLocation() {
      axios.put(`/api/locations/${this.locationForm.id}`, {
        name: this.locationForm.name,
        address: this.locationForm.address,
        latitude: this.locationForm.latitude,
        longitude: this.locationForm.longitude
      }, {
        headers: {
          Authorization: `Bearer ${this.token}`
        }
      })
      .then(response => {
        this.fetchLocations(this.locations.current_page);
        this.closeModal();
      })
      .catch(error => {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors.push('Failed to update location.');
        }
      });
    },
    deleteLocation(locationId) {
      axios.delete(`/api/locations/${locationId}`, {
        headers: {
          Authorization: `Bearer ${this.token}`
        }
      })
      .then(() => {
        this.fetchLocations(this.locations.current_page);
        this.closeConfirmDelete();
      })
      .catch(error => {
        console.error('Error deleting location:', error);
        this.errors.push('Failed to delete location.');
        this.closeConfirmDelete();
      });
    },
    confirmDelete(locationId, locationName) {
      this.confirmDeleteId = locationId;
      this.confirmDeleteName = locationName;
      this.showConfirmDelete = true;
    },
    closeConfirmDelete() {
      this.showConfirmDelete = false;
      this.confirmDeleteId = null;
      this.confirmDeleteName = '';
    },
    openModal(type, location = {}) {
      this.errors = [];
      if (type === 'edit') {
        this.modalTitle = 'Edit Location';
        this.locationForm.id = location.id;
        this.locationForm.name = location.name;
        this.locationForm.address = location.address;
        this.locationForm.latitude = location.latitude;
        this.locationForm.longitude = location.longitude;
      } else {
        this.modalTitle = 'Add Location';
        this.locationForm.id = null;
        this.locationForm.name = '';
        this.locationForm.address = '';
        this.locationForm.latitude = '';
        this.locationForm.longitude = '';
      }
      this.showModal = true;
    },
    closeModal() {
      this.showModal = false;
    },
    locationDetails(location) {
      this.detailsLocation = location;
      this.center = [location.latitude, location.longitude];
      this.markerLatLng = [location.latitude, location.longitude];
      this.showDetailsModal = true;
    },
    closeDetailsModal() {
      this.showDetailsModal = false;
      this.detailsLocation = {
        name: '',
        address: '',
        latitude: '',
        longitude: ''
      };
      this.center = [47.313220, -1.319482];  // Reset to default
      this.markerLatLng = [47.313220, -1.319482];  // Reset to default
    }
  },
  created() {
    this.fetchLocations();
  }
};
</script>


