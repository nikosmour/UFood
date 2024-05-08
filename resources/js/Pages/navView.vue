<template>
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
        <div class="container">
            <router-link class="nav-link router-link-exact-active" to=" ">
                {{ appName }}
            </router-link>
            <button aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="'Toggle navigation'"
                    class="navbar-toggler" data-bs-target="#navbarSupportedContent"
                    data-bs-toggle="collapse" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div id="navbarSupportedContent" class="collapse navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul v-if="isAuthenticated" class="navbar-nav me-auto">
                    <li v-if="hasAbility($enums.UserAbilityEnum.COUPON_SELL)" class="nav-item">
                        <router-link :to="{name:'purchase'}" class="nav-link router-link-exact-active"> Purchase
                        </router-link>
                    </li>
                    <li v-if="hasAbility($enums.UserAbilityEnum.ENTRY_CHECK)" class="nav-item">
                        <router-link :to="{name:'entryChecking'}" class="nav-link router-link-exact-active">
                            EntryChecking
                        </router-link>
                    </li>
                    <template v-if="hasAbility($enums.UserAbilityEnum.CARD_APPLICATION_CHECK)">
                        <li v-for="category in Object.keys($enums.CardStatusEnum)" class="nav-item">
                            <router-link
                                :to="{name:'cardApplication.checking',params:{category:category.toLowerCase()}}"
                                class="nav-link router-link-exact-active">
                                {{ category.toLowerCase() }}
                            </router-link>
                        </li>
                    </template>
                    <li v-if="hasAbility($enums.UserAbilityEnum.CARD_OWNERSHIP)" class="nav-item dropdown">
                        <a v-pre id="navbarDropdown"
                           aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle " data-bs-toggle="dropdown"
                           href="#" role="button">
                            student.nav.Free Food <span class="caret"></span>
                        </a>
                        <div aria-labelledby="navbarDropdown" class="dropdown-menu dropdown-menu-left">
                            <router-link :to="{name:'card.History'}"
                                         class="nav-link router-link-exact-active dropdown-item">
                                'student.card.History'
                            </router-link>
                            <router-link :to="{name:'card.application'}"
                                         class="nav-link router-link-exact-active dropdown-item">
                                'student.card.Request'
                            </router-link>
                        </div>
                    </li>

                    <li v-if="hasAbility($enums.UserAbilityEnum.COUPON_OWNERSHIP)"
                        class="nav-item dropdown">
                        <a v-pre id="navbarDropdown"
                           aria-expanded="false" aria-haspopup="true" class="nav-link dropdown-toggle " data-bs-toggle="dropdown"
                           href="#" role="button">
                            student.nav.Coupons <span class="caret"></span>
                        </a>
                        <div aria-labelledby="navbarDropdown" class="dropdown-menu dropdown-menu-left">
                            <router-link :to="{name:'coupons.History'}"
                                         class="nav-link router-link-exact-active dropdown-item">
                                coupons.History
                            </router-link>
                            <router-link :to="{name:'coupons.transfer'}"
                                         class="nav-link router-link-exact-active dropdown-item">
                                coupons.transfer
                            </router-link>
                        </div>
                    </li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Authentication Links -->
                    <!--                    @guest
                                        @if (Route::has('login'))-->
                    <li v-if="!isAuthenticated" class="nav-item">
                        <router-link :to="{name:'login'}" class="nav-link router-link-exact-active"> Login</router-link>
                    </li>
                    <!--                    @endif

                                        @if (Route::has('register'))-->
                    <!--                    <li class="nav-item">
                                            <router-link class="nav-link router-link-exact-active" to="{name:'Register'}"> Register </router-link>
                                        </li>-->
                    <!--                    @endif-->
                    <!--                    @else -->
                    <li v-else class="nav-item dropdown">
                        <a id="navbarDropdown" aria-expanded="false" aria-haspopup="true"
                           class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#" role="button">
                            {{ currentUser.name }} <span class="caret"></span>
                        </a>

                        <div aria-labelledby="navbarDropdown" class="dropdown-menu dropdown-menu-end">
                            <router-link :to="{name:'userProfile'}" class="nav-link router-link-exact-active"> My Info
                            </router-link>
                            <button class="nav-link "
                                    v-on:click="logout()">
                                LogOut
                            </button>
                        </div>
                    </li>
                    <!--                    @endguest-->
                </ul>
            </div>
        </div>
    </nav>

</template>
<script>
import {mapActions, mapGetters} from "vuex";

export default {
    computed: {
        /*...mapState({
            isAuthenticated: state => state.auth.isLoggedIn,
            currentUser: state => state.auth.user,
        }),*/
        ...mapGetters([
            'isAuthenticated',
            'currentUser',
            'hasAbility',
        ]),
        routeTitle() {
            return this.$route.name ? `${this.$route.name} | ${this.appName}` : this.appName;
        },
        appName() {
            return process.env.MIX_APP_NAME;
        }
    },
    methods: {
        ...mapActions({
            // loginUser: 'loginUser',
            logout: 'logout',
        }),
    },
    watch: {
        routeTitle(newValue) {
            document.title = newValue;
        }
    }
}
</script>

<style scoped>

</style>
