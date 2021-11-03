<template>
  <nav>
    <v-app-bar color="deep-purple accent-4">
      <v-app-bar-nav-icon
        v-if="logged"
        @click.stop="drawer = !drawer"
      ></v-app-bar-nav-icon>

      <v-toolbar-title>
        <a href="/giftbox/public">GIFTBOX </a>
      </v-toolbar-title>

      <v-spacer></v-spacer>
      <v-btn v-if="logged" @click="logout">
        <span>Log Out</span>
        <v-icon right>mdi-logout</v-icon>
      </v-btn>
      <div v-else>
        <v-btn @click="login">
          <span>Log In</span>
          <v-icon right>mdi-login</v-icon>
        </v-btn>
        <v-btn @click="register">
          <span>Register</span>
          <v-icon right>mdi-account-circle</v-icon>
        </v-btn>
      </div>
    </v-app-bar>

    <v-navigation-drawer v-model="drawer" absolute bottom temporary>
      <v-list nav dense v-for="link in links" v-bind:key="link.name">
        <v-list-item-group v-model="model" mandatory color="indigo">
          <router-link
            v-if="show(link.show)"
            :to="{ name: link.route }"
            exact-active-class="text-purple-600"
          >
            <v-list-item>
              <v-list-item-title>{{ link.name }}</v-list-item-title>
            </v-list-item>
          </router-link>
        </v-list-item-group>
      </v-list>
    </v-navigation-drawer>
  </nav>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  data() {
    return {
      drawer: false,
      model: 1,
      links: [
        {
          name: "Enrolled Campaigns",
          route: "enrolledCampaigns",
          show: "logged",
        },
        { name: "Campaigns List", route: "campaigns", show: "hr" },
        { name: "Create Campaign", route: "campaignsCreate", show: "hr" },
        { name: "Gift Items", route: "giftItems", show: "hr" },
        { name: "Create Gift Item", route: "giftItemsCreate", show: "hr" },
      ],
    };
  },
  computed: mapGetters(["logged", "hr"]),
  methods: {
    register() {
      axios.post("/giftbox/public/register").finally((err) => {
        window.location.href = "/giftbox/public/register";
      });
    },
    login() {
      axios.post("/giftbox/public/login").finally((err) => {
        window.location.href = "/giftbox/public/login";
      });
    },
    logout() {
      axios.post("/giftbox/public/logout").finally((err) => {
        this.$store.dispatch("logout");
        window.location.href = "/giftbox/public";
      });
    },
    show(guard) {
      if (guard === "hr") {
        return this.$store.getters.hr;
      }
      if (guard === "logged") {
        return this.$store.getters.logged;
      } else {
        return true;
      }
    },
  },
};
</script>
