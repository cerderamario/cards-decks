<template>
  <q-layout view="lHh Lpr lFf">
    <q-header class="bg-blue-grey-9 text-white">
      <q-toolbar>
        <q-btn flat @click="goTo('/cartas')">
          <q-icon name="home" />
        </q-btn>
        <q-toolbar-title>Mis Barajas</q-toolbar-title>
        <q-btn flat @click="crearBaraja">
          <q-icon name="add_circle" />
          <span>Crear Baraja</span>
        </q-btn>
        <q-btn flat @click="cerrarSesion">
          <q-icon name="exit_to_app" />
          <span>Cerrar Sesión</span>
        </q-btn>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="q-pa-md content-background">
        <div class="q-gutter-md row">
          <div class="col-12 col-md-8">
            <div class="row q-col-gutter-md">
              <q-card
                style="margin: 20px; text-align: center"
                :class="{
                  'col-12 col-md-8':
                    modificar &&
                    barajaEnEdicion &&
                    barajaEnEdicion.id === miDeck.id,
                  'col-md-4':
                    !modificar ||
                    (barajaEnEdicion && barajaEnEdicion.id !== miDeck.id),
                }"
                :style="
                  modificar &&
                  barajaEnEdicion &&
                  barajaEnEdicion.id === miDeck.id
                    ? 'max-width: 75%'
                    : ''
                "
                v-for="miDeck in misDecks"
                :key="miDeck.id"
              >
                <q-card-section>
                  <div class="text-h6">{{ miDeck.name }}</div>
                </q-card-section>
                <q-card-actions align="right">
                  <q-btn
                    label="Borrar"
                    color="negative"
                    @click="borrarBaraja(miDeck.id)"
                  />
                  <q-btn
                    label="Modificar"
                    color="primary"
                    @click="modificarBaraja(miDeck)"
                  />
                  <q-btn
                    v-if="
                      modificar &&
                      barajaEnEdicion &&
                      barajaEnEdicion.id === miDeck.id
                    "
                    label="Guardar"
                    color="positive"
                    @click="guardarCambios"
                  />
                </q-card-actions>
                <q-card-section
                  v-if="
                    modificar &&
                    barajaEnEdicion &&
                    barajaEnEdicion.id === miDeck.id
                  "
                >
                  <div
                    style="width: 600px; max-height: 400px; overflow-y: scroll"
                    v-if="barajaEnEdicion.cartas.length"
                    class="cartas-scroll"
                  >
                    <q-list
                      v-for="(carta, index) in barajaEnEdicion.cartas"
                      :key="index"
                    >
                      <q-item
                        clickable
                        v-ripple
                        @mouseover="
                          mostrarImagenGrande(carta.card_images[0].image_url)
                        "
                        @mouseleave="ocultarImagenGrande"
                      >
                        <q-item-section thumbnail>
                          <img
                            :src="carta.card_images[0].image_url"
                            :alt="carta.name"
                          />
                        </q-item-section>
                        <q-item-section>{{ carta.name }}</q-item-section>
                        <q-item-section>{{ carta.type }}</q-item-section>
                        <q-item-section>
                          <img
                            class="remove"
                            @click="eliminarCarta(index)"
                            src="../img/remove_icon.png"
                          />
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </div>
                </q-card-section>
              </q-card>
            </div>
          </div>
          <div
            style="
              max-height: 600px;
              border-radius: 7%;
              border: 2px solid black;
              margin-right: 15px;
            "
            class="col-12 col-md-4 cartas-disponibles"
            v-if="modificar"
          >
            <h4>Cartas Disponibles</h4>
            <q-input filled v-model="busqueda" placeholder="Buscar cartas..." />
            <q-option-group
              v-model="filtroTipo"
              :options="[
                { label: 'All', value: '', color: 'blue' },
                { label: 'Monsters', value: 'Monster', color: 'yellow' },
                { label: 'Spell Cards', value: 'Spell Card', color: 'green' },
                { label: 'Trap Cards', value: 'Trap Card', color: 'purple' },
              ]"
              inline
              bordered
              type="radio"
            />
            <div
              style="width: 500px; max-height: 300px; overflow-y: scroll"
              class="cartas-scroll"
            >
              <q-list v-for="carta in filteredCartas" :key="carta.id">
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
        <div v-if="imagenGrande" class="imagen-grande-container">
          <img :src="imagenGrande" class="imagen-grande" />
        </div>
      </q-page>
    </q-page-container>

    <q-footer class="footer" elevated>
      <q-toolbar>
        <q-toolbar-title>Mario Cerdera ©</q-toolbar-title>
      </q-toolbar>
    </q-footer>
  </q-layout>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      listado: [],
      misDecks: [],
      logueado: !!localStorage.getItem("user"),
      modificar: false,
      barajaEnEdicion: null,
      cartas: [],
      busqueda: "",
      filtroTipo: "",
      imagenGrande: null,
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
    goTo(ruta) {
      this.$router.push(ruta);
    },
    eliminarCarta(index) {
      this.barajaEnEdicion.cartas.splice(index, 1);
    },
    anadirCarta(carta) {
      this.barajaEnEdicion.cartas.push({ ...carta });
    },
    async obtenerBarajas() {
      try {
        const userId = localStorage.getItem("user");

        if (isNaN(userId)) {
          throw new Error(
            "El user_id es NaN, verifica que está almacenado correctamente en localStorage."
          );
        }

        const response = await axios.get(
          `http://localhost/cardsndecks/cardsndecks-project/src/back/obtener_barajas.php?user_id=${userId}`
        );
        this.misDecks = response.data;
      } catch (error) {
        console.error("Error al obtener las barajas:", error);
      }
    },
    async modificarBaraja(baraja) {
      this.modificar = true;
      this.barajaEnEdicion = { ...baraja, cartas: [...baraja.cartas] };
      await this.obtenerCartas();
    },
    async guardarCambios() {
      try {
        const response = await axios.post(
          "http://localhost/cardsndecks/cardsndecks-project/src/back/actualizar_baraja.php",
          {
            deck_id: this.barajaEnEdicion.id,
            cartas: this.barajaEnEdicion.cartas.map((carta) => ({
              card_id: carta.id,
            })),
          }
        );

        if (response.data.message === "Baraja actualizada correctamente") {
          this.obtenerBarajas();
          this.modificar = false;
          this.barajaEnEdicion = null;
        } else {
          console.error(
            "Error al actualizar la baraja:",
            response.data.message
          );
        }
      } catch (error) {
        console.error("Error al actualizar la baraja:", error);
      }
    },
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
    async borrarBaraja(deckId) {
      try {
        const response = await axios.post(
          "http://localhost/cardsndecks/cardsndecks-project/src/back/borrar_baraja.php",
          {
            deck_id: deckId,
          }
        );
        this.$router.go(0);
        if (response.data.message === "Baraja eliminada correctamente") {
          this.misDecks = this.misDecks.filter((deck) => deck.id !== deckId);
        } else {
          console.error("Error al borrar la baraja:", response.data.message);
        }
      } catch (error) {
        console.error("Error al borrar la baraja:", error);
      }
    },
    crearBaraja() {
      this.$router.push("/cartas");
    },
    cerrarSesion() {
      localStorage.removeItem("user");
      this.$router.push("/login");
    },
    mostrarImagenGrande(imageUrl) {
      this.imagenGrande = imageUrl;
    },
    ocultarImagenGrande() {
      this.imagenGrande = null;
    },
  },
  created() {
    this.obtenerBarajas();
  },
};
</script>

<style scoped>
.content-background {
  background-image: url("../img/bg.jpg");
  background-size: cover;
  background-position: center;
  min-height: calc(100vh - 100px);
}

.cartas-disponibles,
.miDeck,
q-list {
  background-color: white;
}

q-card {
  margin-bottom: 20px;
}

img {
  width: 50px;
  height: 75px;
}

.imagen-grande-container {
  position: fixed;
  top: 50px;
  right: 20px;
  z-index: 1000;
}

.imagen-grande {
  width: 300px;
  height: auto;
}

.cartas-disponibles {
  position: fixed;
  top: 50px;
  right: 0;
  width: 30%;
  max-height: calc(100vh - 80px);
  padding: 20px;
  padding-right: 20px;
  background: white;
  border-left: 1px solid #e0e0e0;
}

.cartas-scroll {
  max-height: calc(100vh - 100px);
}

.add {
  width: 50px;
  height: 50px;
}

.remove {
  width: 50px;
  height: 50px;
}

.row.q-col-gutter-md {
  gap: 1rem;
}

q-card.col-12.col-md-8 {
  max-width: 60%;
}

q-card.col-md-4 {
  width: 100%;
}
</style>
