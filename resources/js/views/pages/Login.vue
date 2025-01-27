<template>
  <div class="wrapper min-vh-100 d-flex flex-column align-items-center mt-5">
    <CContainer>
      <CRow class="justify-content-center">
        <CCol :md="4">
          <CCard class="p-4">
            <CCardBody>
              <CForm @submit.prevent="login">
                <h3 class="mb-3">Login</h3>
                <CInputGroup class="mb-3">
                  <CInputGroupText>
                    <CIcon icon="cil-user" />
                  </CInputGroupText>
                  <CFormInput
                    type="email"
                    v-model="email"
                    placeholder="Username"
                    autocomplete="username"
                  />
                </CInputGroup>
                <CInputGroup class="mb-4">
                  <CInputGroupText>
                    <CIcon icon="cil-lock-locked" />
                  </CInputGroupText>
                  <CFormInput
                    type="password"
                    v-model="password"
                    placeholder="Password"
                    autocomplete="current-password"
                  />
                </CInputGroup>
                <div v-if="errorMessage" class="text-danger mb-3">{{ errorMessage }}</div>
                <CRow>
                  <CCol :xs="6">
                    <CButton color="primary" class="px-4" @click="login"> Login </CButton>
                  </CCol>
                </CRow>
              </CForm>
            </CCardBody>
          </CCard>
        </CCol>
      </CRow>
    </CContainer>
  </div>
</template>
<script>
import axios from 'axios'

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: ''
    }
  },
  methods: {
    login() {
      axios.post('/api/login', {
        email: this.email,
        password: this.password
      })
      .then(response => {
        this.errorMessage = '';
        const token = response.data.data.token.access_token;
        const username = response.data.data.name;
        localStorage.setItem('token', token);
        localStorage.setItem('username', username);
        if (localStorage.getItem('token') !== undefined) {
          this.$router.push('/dashboard');
        } else {
          this.$router.push('/');
        }

      })
      .catch(error => {
        if (error.response && error.response.data.statusCode === 401) {
          this.errorMessage = 'User or password is wrong.';
        } else {
          console.error('Login failed:', error);
        }
      })
    }
  }
}
</script>
