<template>
  <div class="container">
    <div class="register q-pa-md" style="max-width: 400px">
      <h2>Registro de usuario</h2>
      <q-form @submit.prevent="registrarUsuario">
        <q-input
          type="text"
          v-model="username"
          placeholder="Nombre de usuario"
          required
          lazy-rules
          :rules="[(val) => !!val || 'Por favor ingresa algo']"
        />
        <q-input
          type="password"
          v-model="password"
          placeholder="Contraseña"
          required
          lazy-rules
          :rules="[(val) => !!val || 'Por favor ingresa algo']"
        />
        <q-input
          type="email"
          v-model="email"
          placeholder="Correo electrónico"
          required
        />
        <q-btn type="submit" label="Registrar" />
      </q-form>
      <q-btn
        label="Iniciar Sesión"
        @click="goToLogin"
        color="secondary"
        class="q-mt-md"
      />
      <p v-if="mensaje">{{ mensaje }}</p>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      username: "",
      password: "",
      email: "",
      mensaje: "",
    };
  },
  methods: {
    registrarUsuario() {
      axios
        .post(
          "http://localhost/cardsndecks/cardsndecks-project/src/back/register.php",
          {
            username: this.username,
            password: this.password,
            email: this.email,
          }
        )
        .then((response) => {
          this.mensaje = response.data.message;
          if (response.status === 201) {
            this.username = "";
            this.password = "";
            this.email = "";
          }
        })
        .catch((error) => {
          if (error.response && error.response.status === 400) {
            this.mensaje = error.response.data.message;
          } else {
            this.mensaje = "Error al registrar el usuario";
            console.error("Error:", error);
          }
        });
    },
    goToLogin() {
      this.$router.push("/login");
    },
  },
};
</script>

<style>
.container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-image: url("../img/wpp.jpg");
}
.register {
  border: 1px solid black;
  border-radius: 5%;
  margin: 70px;
  padding: 40px;
  background-color: white;
}
</style>
