<template>
    <v-app-bar app>
        <v-app-bar-title>{{ appName }}</v-app-bar-title>

        <v-spacer></v-spacer>

        <!-- Left side of navbar for authenticated users -->
        <v-row v-if="isAuthenticated" align="center" class="mr-4">
            <template v-for="(button, index) in navButtons" :key="index">
                <v-btn v-if="hasAbility(button.ability)" :to="button.to" link>{{ $t(button.label) }}</v-btn>
            </template>
            <!-- Card Menu -->
            <v-btn v-if="hasAbility($enums.UserAbilityEnum.CARD_OWNERSHIP)" color="primary">
                {{ $t('card.value') }}
                <v-menu activator="parent">

                    <v-list>
                        <v-list-item :to="{ name: 'card.History' }" link>
                            <v-list-item-title>{{ $t('history') }}</v-list-item-title>
                        </v-list-item>
                        <v-list-item :to="{ name: 'card.application' }" link>
                            <v-list-item-title>{{ $t('application') }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-btn>
            <!-- Coupon Menu -->
            <v-btn v-if="hasAbility($enums.UserAbilityEnum.COUPON_OWNERSHIP)" color="primary">
                {{ $t('coupon.value', 2) }}
                <v-menu activator="parent" offset-y>
                    <v-list>
                        <v-list-item :to="{ name: 'coupons.History' }" link>
                            <v-list-item-title>{{ $t('history') }}</v-list-item-title>
                        </v-list-item>
                        <v-list-item :to="{ name: 'coupons.transfer' }" link>
                            <v-list-item-title>{{ $t('transfer.value') }}</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </v-btn>
        </v-row>


        <!-- Right side of navbar for authentication links -->

        <v-row align="center" class="mr-4">
            <v-btn color="primary" @click.stop="drawer = true">
                <v-icon icon="mdi-cog"></v-icon>
                <span>{{ $t('settings') }}</span>
            </v-btn>
            <template v-if="isAuthenticated">
                <v-btn :to="{ name: 'userProfile' }" color="primary" link>
                    {{ currentUser.name }}
                    <v-icon icon="mdi-account-circle"></v-icon>
                </v-btn>
                <v-btn v-if="isAuthenticated" color="primary" link @click="logout">
                    {{ $t('logout') }}
                    <v-icon icon="mdi-logout"></v-icon>
                </v-btn>
            </template>
            <v-btn v-else :to="{ name: 'login' }" color="primary" link>
                {{ $t('login') }}
                <v-icon icon="mdi-login"></v-icon>
            </v-btn>

        </v-row>

    </v-app-bar>
    <settings v-model="drawer" right temporary/>
</template>

<script>
import {mapActions, mapGetters} from "vuex";
import Settings from "./navView/settings.vue";

export default {
    name: "NavView",
    components: {Settings},
    data() {
        return {drawer: false};
    },
    computed: {
        ...mapGetters('auth', [
            'isAuthenticated',
            'currentUser',
            'hasAbility',
        ]),
        appName() {
            return this.$i18n.t('company.appName'); // Using translation for app name
        },
        navButtons() {
            return [
                {label: 'purchase.value', to: {name: 'purchase'}, ability: this.$enums.UserAbilityEnum.COUPON_SELL},
                {label: 'entry_check', to: {name: 'entryChecking'}, ability: this.$enums.UserAbilityEnum.ENTRY_CHECK},
                {
                    label: 'card.application.submitted',
                    to: {name: 'cardApplication.checking', params: {category: 'submitted'}},
                    ability: this.$enums.UserAbilityEnum.CARD_APPLICATION_CHECK
                },
            ];
        }
    },
    methods: {
        // Mapping actions from Vuex store
        ...mapActions('auth', [
            'logout',
        ]),
    },
    watch: {
        '$route.name'(newValue) {
            document.title = newValue ? `${newValue} | ${this.appName}` : this.appName;
        }
    }
}
</script>

