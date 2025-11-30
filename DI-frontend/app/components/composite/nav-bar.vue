<script setup lang="ts">
import type { User } from '~/types/auth';
import { type NavDropdown } from '~/types/nav';
const { t } = useI18n();

const props = defineProps<{
  dropdowns: NavDropdown[];
}>();

const showMobileMenu = ref<boolean>(false);
const showDropdowns = ref<boolean>(false);
const allowTransition = ref<boolean>(false);
function showDropdown(index:number, disableTransition: boolean) {
    allowTransition.value = !disableTransition;
    props.dropdowns[index].show = true;
    showDropdowns.value = true;
}

function hideDropdown(index:number) {
    props.dropdowns[index].show = false;
    showDropdowns.value = false;
}

const userStore = useUserStore();

// async function logout() {
//   const user = useState('user');
//   const response = await useCsrfFetch('/api/auth/logout', {method: 'POST'});
//   if (response.status.value === 'success') {
//     return navigateTo('/login', {external: true});
//   }
// }
</script>

<template>
<ClientOnly>
    <nav>
        <div class="root">
            <div class="left">
                <Logo orientation="h" :size="1.5"/>
                <div class="nav-btns">
                    <div class="nav-btn" v-for="(dropdown, index) in dropdowns" :key="index" @mouseover="showDropdown(index, false)" @touch="showDropdown(index, false)" @mouseleave="hideDropdown(index)">
                        <a :title="dropdown.text" :class="{active : dropdown.show}">{{ dropdown.text }}<DirectionIcon direction="down" v-if="dropdown.menu.length > 0"/></a>
                        <Teleport to="#nav-bar-dropdown-menus" defer>
                            <Transition :name="(allowTransition) ? 'dropdown' : ''">       
                                <div class="nav-dropdown" v-if="(dropdown.menu.length > 0) && dropdown.show" @mouseover="showDropdown(index, true)" @touch="showDropdown(index, true)" @mouseleave="hideDropdown(index)">
                                    <ul class="dropdown-list">
                                        <li v-for="item in dropdown.menu">
                                            <NuxtLink class="dropdown-item" :to="item.url">
                                                <span>{{ item.title }}</span>
                                                <p>{{ item.text }}</p>
                                            </NuxtLink>
                                        </li>
                                    </ul>
                                </div>
                            </Transition>
                        </Teleport>
                    </div>
                </div>
            </div>
            <div class="right">
                <ThemeSwitcher/>
                <LocaleSwitcher/>
                <div class="nav-btns">
                  <Button variant="primary" @click="userStore.logout"><LogoutIcon/>{{ t('logout') }}</Button>
                  <Button variant="secondary" v-if="useState<User>('user').value?.role === 'ADMIN'" to="/immobilien/create"><AddIcon/>{{ t('create') }}</Button>
                  <Button variant="secondary" v-else><UserIcon/>{{ t('profile') }}</Button>
                </div>
                <Button variant="tertiary" class="menu-btn" @click="showMobileMenu = !showMobileMenu" style="padding: 25px 5px" :size="1.3">
                    <Transition name="bounce" mode="out-in">
                        <HamburgerMenuIcon v-if="!showMobileMenu"/>
                        <CloseIcon v-else/>
                    </Transition>
                </Button>
            </div>
        </div>

        <div id="nav-bar-dropdown-menus" style="box-sizing: border-box;" v-show="showDropdowns"></div>
        <Transition name="hamburger-dropdown">
          <div class="mobile-menu" v-show="showMobileMenu">
            <div class="mobile-nav-btns">
              <div class="nav-btn" v-for="(dropdown, index) in dropdowns" :key="index" @mouseover="showDropdown(index, false)" @touch="showDropdown(index, false)" @click="showDropdown(index, false)" @mouseleave="hideDropdown(index)">
                  <a :title="dropdown.text">{{ dropdown.text }}<DirectionIcon direction="down" v-if="dropdown.menu.length > 0"/></a>
                    <Transition :name="(allowTransition) ? 'dropdown' : ''">       
                        <div class="nav-dropdown" v-if="(dropdown.menu.length > 0) && dropdown.show" @mouseover="showDropdown(index, true)" @click="showDropdown(index, true)" @touch="showDropdown(index, true)" @mouseleave="hideDropdown(index)">
                            <ul class="dropdown-list">
                                <li v-for="item in dropdown.menu">
                                    <a class="dropdown-item" :href="item.url">
                                        <span>{{ item.title }}</span>
                                        <p>{{ item.text }}</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </Transition>
              </div>
            </div>
            <div class="bottom-btns">
              <Button variant="primary" @click="userStore.logout"><LogoutIcon/>{{ t('logout') }}</Button>
              <Button variant="secondary"><UserIcon/>{{ t('profile') }}</Button>       
            </div>
          </div>
        </Transition>
    </nav>
</ClientOnly>
</template>

<style lang="css" scoped>
nav {
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
}

