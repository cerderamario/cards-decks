<template>
    <div class="container">
      <div class="menu">
        <img src="../img/icon.jpg">
        <h3>Cards&Decks</h3>
        <h4>Cards&Decks es una aplicación para la creación y modificación de mazos para el juego Speed Duel</h4>
        <h4>pon a prueba tu creatividad y forma una baraja invencible</h4>
        <div class="btns">
          <q-btn @click="$router.push('/register')" label="Registrarse" color="primary"/>
          <q-btn @click="$router.push('/login')" label="Iniciar Sesión" color="primary"/>
        </div>
      </div>
    </div>
  </template>

<style>
.container{
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background-image: url("../img/wpp.jpg");
}
.menu{
  border: 1px solid black;
  border-radius: 5%;
  margin: 70px;
  padding: 70px;
  background-color: white;
  text-align: center;
}

.btns{
  display: flex; 
  align-items: center; 
  justify-content: center; 
  gap: 20px;
}
img{
  width: 75px;
  height: 100px;
}
</style>
  