<template>
  <v-app>
    <v-simple-table :class="{ loading: loading }">
      <template v-slot:default>
        <thead>
          <tr>
            <th class="px-4 py-2 whitespace-nowrap">Name</th>
            <th class="px-4 py-2 whitespace-nowrap">Status</th>
            <th class="px-4 py-2 whitespace-nowrap">Dispatch Date</th>
            <th class="px-4 py-2 whitespace-nowrap">Delivery Date</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="giftCampaign in giftCampaigns" :key="giftCampaign.id">
            <td>{{ giftCampaign.name }}</td>
            <td>{{ giftCampaign.campaign_status.status }}</td>
            <td>{{ giftCampaign.dispatch_date }}</td>
            <td>{{ giftCampaign.delivery_date }}</td>
            <td>
              <v-btn
                @click="unsubscribe(giftCampaign.id)"
                v-if="subscribed(user.id, giftCampaign.subscribers)"
                text
                color="error"
              >
                Unsubscribe
              </v-btn>
              <v-btn
                v-else
                @click="subscribe(giftCampaign)"
                text
                color="primary"
              >
                Subscribe
              </v-btn>
            </td>
          </tr>
        </tbody>
      </template>
    </v-simple-table>
    <v-snackbar v-model="snackbar" timeout="2000">
      You must login to subscribe to campaigns!

      <template v-slot:action="{ attrs }">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
    <v-snackbar v-model="snackbar1" timeout="2000"
      >Unable to subscribe now.
      <template v-slot:action="{ attrs }">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar1 = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </v-app>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  data: function () {
    return {
      giftCampaign: "",
      giftCampaigns: [],
      loading: true,
      snackbar: false,
      snackbar1: false,
    };
  },
  mounted() {
    this.$store.commit("setAuthUser", window.auth_user);
    this.loadGiftCampaigns();
  },
  computed: mapGetters(["user"]),
  methods: {
    loadGiftCampaigns: function () {
      axios
        .get("/giftbox/public/api/home")
        .then((response) => {
          this.giftCampaigns = response.data.data;
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    subscribe(giftCampaign) {
      if (!this.$store.getters.isLoggedIn) {
        this.snackbar = true;
      } else {
        axios
          .post("/giftbox/public/api/subscribers", giftCampaign)
          .then((respone) => {
            this.loadGiftCampaigns();
          })
          .catch((error) => {
            if (error.response.status == 422) {
              this.snackbar1 = true;
            }
          });
      }
    },
    unsubscribe(giftCampaignId) {
      axios
        .delete(`/giftbox/public/api/subscribers/${giftCampaignId}`)
        .then((respone) => {
          this.loadGiftCampaigns();
        })
        .catch((error) => {
          if (error.response.status == 422) {
            this.errors = error.response.data.errors;
          }
        });
    },
    subscribed(userId, subscribers) {
      let flag = null;
      subscribers.forEach((subscriber) => {
        if (userId == subscriber.id) {
          flag = 1;
        }
      });
      if (flag === 1) {
        return true;
      } else if (typeof flag === "undefined") {
        return false;
      }
    },
  },
};
</script>
