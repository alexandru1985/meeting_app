<template>
  <CModal :visible="showModal" @close="closeModal">
    <CModalHeader @close="closeModal">
      <CModalTitle>{{ modalTitle }}</CModalTitle>
    </CModalHeader>
    <CModalBody>
      <form>
        <div class="row mb-3">
          <span class="col-3">
            <label for="attendeeName" class="form-label">Name</label>
          </span>
          <span class="col">
            <input type="text" class="form-control" id="attendeeName" v-model="attendeeForm.name" />
            <div v-if="errors.name" class="text-danger mt-2">{{ errors.name[0] }}</div>
          </span>
        </div>
        <div class="row mb-3">
          <span class="col-3">
            <label for="attendeeCompany" class="form-label">Company</label>
          </span>
          <span class="col">
            <CFormSelect v-model="attendeeForm.company_id" aria-label="Select company">
              <option value="">Select Company</option>
              <option v-for="company in companies" :key="company.id" :value="company.id">
                {{ company.name }}
              </option>
            </CFormSelect>
            <div v-if="errors.company_id" class="text-danger mt-2">{{ errors.company_id[0] }}</div>
          </span>
        </div>
        <div class="row mb-3">
          <span class="col-3">
            <label for="attendeeAttendeeGroup" class="form-label">Attendee Type</label>
          </span>
          <span class="col">
            <CFormSelect v-model="attendeeForm.attendee_group_id" aria-label="Select Attendee Type">
              <option value="">Select Attendee Type</option>
              <option v-for="attendeeGroup in attendeeGroups" :key="attendeeGroup.id" :value="attendeeGroup.id">
                {{ attendeeGroup.name }}
              </option>
            </CFormSelect>
            <div v-if="errors.attendee_group_id" class="text-danger mt-2">{{ errors.attendee_group_id[0] }}</div>
          </span>
        </div>
        <div class="row mb-3">
          <span class="col-3">
            <label for="confirmed" class="form-label">Confirmed</label>
          </span>
          <span class="col">
            <CFormSwitch id="confirmedSwitch" v-model="attendeeForm.confirmed" />
          </span>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
      </form>
    </CModalBody>
  </CModal>
</template>

<script>
export default {
  props: {
    showModal: Boolean,
    modalTitle: String,
    attendeeForm: Object,
    errors: Object,
    companies: Array,
    attendeeGroups: Array
  }
};
</script>
