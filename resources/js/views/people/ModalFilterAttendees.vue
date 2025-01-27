<template>
  <CModal :visible="showFilterModal" :backdrop="'static'" @close="closeFilterModal">
    <CModalHeader @close="closeFilterModal">
      <CModalTitle>Filter Attendees</CModalTitle>
    </CModalHeader>
    <CModalBody>
      <form @submit.prevent="applyFilters">
        <div class="row mb-3">
          <span class="col-3">
            <label for="filterName" class="form-label">Name</label>
          </span>
          <span class="col">
            <input type="text" class="form-control" id="filterName" v-model="filters.name" />
          </span>
        </div>
        <div class="row mb-3">
          <span class="col-3">
            <label for="filterCompany" class="form-label">Company</label>
          </span>
          <span class="col">
            <Multiselect id="filterCompany" v-model="filters.companies" :options="companies" track-by="id" label="name" :multiple="true" :show-labels="false" />
          </span>
        </div>
        <div class="row mb-3">
          <span class="col-3">
            <label for="filterAttendeeGroup" class="form-label">Attendee Type</label>
          </span>
          <span class="col">
            <Multiselect id="filterAttendeeGroup" v-model="filters.attendeeGroups" :options="attendeeGroups" track-by="id" label="name" :multiple="true" :show-labels="false" />
          </span>
        </div>
        <div class="row mb-3">
          <span class="col-3">
            <label for="filterConfirmed" class="form-label">Confirmed</label>
          </span>
          <span class="col">
            <Multiselect id="filterConfirmed" v-model="filters.confirmed" :options="confirmedOptions" track-by="value" label="text" :multiple="true" :show-labels="false" />
          </span>
        </div>
        <div class="text-end">
          <button type="submit" class="btn btn-primary">Apply</button>
        </div>
      </form>
    </CModalBody>
  </CModal>
</template>
<script>
import Multiselect from 'vue-multiselect';

export default {
  components: {
    Multiselect
  },
  props: {
    showFilterModal: Boolean,
    filters: Object,
    companies: Array,
    attendeeGroups: Array,
    confirmedOptions: Array
  },
  methods: {
    closeFilterModal() {
      this.$emit('close');
    },
    applyFilters() {
      this.$emit('apply');
    }
  }
};
</script>
