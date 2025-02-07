import { ref, computed, watchEffect } from "vue";
import { defineStore } from "pinia";

export const AuthStore = defineStore("auth", () => {
  const token = ref(localStorage.getItem("token") ?? null);
  const isauth = ref(localStorage.getItem("token") && localStorage.getItem("user"));
  const user = ref(JSON.parse(localStorage.getItem("user")) ?? null);
  const isAdmin = ref(Number(localStorage.getItem("isAdmin")) ?? null);

  const activeView = ref(localStorage.getItem("activeView") ?? "ProfileFormateur");
  const statusMessage = ref("");
  const statusBienvenue = ref("");
  const ActiveViewAdmin = ref(localStorage.getItem("ActiveViewAdmin") ?? "statistics");
  
  let logoutTimer = null; 

  const getToken = computed(() => token.value);
  const getisauth = computed(() => isauth.value);
  const getuser = computed(() => user.value);
  const getisadmin = computed(() => isAdmin.value);
  const getActiveView = computed(() => activeView.value); 
  const getStatusMessage = computed(() => statusMessage.value);
  const getStatusBienvenue = computed(() => statusBienvenue.value);
  const getActiveViewAdmin = computed(() => ActiveViewAdmin.value);

  function logout() {
    token.value = null;
    isauth.value = false;
    isAdmin.value = null;
    user.value = null;
    activeView.value = "ProfileFormateur";

    localStorage.removeItem("token");
    localStorage.removeItem("isauth");
    localStorage.removeItem("isAdmin");
    localStorage.removeItem("user");
    localStorage.removeItem("activeView");

    if (logoutTimer) {
      clearTimeout(logoutTimer);
      logoutTimer = null;
    }
  }

  function startLogoutTimer() {
    if (logoutTimer) clearTimeout(logoutTimer);
    logoutTimer = setTimeout(logout, 3600000);
  }

  function login(t, u, admin_client,status) {
    token.value = t;
    isauth.value = true;
    isAdmin.value = admin_client;
    user.value = u;
    statusBienvenue.value=status;

    localStorage.setItem("token", t);
    localStorage.setItem("isauth", true);
    localStorage.setItem("isAdmin", admin_client);
    localStorage.setItem("user", JSON.stringify(u));
    localStorage.setItem('statusBienvenue',status);

       setTimeout(() => {
        localStorage.removeItem("statusBienvenue");
        statusBienvenue.value = null;
    }, 20000);

    startLogoutTimer(); 
  }

  function updateUser(name, lastname, email, image) {
    const currentUser = JSON.parse(localStorage.getItem("user")) || {};

    const imageUrl = image ? `/${image}` : currentUser.image;

    const updatedUser = { ...currentUser, name, lastname, email, image: imageUrl };

    console.log("Utilisateur mis à jour dans le store :", updatedUser);

    localStorage.setItem("user", JSON.stringify(updatedUser));
    user.value = updatedUser;
}

  function setActiveViewAdmin(viewName) {
    ActiveViewAdmin.value = viewName;
    localStorage.setItem("ActiveViewAdmin", viewName);
  }
  function setActiveView(viewName) {
    activeView.value = viewName;
    localStorage.setItem("activeView", viewName);
    startLogoutTimer();
  }

  function setStatusMessage(message) {
    statusMessage.value = message;
  }

  function clearStatusMessage() {
    statusMessage.value = "";
  }

  watchEffect(() => {
    if (token.value) {
      startLogoutTimer();
    }
  });

  return {
    login,
    logout,
    updateUser,
    setActiveView,
    setStatusMessage,
    clearStatusMessage,
    setActiveViewAdmin,
    token,
    user,
    isauth,
    isAdmin,
    activeView,
    statusMessage,
    ActiveViewAdmin,
    getToken,
    getisadmin,
    getisauth,
    getuser,
    getActiveView,
    getStatusMessage,
    getStatusBienvenue,
    getActiveViewAdmin
  };
});
