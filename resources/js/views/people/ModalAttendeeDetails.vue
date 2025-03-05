<template>
  <CModal :visible="showDetailsModal">
    <CModalHeader>
      <CModalTitle>Attendee Details</CModalTitle>
    </CModalHeader>
    <CModalBody>
      <div class="row mb-3">
        <span class="col-3">Name</span>
        <span class="col">{{ detailsAttendee.name }}</span>
      </div>
      <div class="row mb-3">
        <span class="col-3">Company</span>
        <span class="col">{{ detailsAttendee.company.name }}</span>
      </div>
      <div class="row mb-3">
        <span class="col-3">Attendee Type</span>
        <span class="col">{{ formatAttendeeGroupName(detailsAttendee.attendee_group?.name) }}</span>
      </div>
      <div class="row mb-3">
        <span class="col-3">
          <label for="eventIsSet" class="form-label">Confirmed</label>
        </span>
        <span class="col">
          {{ this.detailsAttendee.events[0]?.pivot.confirmed === 1 ? 'Yes' : 'No' }}
        </span>
      </div>
    </CModalBody>
  </CModal>
</template>
<script>
export default {
  props: {
    showDetailsModal: Boolean,
    detailsAttendee: Object
  },
  methods: {
    formatAttendeeGroupName(name) {
      if (!name) return '';
      return name.slice(0, -1);
    }
  }
};
</script>
