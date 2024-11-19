<template>
  <div class="container">
    <div class="form-container">
      <div class="q-pa-md" style="max-width: 400px">
        <h2>Inicio de sesión</h2>
        <q-form @submit.prevent="onSubmit" @reset="onReset" class="q-gutter-md">
          <q-input
            type="text"
            v-model="username"
            placeholder="Nombre de usuario"
            required
            lazy-rules
            :rules="[
              (val) => (val && val.length > 0) || 'Por favor escribe algo',
            ]"
          />
          <q-input
            type="password"
            v-model="password"
            placeholder="Contraseña"
            required
            lazy-rules
            :rules="[(val) => (val && val.length > 0) || 'Por favor escribe']"
          />
          <q-btn label="Acceder" type="submit" color="primary" />
          <q-btn
            label="Reiniciar"
            type="reset"
            color="primary"
            flat
            class="q-ml-sm"
          />
        </q-form>
        <q-btn
          label="Registrarse"
          @click="goToRegister"
          color="secondary"
          class="q-mt-md"
        />
        <p v-if="mensaje">{{ mensaje }}</p>
      </div>
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
      mensaje: "",
    };
  },
  created() {
    this.cleanStorage();
  },
  methods: {
    cleanStorage() {
      localStorage.clear();
    },
    onSubmit() {
      axios
        .post(
          "http://localhost/cardsndecks/cardsndecks-project/src/back/login.php",
          {
            username: this.username,
            password: this.password,
          }
        )
        .then((response) => {
          this.mensaje = response.data.message;
          if (response.data.user) {
            localStorage.setItem("user", response.data.user.id);
            this.$router.push("/cartas");
          }
        })
        .catch((error) => {
          this.mensaje = "Error al iniciar sesión";
          console.error("Error:", error);
        });
    },
    onReset() {
      this.username = null;
      this.password = null;
    },
    goToRegister() {
      this.$router.push("/register");
    },
  },
};
</script>

<style scoped>
.container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-image: url("../img/wpp.jpg");
}
.form-container {
  border: 1px solid black;
  border-radius: 5%;
  margin: 50px;
  padding: 40px;
  background-color: white;
}
</style>
