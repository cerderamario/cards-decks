<template>
  <div class="containerDB" v-if="logueado">
    <q-layout view="lHh Lpr lFf">
      <q-header class="navbar bg-blue-grey-9 text-white shadow-2">
        <q-toolbar class="rounded-borders">
          <q-btn flat label="Cards&Decks" />
          <q-space />
          <q-tabs v-model="tab" shrink>
            <q-tab
              name="crearBaraja"
              label="Crear Baraja"
              @click="irACrearBaraja"
            />
            <q-tab
              name="misBarajas"
              label="Mis Barajas"
              @click="irAMisBarajas"
            />
            <q-tab
              name="cerrarSesion"
              label="Cerrar sesión"
              @click="cerrarSesion"
            >
              <q-icon name="exit_to_app" />
            </q-tab>
          </q-tabs>
        </q-toolbar>
      </q-header>

      <q-page-container class="content">
        <div class="background" v-if="tab === 'crearBaraja'">
          <div class="cartas-container">
            <div class="imagen-grande" v-if="imagenGrande">
              <img :src="imagenGrande" alt="Imagen de la carta en grande" />
            </div>
            <div
              style="
                max-height: 600px;
                border-radius: 7%;
                border: 2px solid black;
                margin-right: 15px;
              "
              class="col-12 col-md-4 cartas-disponibles"
            >
              <h3>Cartas Disponibles</h3>
              <q-input
                filled
                v-model="busqueda"
                placeholder="Buscar cartas..."
              />
              <div>
                <q-option-group
                  v-model="filtroTipo"
                  :options="[
                    { label: 'All', value: '', color: 'blue' },
                    { label: 'Monsters', value: 'Monster', color: 'yellow' },
                    {
                      label: 'Spell Cards',
                      value: 'Spell Card',
                      color: 'green',
                    },
                    {
                      label: 'Trap Cards',
                      value: 'Trap Card',
                      color: 'purple',
                    },
                  ]"
                  inline
                  bordered
                  type="radio"
                />
              </div>
              <div
                style="width: 500px; max-height: 300px; overflow-y: scroll"
                class="lista-cartas"
              >
                <div v-for="carta in filteredCartas" :key="carta.id">
                  <q-list style="border: 1px solid black">
                    <q-item clickable v-ripple>
                      <q-item-section thumbnail>
                        <img
                          :src="carta.card_images[0].image_url"
                          :alt="carta.name"
                          @mouseover="
                            mostrarImagenGrande(carta.card_images[0].image_url)
                          "
                          @mouseleave="ocultarImagenGrande"
                        />
                      </q-item-section>
                      <q-item-section>{{ carta.name }}</q-item-section>
                      <q-item-section>{{ carta.type }}</q-item-section>
                      <q-item-section>
                        <img
                          class="add"
                          @click="anadirCarta(carta)"
                          src="../img/add_icon.png"
                        />
                      </q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
            </div>

            <div
              style="
                max-height: 600px;
                border-radius: 7%;
                border: 2px solid black;
                margin-right: 15px;
              "
              class="baraja-list"
            >
              <h4>Mi Baraja</h4>
              <q-btn
                class="btn-guardar"
                label="Guardar Baraja"
                @click="promptGuardarBaraja"
                color="primary"
              />
              <div
                style="width: 600px; max-height: 350px; overflow-y: scroll"
                v-if="barajaCartas.length"
              >
                <div v-for="carta in barajaCartas" :key="carta.id">
                  <q-list style="border: 1px solid black">
                    <q-item clickable v-ripple>
                      <q-item-section thumbnail>
                        <img
                          :src="carta.card_images[0].image_url"
                          :alt="carta.name"
                          @mouseover="
                            mostrarImagenGrande(carta.card_images[0].image_url)
                          "
                          @mouseleave="ocultarImagenGrande"
                        />
                      </q-item-section>
                      <q-item-section>{{ carta.name }}</q-item-section>
                      <q-item-section>{{ carta.type }}</q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
              <div v-else>
                <p>No has añadido cartas a tu baraja aún.</p>
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="tab === 'misBarajas'">
          <my-decks @imagenGrande="mostrarImagenGrande" />
        </div>
      </q-page-container>

      <q-footer class="footer" elevated>
        <q-toolbar>
          <q-toolbar-title>Mario Cerdera ©</q-toolbar-title>
        </q-toolbar>
      </q-footer>
    </q-layout>
  </div>
  <div v-else>
    <h1>No estás logueado</h1>
  </div>
