<template>
  <CDropdown placement="bottom-end" variant="nav-item">
    <CDropdownToggle class="py-0 pe-0" :caret="false">
      <CIcon icon="cil-user" size="lg" /> {{ username }}
    </CDropdownToggle>
    <CDropdownMenu class="pt-0">
      <CDropdownItem @click="logout">
        <CIcon icon="cil-lock-locked" /> Logout
      </CDropdownItem>
    </CDropdownMenu>
  </CDropdown>
</template>
<script>
import axios from 'axios';

export default {
  data() {
    return {
      username: localStorage.getItem('username') || 'Guest'
    };
  },
  methods: {
    logout() {
      const token = localStorage.getItem('token');
      axios.post('/api/logout', {}, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      })
      .then(() => {
        localStorage.removeItem('token');
        localStorage.removeItem('username');
        this.$router.push('/login');
      })
      .catch(error => {
        console.error('Logout failed:', error);
      });
    }
  }
};
</script>