.mobile-menu > .bottom-btns {
  padding: 15px;
  display: flex;
  flex-flow: row nowrap;
  justify-content: space-around;
}

.mobile-nav-btns .dropdown-item {
  padding: 10px;
  gap: 5px;
}

.mobile-nav-btns .dropdown-list {
  display: flex;
  flex-flow: column nowrap;
  box-shadow: unset;
  padding: 0;
}

.mobile-nav-btns .nav-dropdown {
  margin: unset;
  max-width: 100%;
  padding-top: 5px;
}

.mobile-nav-btns > .nav-btn > a {
  justify-content: space-between;
}

.mobile-nav-btns > .nav-btn {
  align-items: stretch;
  padding: 15px;
}

.mobile-nav-btns {
  display: flex;
  flex-flow: column nowrap;
}

.mobile-menu {
  box-sizing: border-box;
  min-height: calc(100vh - 80px);
  display: none;
  background-color: var(--contrast-5-percent);
}

.nav-btn>a>:deep(svg) {
  width: calc(var(--font-size) * 0.7);
  transition: transform 0.3s ease;
}

.dropdown-item>span {
  font-weight: 620;
}

.dropdown-item:active {
  color: var(--pink);
}

.dropdown-item:hover, .dropdown-item:active {
  background-color: var(--pink-5-percent);

  &>span {
    color: var(--pink);
  }
}

.dropdown-item {
  display: flex;
  flex-flow: column nowrap;
  align-items: flex-start;
  user-select: none;
  cursor: pointer;
  justify-content: flex-start;
  background-color: var(--contrast-normal);
  border-radius: 10px;
  padding: 25px;
  height: 100%;
  gap: 15px;
  box-sizing: border-box;
}

.dropdown-list {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 10px;
  border-radius: 10px;
  padding: 10px;
  background-color: var(--contrast-5-percent);
  box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
}

.nav-dropdown {
  max-width: 90%;
  margin: auto;
  padding-top: 15px;
}

.nav-btn {
  padding: 5px;
  display: flex;
  flex-flow: column nowrap;
  align-items: center;
  justify-content: center;
  height: 100%;
  box-sizing: border-box;
}

.nav-btn>a.active {
  color: var(--pink);

  &>:deep(svg) {
    transform: rotate(180deg);
  }
}

.nav-btn > a {
  display: flex;
  flex-flow: row nowrap;
  align-items: baseline;
  gap: 10px;
}

.root {
  display: flex;
  flex-flow: row nowrap;
  align-items: center;
  justify-content: space-between;
  box-sizing: border-box;
  padding: 0 10px;
  background-color: var(--contrast-5-percent);
  box-shadow: 0px 5px 19px -7px rgba(0,0,0,1);
  height: 80px;
}

.right {
  display: flex;
  flex-flow: row nowrap;
  gap: 5px;
  align-items: center;
  height: 100%;
}

.right > .menu-btn {
  display: none;
}

.left {
  display: flex;
  flex-flow: row nowrap;
  align-items: stretch;
  gap: 10px;
  height: 100%;
}

.nav-btns {
  display: flex;
  flex-flow: row nowrap;
  align-items: stretch;
  gap: 10px;
}

.bounce-enter-active {
  animation: bounce-in 0.2s;
}

.bounce-leave-active {
  animation: bounce-in 0.2s reverse;
}

@keyframes bounce-in {
  0% {
    transform: scale(0);
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
  }
}

.dropdown-enter-active {
  transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
  transform-origin: top center;
}

.dropdown-enter-from {
  opacity: 0;
  transform: scaleY(0) translateY(-10px);
}

.dropdown-enter-to {
  opacity: 1;
  transform: scaleY(1) translateY(0);
}

@media screen and (max-width: 1075px) {
  .right > .menu-btn {
    display: block;
  }

  .left > .nav-btns {
    display: none;
  }

  .mobile-menu {
    display: flex;
    flex-flow: column nowrap;
    justify-content: space-between;
  }

  #nav-bar-dropdown-menus {
    display: none;
  }
}

@media screen and (max-width: 640px) {
  .nav-btns {
    display: none;
  }
}

.hamburger-dropdown-enter-active,
.hamburger-dropdown-leave-active {
  transition: opacity 0.5s ease, transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);
  transform-origin: top center;
}

.hamburger-dropdown-enter-from,
.hamburger-dropdown-leave-to {
  opacity: 0;
  transform: scaleY(0) translateY(-10px);
}

.hamburger-dropdown-enter-to,
.hamburger-dropdown-leave-from {
  opacity: 1;
  transform: scaleY(1) translateY(0);
}
</style>

<i18n lang="json">
{
  "en": {
    "logout" : "Sign out",
    "profile" : "To Profile",
    "create": "Create"
  },

  "de": {
    "logout" : "Abmelden",
    "profile" : "Zum Profil",
    "create": "Erstellen"
  }
}
</i18n>