</template>

<script>
import axios from "axios";
import router from "../router";
import MyDecks from "./MyDecks.vue";

export default {
  components: {
    MyDecks,
  },
  data() {
    return {
      cartas: [],
      barajaCartas: [],
      nombreBaraja: "",
      logueado: !!localStorage.getItem("user"),
      tab: "crearBaraja",
      imagenGrande: null,
      busqueda: "",
      filtroTipo: "",
    };
  },
  computed: {
    filteredCartas() {
      let cartas = this.cartas;

      if (this.filtroTipo) {
        cartas = cartas.filter((carta) => carta.type.includes(this.filtroTipo));
      }

      if (this.busqueda) {
        cartas = cartas.filter((carta) =>
          carta.name.toLowerCase().includes(this.busqueda.toLowerCase())
        );
      }

      return cartas;
    },
  },
  methods: {
    async obtenerCartas() {
      try {
        const response = await axios.get(
          "https://db.ygoprodeck.com/api/v7/cardinfo.php?format=Speed%20Duel"
        );
        this.cartas = response.data.data;
      } catch (error) {
        console.error("Error al obtener las cartas:", error);
      }
    },
    anadirCarta(carta) {
      this.barajaCartas.push(carta);
    },
    promptGuardarBaraja() {
      this.nombreBaraja = prompt("Introduce el nombre de la baraja:");
      if (this.nombreBaraja) {
        this.guardarBaraja();
      }
    },
    async guardarBaraja() {
      try {
        const deckData = {
          deck_name: this.nombreBaraja,
          cards: this.barajaCartas.map((carta) => ({ id: carta.id })),
          user_id: localStorage.getItem("user"),
        };
        const response = await axios.post(
          "http://localhost/cardsndecks/cardsndecks-project/src/back/guardar_baraja.php",
          deckData
        );
        this.barajaCartas = [];
        this.nombreBaraja = "";
      } catch (error) {
        console.error("Error al guardar la baraja:", error);
      }
    },
    irACrearBaraja() {
      this.tab = "crearBaraja";
      router.push("/cartas");
    },
    irAMisBarajas() {
      this.tab = "misBarajas";
      router.push("/my-decks");
    },
    cerrarSesion() {
      localStorage.removeItem("user");
      router.push("/login");
    },
    mostrarImagenGrande(imagenUrl) {
      this.imagenGrande = imagenUrl;
    },
    ocultarImagenGrande() {
      this.imagenGrande = null;
    },
  },
  created() {
    this.obtenerCartas();
  },
};
</script>

<style scoped>
body,
html {
  margin: 0;
  padding: 0;
  height: 100%;
  overflow: hidden;
}

.containerDB {
  display: flex;
  flex-direction: column;
  height: 100vh;
  background-color: white;
}

.navbar {
  z-index: 1000;
}

.footer {
  z-index: 1000;
}

.q-page-container {
  display: flex;
  flex-direction: column;
  height: 100%;
  background-image: url("../img/bg.jpg");
  background-size: cover;
  background-position: center;
}

.cartas-container {
  display: flex;
  justify-content: space-between;
  gap: 20px;
  flex: 1;
  padding: 20px;
}

.cartas-disponibles,
.baraja-list {
  flex: 1;
  max-width: 45%;
  border: 2px solid black;
  border-radius: 10px;
  padding: 20px;
  background-color: white;
}

q-list {
  height: auto;
}

img {
  width: 50px;
  height: 75px;
}

.imagen-grande {
  position: fixed;
  top: 100px;
  left: 20px;
  z-index: 1000;
}

.imagen-grande img {
  width: 300px;
  height: 400px;
}

.btn-guardar {
  margin-top: 20px;
}

.add {
  width: 50px;
  height: 50px;
}
</style>
