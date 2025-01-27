<template>
  <CModal :visible="showConfirmDelete" @close="closeModal">
    <CModalHeader @close="closeModal">
      <CModalTitle>Delete Attendee</CModalTitle>
    </CModalHeader>
    <CModalBody>
      <p>Do you want to delete {{ confirmDeleteName }}?</p>
    </CModalBody>
    <CModalFooter>
      <div class="btn-group" role="group">
        <CButton color="primary" @click="confirmDelete">Yes</CButton>
        <div class="separator"></div>
        <CButton color="secondary" @click="closeModal">Close</CButton>
      </div>
    </CModalFooter>
  </CModal>
</template>
<script>
export default {
  props: {
    showConfirmDelete: Boolean,
    confirmDeleteName: String,
    confirmDeleteId: Number
  },
  methods: {
    closeModal() {
      this.$emit('close');
    },
    confirmDelete() {
      this.$emit('confirm', this.confirmDeleteId);
    }
  }
};
</script>

